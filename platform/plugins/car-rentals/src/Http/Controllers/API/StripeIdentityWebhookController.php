<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Models\CustomerKycVerification;
use Botble\CarRentals\Services\Kyc\KycVerificationService;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;

class StripeIdentityWebhookController extends BaseApiController
{
    public function __construct(protected KycVerificationService $kycVerificationService)
    {
    }

    public function __invoke(Request $request): Response
    {   
        $secret = (string) CarRentalsHelper::getSetting('kyc_stripe_webhook_secret', '');
        if ($secret === '') {
            return response()->json(['message' => 'Webhook secret not configured.'], 503);
        }

        $payload = $request->getContent();
        $sigHeader = (string) $request->header('Stripe-Signature', '');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException|\UnexpectedValueException $e) {
            return response()->json(['message' => 'Invalid Stripe signature.'], 401);
        }

        $type = (string) $event->type;

        if (! str_starts_with($type, 'identity.verification_session.')) {
            return response()->json(['received' => true, 'ignored' => true]);
        }

        /** @var \Stripe\Identity\VerificationSession $session */
        $session = $event->data->object;
        $sessionId = (string) $session->id;
        $status = (string) $session->status;

        $metaVid = isset($session->metadata['cr_verification_id'])
            ? (string) $session->metadata['cr_verification_id']
            : '';

        $verification = CustomerKycVerification::query()
            ->where('provider_reference', $sessionId)
            ->first();

        if (! $verification && $metaVid !== '') {
            $verification = CustomerKycVerification::query()->find((int) $metaVid);
        }

        if (! $verification) {
            return response()->json(['message' => 'Verification not found.'], 202);
        }

        if ((string) data_get($verification->provider_payload, 'last_event_id', '') === (string) $event->id) {
            return response()->json(['received' => true, 'deduplicated' => true]);
        }

        $lastEventCreatedAt = (int) data_get($verification->provider_payload, 'last_event_created_at', 0);
        $currentEventCreatedAt = (int) ($event->created ?? 0);
        if ($lastEventCreatedAt > 0 && $currentEventCreatedAt > 0 && $currentEventCreatedAt < $lastEventCreatedAt) {
            return response()->json(['received' => true, 'ignored' => 'out_of_order']);
        }

        if ($status === 'processing') {
            return response()->json(['received' => true, 'status' => 'processing']);
        }

        $normalizedPayload = $this->buildMinimalStripePayload($session, (string) $event->id, $type, (int) ($event->created ?? 0));
        $decisionStatus = $this->mapDecisionStatus($status, $normalizedPayload);

        $this->kycVerificationService->applyWebhookDecision(
            $verification,
            $decisionStatus,
            $normalizedPayload,
            (string) $event->id
        );

