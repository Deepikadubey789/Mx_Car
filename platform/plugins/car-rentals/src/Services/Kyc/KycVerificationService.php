<?php

namespace Botble\CarRentals\Services\Kyc;

use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Models\CustomerKycDocument;
use Botble\CarRentals\Models\CustomerKycVerification;
use Botble\Media\Facades\RvMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Stripe\StripeClient;

class KycVerificationService
{
    public const REQUIRED_DOCUMENT_TYPES = ['license_front', 'license_back', 'selfie'];

    public function __construct(
        protected KycProviderInterface $provider,
        protected KycDecisionService $decisionService
    ) {
    }

    public function start(Customer $customer): CustomerKycVerification
    {
        return DB::transaction(function () use ($customer): CustomerKycVerification {
            $verification = CustomerKycVerification::query()->create([
                'customer_id' => $customer->id,
                'status' => 'draft',
                'provider' => (string) CarRentalsHelper::getSetting('kyc_provider', 'stripe'),
            ]);

            $customer->forceFill([
                'kyc_status' => 'pending',
                'kyc_current_verification_id' => $verification->id,
            ])->save();

            return $verification;
        });
    }

    public function uploadDocument(CustomerKycVerification $verification, UploadedFile $file, string $documentType): CustomerKycDocument
    {
        $result = RvMedia::handleUpload($file, 0, 'kyc/' . $verification->customer_id);

        if (data_get($result, 'error')) {
            throw new \RuntimeException((string) data_get($result, 'message', 'Failed to upload KYC file.'));
        }

        return CustomerKycDocument::query()->updateOrCreate([
            'verification_id' => $verification->id,
            'document_type' => $documentType,
        ], [
            'verification_id' => $verification->id,
            'customer_id' => $verification->customer_id,
            'document_type' => $documentType,
            'file_path' => (string) data_get($result, 'data.url'),
            'checksum' => hash_file('sha256', $file->getRealPath()),
            'metadata' => [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ],
        ]);
    }

    public function submit(CustomerKycVerification $verification, array $meta = []): CustomerKycVerification
    {
        if (in_array((string) $verification->status, ['approved', 'rejected', 'manual_review'], true)) {
            return $verification->exists ? $verification->fresh(['documents']) : $verification;
        }

        $documents = $verification->documents()->get()->keyBy('document_type');

        $providerResult = $this->provider->verify([
            'verification_id' => $verification->id,
            'customer_id' => $verification->customer_id,
            'license_number' => (string) data_get($meta, 'license_number', ''),
            'license_front_path' => data_get($documents, 'license_front.file_path'),
            'license_back_path' => data_get($documents, 'license_back.file_path'),
            'selfie_path' => data_get($documents, 'selfie.file_path'),
        ]);

        $providerResult['ocr_payload'] = array_replace_recursive(
            (array) data_get($providerResult, 'ocr_payload', []),
            $this->buildDerivedOcrPayload($documents)
        );

        if (data_get($providerResult, 'await_stripe_hosted')) {
            $this->persistStripeHostedVerification($verification, $providerResult);

            return $verification->fresh(['documents']);
        }

        $decision = $this->decisionService->decide($providerResult);

        $verification->fill([
            'status' => $decision['status'],
            'provider_reference' => data_get($providerResult, 'provider_reference'),
            'ocr_confidence_score' => data_get($providerResult, 'ocr_confidence_score'),
            'face_match_score' => data_get($providerResult, 'face_match_score'),
            'risk_score' => data_get($providerResult, 'risk_score'),
            'license_valid' => data_get($providerResult, 'license_valid', false),
            'license_expiry_date' => data_get($providerResult, 'license_expiry_date'),
            'license_number' => data_get($providerResult, 'license_number'),
            'ocr_payload' => data_get($providerResult, 'ocr_payload', []),
            'provider_payload' => data_get($providerResult, 'provider_payload', []),
            'decision_reasons' => $decision['reasons'],
        ])->save();

        $customerStatus = match ($verification->status) {
            'approved' => 'verified',
            'rejected' => 'failed',
            default => 'manual_review',
        };

        $verification->customer->forceFill([
            'is_verified' => $verification->status === 'approved',
            'verified_at' => $verification->status === 'approved' ? now() : null,
            'kyc_status' => $customerStatus,
            'kyc_level' => $verification->status === 'approved' ? 'driver_verified' : 'basic',
            'kyc_last_verified_at' => $verification->status === 'approved' ? now() : null,
            'kyc_current_verification_id' => $verification->id,
        ])->save();

        Log::info('car_rentals_kyc_submitted', [
            'customer_id' => $verification->customer_id,
            'verification_id' => $verification->id,
            'status' => $verification->status,
            'risk_score' => $verification->risk_score,
        ]);

        return $verification->fresh(['documents']);
    }