        return response()->json(['received' => true]);
    }

    protected function buildMinimalStripePayload(\Stripe\Identity\VerificationSession $session, string $eventId, string $eventType, int $eventCreatedAt = 0): array
    {
        $report = $this->retrieveVerificationReport($session);
        $ocrPayload = $this->extractOcrPayloadFromReport($report);

        $documentErrorCode = (string) data_get($report, 'document.error.code', '');
        $selfieErrorCode = (string) data_get($report, 'selfie.error.code', '');
        $reason = (string) (data_get($report, 'selfie.error.reason')
            ?: data_get($report, 'document.error.reason')
            ?: data_get($session->toArray(), 'last_error.reason')
            ?: data_get($session->toArray(), 'last_error.code')
            ?: ($session->status === 'canceled' ? 'Verification canceled.' : 'Verification failed.'));

        return array_filter([
            'id' => (string) $session->id,
            'status' => (string) $session->status,
            'event_id' => $eventId,
            'event_created_at' => $eventCreatedAt > 0 ? $eventCreatedAt : (int) ($session->created ?? 0),
            'event_type' => $eventType,
            'reason' => in_array((string) $session->status, ['requires_input', 'canceled'], true) ? $reason : null,
            'stripe_identity' => [
                'session_id' => (string) $session->id,
                'status' => (string) $session->status,
                'report' => [
                    'document' => array_filter([
                        'status' => (string) data_get($report, 'document.status', ''),
                        'error_code' => $documentErrorCode !== '' ? $documentErrorCode : null,
                        'error_reason' => (string) data_get($report, 'document.error.reason', ''),
                    ], fn ($value) => $value !== null && $value !== ''),
                    'selfie' => array_filter([
                        'status' => (string) data_get($report, 'selfie.status', ''),
                        'error_code' => $selfieErrorCode !== '' ? $selfieErrorCode : null,
                        'error_reason' => (string) data_get($report, 'selfie.error.reason', ''),
                    ], fn ($value) => $value !== null && $value !== ''),
                ],
            ],
            'ocr_payload' => $ocrPayload,
            'decision_reasons' => $this->kycVerificationService->mapStripeVerificationReasons([
                'status' => (string) $session->status,
                'ocr_payload' => $ocrPayload,
                'stripe_identity' => [
                    'status' => (string) $session->status,
                    'report' => [
                        'document' => ['error_code' => $documentErrorCode],
                        'selfie' => ['error_code' => $selfieErrorCode],
                    ],
                ],
            ]),
        ]);
    }

    protected function extractOcrPayloadFromReport(array $report): array
    {
        $firstName = $this->firstNonEmpty($report, [
            'document.first_name',
            'verified_outputs.first_name',
        ]);
        $lastName = $this->firstNonEmpty($report, [
            'document.last_name',
            'verified_outputs.last_name',
        ]);
        $fullName = trim($firstName . ' ' . $lastName);
        if ($fullName === '') {
            $fullName = $this->firstNonEmpty($report, [
                'document.name',
                'verified_outputs.name',
            ]);
        }

        $extractedFields = array_filter([
            'license_number' => $this->firstNonEmpty($report, [
                'document.document_number',
                'document.id_number',
                'document.number',
                'verified_outputs.id_number',
                'verified_outputs.document_number',
            ]),
            'full_name' => $fullName,
            'date_of_birth' => $this->firstNonEmpty($report, [
                'document.dob',
                'document.date_of_birth',
                'verified_outputs.dob',
                'verified_outputs.date_of_birth',
            ]),
            'expiration_date' => $this->firstNonEmpty($report, [
                'document.expiration_date',
                'document.expiry_date',
                'document.expires_at',
                'verified_outputs.expiration_date',
                'verified_outputs.expiry_date',
            ]),
            'issuing_country' => $this->firstNonEmpty($report, [
                'document.issuing_country',
                'document.issuing_country_code',
                'verified_outputs.issuing_country',
                'verified_outputs.issuing_country_code',
            ]),
        ], fn ($value) => is_string($value) && trim($value) !== '');

        if (empty($extractedFields)) {
            return [];
        }

        return [
            'source' => 'stripe',
            'confidence_score' => (float) data_get($report, 'document.quality_score', 1.0),
            'extracted_fields' => $extractedFields,
        ];
    }

    protected function firstNonEmpty(array $source, array $paths): string
    {
        foreach ($paths as $path) {
            $value = data_get($source, $path);
            if (is_scalar($value)) {
                $text = trim((string) $value);
                if ($text !== '') {
                    return $text;
                }
            }
        }

        return '';
    }

    protected function mapDecisionStatus(string $sessionStatus, array $normalizedPayload): string
    {
        $nonBiometricFallback = (bool) CarRentalsHelper::getSetting('kyc_allow_non_biometric_fallback', false);
        $selfieErrorCode = (string) data_get($normalizedPayload, 'stripe_identity.report.selfie.error_code', '');
        if ($sessionStatus === 'requires_input'
            && $nonBiometricFallback
            && str_starts_with($selfieErrorCode, 'selfie_')) {
            return 'manual_review';
        }

        if ($sessionStatus === 'verified') {
            $documentStatus = (string) data_get($normalizedPayload, 'stripe_identity.report.document.status', 'verified');
            $selfieStatus = (string) data_get($normalizedPayload, 'stripe_identity.report.selfie.status', 'verified');

            if (in_array($documentStatus, ['unverified', 'requires_input'], true) || in_array($selfieStatus, ['unverified', 'requires_input'], true)) {
                return 'manual_review';
            }

            return 'verified';
        }

        return match ($sessionStatus) {
            'requires_input', 'canceled' => 'rejected',
            default => 'manual_review',
        };
    }

    protected function retrieveVerificationReport(\Stripe\Identity\VerificationSession $session): array
    {
        $secret = (string) CarRentalsHelper::getSetting('kyc_stripe_secret_key', '');

        if ($secret === '') {
            return [];
        }

        try {
            $stripe = new StripeClient($secret);
            $retrieved = $stripe->identity->verificationSessions->retrieve((string) $session->id, [
                'expand' => ['last_verification_report'],
            ]);

            return json_decode(json_encode(data_get($retrieved->toArray(), 'last_verification_report', [])), true) ?? [];
        } catch (\Throwable) {
            return [];
        }
    }
}