    public function hasRequiredDocuments(CustomerKycVerification $verification): bool
    {
        $documents = $verification->relationLoaded('documents')
            ? $verification->documents
            : $verification->documents()->get();

        $documentTypes = $documents->pluck('document_type')->unique()->all();

        return empty(array_diff(self::REQUIRED_DOCUMENT_TYPES, $documentTypes));
    }

    public function copyDocumentsToVerification(CustomerKycVerification $source, CustomerKycVerification $target): void
    {
        $sourceDocs = $source->relationLoaded('documents')
            ? $source->documents
            : $source->documents()->get();

        foreach ($sourceDocs as $doc) {
            if (! in_array((string) $doc->document_type, self::REQUIRED_DOCUMENT_TYPES, true)) {
                continue;
            }

            CustomerKycDocument::query()->updateOrCreate(
                [
                    'verification_id' => $target->id,
                    'document_type' => $doc->document_type,
                ],
                [
                    'customer_id' => $target->customer_id,
                    'file_path' => (string) $doc->file_path,
                    'checksum' => (string) $doc->checksum,
                    'metadata' => (array) ($doc->metadata ?? []),
                ]
            );
        }
    }

    public function shouldAutoSubmit(CustomerKycVerification $verification): bool
    {
        if (! in_array((string) $verification->status, ['draft', 'pending'], true)) {
            return false;
        }

        if ((string) CarRentalsHelper::getSetting('kyc_provider', 'stripe') === 'stripe'
            && (bool) CarRentalsHelper::getSetting('kyc_stripe_enabled', true)) {
            return false;
        }

        return $this->hasRequiredDocuments($verification);
    }

    public function applyWebhookDecision(CustomerKycVerification $verification, string $externalStatus, array $payload = [], ?string $eventId = null): CustomerKycVerification
    {
        $stripeSessionStatus = (string) data_get($payload, 'stripe_identity.status', data_get($payload, 'status', ''));

        $mappedVerificationStatus = match (strtolower($externalStatus)) {
            'approved', 'verified', 'success', 'completed' => 'approved',
            'rejected', 'declined', 'failed' => 'rejected',
            default => 'manual_review',
        };

        $persistedVerificationStatus = $mappedVerificationStatus === 'manual_review' && $stripeSessionStatus !== ''
            ? $stripeSessionStatus
            : $mappedVerificationStatus;

        $mappedCustomerStatus = match ($mappedVerificationStatus) {
            'approved' => 'verified',
            'rejected' => 'failed',
            default => 'manual_review',
        };

        $providerPayload = (array) ($verification->provider_payload ?? []);
        $providerPayload['last_webhook_at'] = now()->toIso8601String();
        $providerPayload['last_webhook_payload'] = $this->normalizeStripeWebhookPayload($payload);
        $providerPayload['last_event_created_at'] = (int) data_get($payload, 'event_created_at', 0);
        if ($eventId) {
            $providerPayload['last_event_id'] = $eventId;
        }
        $decisionReasons = $this->mapStripeVerificationReasons($payload);

        DB::transaction(function () use ($verification, $mappedVerificationStatus, $persistedVerificationStatus, $mappedCustomerStatus, $payload, $providerPayload, $decisionReasons): void {
            $normalizedOcr = (array) data_get($payload, 'ocr_payload', []);
            $ocrFields = (array) data_get($normalizedOcr, 'extracted_fields', []);
            $ocrConfidence = data_get($normalizedOcr, 'confidence_score');
            $licenseExpiry = (string) (data_get($ocrFields, 'expiration_date', '') ?: '');
            $licenseNumber = (string) (data_get($ocrFields, 'license_number', '') ?: '');
            $decisionInput = array_merge((array) $payload, [
                'ocr_payload' => $normalizedOcr,
                'ocr_confidence_score' => is_numeric($ocrConfidence) ? (float) $ocrConfidence : data_get($verification, 'ocr_confidence_score'),
                'license_expiry_date' => $licenseExpiry !== '' ? $licenseExpiry : data_get($verification, 'license_expiry_date'),
                'license_number' => $licenseNumber !== '' ? $licenseNumber : data_get($verification, 'license_number'),
            ]);
            $decision = $this->decisionService->decide($decisionInput);

            $verification->update([
                'status' => $persistedVerificationStatus === 'approved' && $decision['status'] !== 'approved'
                    ? $decision['status']
                    : $persistedVerificationStatus,
                'provider_payload' => $providerPayload,
                'ocr_payload' => $normalizedOcr,
                'ocr_confidence_score' => is_numeric($ocrConfidence) ? (float) $ocrConfidence : data_get($verification, 'ocr_confidence_score'),
                'license_number' => $licenseNumber !== '' ? $licenseNumber : data_get($verification, 'license_number'),
                'license_expiry_date' => $licenseExpiry !== '' ? $licenseExpiry : data_get($verification, 'license_expiry_date'),
                'decision_reasons' => ! empty($decisionReasons)
                    ? array_values(array_unique(array_merge($decisionReasons, (array) $decision['reasons'])))
                    : (array) data_get($payload, 'decision_reasons', $verification->decision_reasons ?? []),
                'rejection_reason' => $mappedVerificationStatus === 'rejected'
                    ? (string) data_get(
                        $payload,
                        'reason',
                        data_get($payload, 'message', data_get($payload, 'stripe_identity.report.document.error_reason', data_get($payload, 'stripe_identity.report.selfie.error_reason', $verification->rejection_reason)))
                    )
                    : null,
                'reviewed_at' => now(),
            ]);

            $verification->customer->forceFill([
                'is_verified' => $mappedVerificationStatus === 'approved',
                'verified_at' => $mappedVerificationStatus === 'approved' ? now() : null,
                'kyc_status' => $mappedCustomerStatus,
                'kyc_level' => $mappedVerificationStatus === 'approved' ? 'driver_verified' : 'basic',
                'kyc_last_verified_at' => $mappedVerificationStatus === 'approved' ? now() : null,
                'kyc_current_verification_id' => $verification->id,
            ])->save();
        });

        Log::info('car_rentals_kyc_webhook_applied', [
            'customer_id' => $verification->customer_id,
            'verification_id' => $verification->id,
            'status' => $mappedVerificationStatus,
            'event_id' => $eventId,
        ]);

        return $verification->fresh(['documents']);
    }

    /**
     * True when final KYC outcome is delivered asynchronously (Stripe Identity webhooks).
     */
    public function isAsyncProviderWebhookMode(): bool
    {
        $provider = (string) CarRentalsHelper::getSetting('kyc_provider', 'stripe');

        if ($provider === 'stripe') {
            return (bool) CarRentalsHelper::getSetting('kyc_stripe_enabled', true);
        }

        return false;
    }

    /**
     * Stripe.js modal flow per https://docs.stripe.com/identity/verify-identity-documents (publishable key + client_secret).
     */
    public function stripeIdentityModalEnabled(): bool
    {
        return (string) CarRentalsHelper::getSetting('kyc_provider', 'stripe') === 'stripe'
            && (bool) CarRentalsHelper::getSetting('kyc_stripe_enabled', true)
            && (string) CarRentalsHelper::getSetting('kyc_stripe_publishable_key', '') !== '';
    }

    /**
     * @return array{client_secret: string, verification: CustomerKycVerification}
     */
    public function createStripeIdentitySessionForModal(CustomerKycVerification $verification, array $meta = []): array
    {
        if (in_array((string) $verification->status, ['approved', 'rejected', 'manual_review'], true)) {
            throw new \InvalidArgumentException(__('This verification can no longer be updated.'));
        }

        if (! $this->stripeIdentityModalEnabled()) {
            throw new \RuntimeException(__('Stripe publishable key is not configured for Identity modal.'));
        }

        if ((string) $verification->status === 'pending'
            && $verification->provider_reference
            && data_get($verification->provider_payload, 'stripe_identity')) {
            $existing = $this->retrieveStripeIdentityClientSecret((string) $verification->provider_reference);
            if (is_string($existing) && $existing !== '') {
                return [
                    'client_secret' => $existing,
                    'verification' => $verification->fresh(['documents']),
                ];
            }
        }

        $documents = $verification->documents()->get()->keyBy('document_type');

        $providerResult = $this->provider->verify([
            'verification_id' => $verification->id,
            'customer_id' => $verification->customer_id,
            'license_number' => (string) data_get($meta, 'license_number', ''),
            'license_front_path' => data_get($documents, 'license_front.file_path'),
            'license_back_path' => data_get($documents, 'license_back.file_path'),
            'selfie_path' => data_get($documents, 'selfie.file_path'),
        ]);

        $providerResult['ocr_payload'] = array_replace_recursive(
            (array) data_get($providerResult, 'ocr_payload', []),
            $this->buildDerivedOcrPayload($documents)
        );

        if (! data_get($providerResult, 'await_stripe_hosted')) {
            throw new \RuntimeException(__('Stripe Identity session could not be created.'));
        }

        $clientSecret = data_get($providerResult, 'stripe_client_secret');
        if (! is_string($clientSecret) || $clientSecret === '') {
            throw new \RuntimeException(__('Stripe Identity session is missing a client secret.'));
        }

        $this->persistStripeHostedVerification($verification, $providerResult);

        return [
            'client_secret' => $clientSecret,
            'verification' => $verification->fresh(['documents']),
        ];
    }

    protected function persistStripeHostedVerification(CustomerKycVerification $verification, array $providerResult): void
    {
        $stripeUrl = (string) data_get($providerResult, 'stripe_identity_url', '');
        $providerPayload = (array) data_get($providerResult, 'provider_payload', []);
        unset($providerPayload['client_secret']);
        $providerPayload['stripe_identity'] = true;
        $providerPayload['stripe_identity_url'] = $stripeUrl;

        $verification->fill([
            'status' => 'pending',
            'provider_reference' => data_get($providerResult, 'provider_reference'),
            'provider_payload' => $providerPayload,
            'license_valid' => false,
        ])->save();

        $verification->customer->forceFill([
            'kyc_status' => 'pending',
            'kyc_current_verification_id' => $verification->id,
        ])->save();

        Log::info('car_rentals_kyc_stripe_identity_session_created', [
            'customer_id' => $verification->customer_id,
            'verification_id' => $verification->id,
            'provider_reference' => $verification->provider_reference,
        ]);
    }

    protected function retrieveStripeIdentityClientSecret(string $sessionId): ?string
    {
        $secret = (string) CarRentalsHelper::getSetting('kyc_stripe_secret_key', '');
        if ($secret === '') {
            return null;
        }

        try {
            $stripe = new StripeClient($secret);

            return (string) $stripe->identity->verificationSessions->retrieve($sessionId)->client_secret;
        } catch (\Throwable) {
            return null;
        }
    }

    public function normalizeStripeWebhookPayload(array $payload): array
    {
        return array_filter([
            'stripe_identity' => array_filter([
                'session_id' => data_get($payload, 'stripe_identity.session_id', data_get($payload, 'id')),
                'status' => data_get($payload, 'stripe_identity.status', data_get($payload, 'status')),
                'report' => array_filter([
                    'document' => array_filter([
                        'status' => data_get($payload, 'stripe_identity.report.document.status'),
                        'error_code' => data_get($payload, 'stripe_identity.report.document.error_code'),
                    ], fn ($value) => $value !== null && $value !== ''),
                    'selfie' => array_filter([
                        'status' => data_get($payload, 'stripe_identity.report.selfie.status'),
                        'error_code' => data_get($payload, 'stripe_identity.report.selfie.error_code'),
                    ], fn ($value) => $value !== null && $value !== ''),
                ], fn ($value) => ! empty($value)),
            ], fn ($value) => ! empty($value)),
            'ocr_payload' => array_filter([
                'source' => data_get($payload, 'ocr_payload.source'),
                'confidence_score' => data_get($payload, 'ocr_payload.confidence_score', data_get($payload, 'ocr_confidence_score')),
                'extracted_fields' => array_filter([
                    'license_number' => data_get($payload, 'ocr_payload.extracted_fields.license_number'),
                    'full_name' => data_get($payload, 'ocr_payload.extracted_fields.full_name'),
                    'date_of_birth' => data_get($payload, 'ocr_payload.extracted_fields.date_of_birth'),
                    'expiration_date' => data_get($payload, 'ocr_payload.extracted_fields.expiration_date'),
                    'issuing_country' => data_get($payload, 'ocr_payload.extracted_fields.issuing_country'),
                ], fn ($value) => $value !== null && $value !== ''),
            ], fn ($value) => ! empty($value)),
            'reason' => data_get($payload, 'reason'),
            'decision_reasons' => (array) data_get($payload, 'decision_reasons', []),
            'event_id' => data_get($payload, 'event_id'),
            'event_created_at' => data_get($payload, 'event_created_at'),
            'event_type' => data_get($payload, 'event_type'),
        ], fn ($value) => ! empty($value));
    }

    public function mapStripeVerificationReasons(array $payload): array
    {
        $codes = array_values(array_filter([
            (string) data_get($payload, 'stripe_identity.report.document.error_code', ''),
            (string) data_get($payload, 'stripe_identity.report.selfie.error_code', ''),
        ]));

        $mapped = [];

        foreach ($codes as $code) {
            $mapped[] = match ($code) {
                'selfie_face_mismatch' => 'selfie_face_mismatch',
                'selfie_manipulated' => 'selfie_manipulated',
                'selfie_unverified_other' => 'selfie_unverified_other',
                'selfie_document_missing_photo' => 'selfie_document_missing_photo',
                'document_expired' => 'document_expired',
                'document_type_not_supported' => 'document_type_not_supported',
                'document_unverified_other' => 'document_unverified_other',
                default => 'stripe_verification_failed',
            };
        }

        if (empty($mapped) && (string) data_get($payload, 'stripe_identity.status', data_get($payload, 'status')) === 'requires_input') {
            $mapped[] = 'stripe_verification_failed';
        }

        if (
            ((bool) data_get($payload, 'non_biometric_fallback', false)
                || (bool) $this->getCarRentalsSetting('kyc_allow_non_biometric_fallback', false))
            && in_array('selfie_face_mismatch', $mapped, true)
            && ! in_array('document_expired', $mapped, true)
            && ! in_array('document_type_not_supported', $mapped, true)
        ) {
            $mapped[] = 'requires_manual_biometric_review';
        }

        return array_values(array_unique($mapped));
    }

    protected function getCarRentalsSetting(string $key, mixed $default = null): mixed
    {
        try {
            if (function_exists('get_car_rentals_setting')) {
                return get_car_rentals_setting($key, $default);
            }

            if (class_exists(CarRentalsHelper::class)) {
                return CarRentalsHelper::getSetting($key, $default);
            }
        } catch (\Throwable) {
            return $default;
        }

        return $default;
    }

    public function pruneExpiredProviderPayloads(?int $retentionDays = null): int
    {
        $days = $retentionDays ?? (int) $this->getCarRentalsSetting('kyc_payload_retention_days', 90);
        $days = max(1, $days);
        $cutoff = now()->subDays($days);

        $verifications = CustomerKycVerification::query()
            ->whereNotNull('provider_payload')
            ->where(function ($query) use ($cutoff): void {
                $query->whereNotNull('reviewed_at')->where('reviewed_at', '<', $cutoff)
                    ->orWhere(function ($inner) use ($cutoff): void {
                        $inner->whereNull('reviewed_at')->where('updated_at', '<', $cutoff);
                    });
            })
            ->get();

        $updated = 0;

        foreach ($verifications as $verification) {
            $payload = (array) ($verification->provider_payload ?? []);
            if (empty($payload)) {
                continue;
            }

            $minimal = array_filter([
                'last_event_id' => data_get($payload, 'last_event_id'),
                'last_event_created_at' => data_get($payload, 'last_event_created_at'),
                'last_webhook_at' => data_get($payload, 'last_webhook_at'),
                'stripe_identity' => array_filter([
                    'status' => data_get($payload, 'last_webhook_payload.stripe_identity.status', data_get($payload, 'stripe_identity.status')),
                    'session_id' => data_get($payload, 'last_webhook_payload.stripe_identity.session_id', data_get($payload, 'stripe_identity.session_id')),
                ]),
                'pruned_at' => now()->toIso8601String(),
            ], fn ($value) => ! empty($value));

            if ($minimal !== $payload) {
                $verification->provider_payload = $minimal;
                $verification->save();
                $updated++;
            }
        }

        return $updated;
    }

    protected function buildDerivedOcrPayload(\Illuminate\Support\Collection $documents): array
    {
        $required = self::REQUIRED_DOCUMENT_TYPES;
        $present = collect($required)
            ->filter(fn (string $type) => (string) data_get($documents, $type . '.checksum', '') !== '')
            ->values()
            ->all();

        if (empty($present)) {
            return [];
        }

        $confidence = round(count($present) / count($required), 2);

        return [
            'confidence_score' => $confidence,
            'extracted_fields' => array_filter([
                'license_number' => (string) data_get($documents, 'license_front.metadata.ocr_license_number', ''),
                'full_name' => (string) data_get($documents, 'license_front.metadata.ocr_full_name', ''),
                'expiration_date' => (string) data_get($documents, 'license_front.metadata.ocr_expiration_date', ''),
                'issuing_country' => (string) data_get($documents, 'license_front.metadata.ocr_issuing_country', ''),
            ], fn ($value) => is_string($value) && $value !== ''),
            'document_presence' => $present,
            'derived_from_uploads' => true,
        ];
    }

    protected function redactUploadedDocuments(CustomerKycVerification $verification, string $reason): void
    {
        $verification->loadMissing('documents');

        foreach ($verification->documents as $document) {
            if ((string) $document->file_path === '') {
                continue;
            }

            $metadata = (array) ($document->metadata ?? []);
            $metadata['redacted_at'] = now()->toIso8601String();
            $metadata['redaction_reason'] = $reason;

            $document->forceFill([
                'file_path' => '',
                'metadata' => $metadata,
            ])->save();
        }
    }
}
