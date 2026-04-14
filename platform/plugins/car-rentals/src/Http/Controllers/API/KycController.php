<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Models\CustomerKycVerification;
use Botble\CarRentals\Services\Kyc\KycVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KycController extends BaseApiController
{
    public function __construct(protected KycVerificationService $kycVerificationService)
    {
    }

    public function start()
    {
        $customer = Auth::guard('sanctum')->user();

        $verification = $this->kycVerificationService->start($customer);

        return $this
            ->httpResponse()
            ->setData([
                'verification' => $this->formatVerification($verification->load('documents')),
                'bootstrap' => $this->buildBootstrapPayload($verification->load('documents')),
            ])
            ->setMessage('KYC verification session started')
            ->toApiResponse();
    }

    public function upload(Request $request, int $verificationId)
    {
        $customer = Auth::guard('sanctum')->user();

        $request->validate([
            'document_type' => ['required', 'in:license_front,license_back,selfie'],
            'file' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
        ]);

        $verification = CustomerKycVerification::query()
            ->where('customer_id', $customer->id)
            ->findOrFail($verificationId);

        $document = $this->kycVerificationService->uploadDocument(
            $verification,
            $request->file('file'),
            $request->input('document_type')
        );

        $verification = $verification->fresh(['documents']);
        $autoSubmitted = false;
        $awaitingWebhook = false;

        if ($this->kycVerificationService->shouldAutoSubmit($verification)) {
            $verification = $this->kycVerificationService->submit($verification, []);
            $autoSubmitted = true;
            $awaitingWebhook = $this->kycVerificationService->isAsyncProviderWebhookMode()
                && (string) $verification->status === 'pending';
        }

        return $this
            ->httpResponse()
            ->setData([
                'document' => [
                    'id' => $document->id,
                    'document_type' => $document->document_type,
                    'uploaded' => true,
                    'uploaded_at' => optional($document->updated_at)->toIso8601String(),
                ],
                'auto_submitted' => $autoSubmitted,
                'awaiting_webhook' => $awaitingWebhook,
                'verification' => $this->formatVerification($verification),
                'bootstrap' => $this->buildBootstrapPayload($verification),
            ])
            ->setMessage($autoSubmitted ? 'KYC document uploaded and verification submitted' : 'KYC document uploaded')
            ->toApiResponse();
    }

    public function submit(Request $request, int $verificationId)
    {
        $customer = Auth::guard('sanctum')->user();

        $request->validate([
            'license_number' => ['nullable', 'string', 'min:6', 'max:120'],
        ]);

        $verification = CustomerKycVerification::query()
            ->where('customer_id', $customer->id)
            ->with('documents')
            ->findOrFail($verificationId);

        $verification = $this->kycVerificationService->submit($verification, [
            'license_number' => (string) $request->input('license_number', ''),
        ]);

        $awaitingWebhook = $this->kycVerificationService->isAsyncProviderWebhookMode()
            && (string) $verification->status === 'pending';

        $stripeIdentityUrl = (string) data_get($verification->provider_payload, 'stripe_identity_url', '');

        return $this
            ->httpResponse()
            ->setData([
                'awaiting_webhook' => $awaitingWebhook,
                'stripe_identity_url' => $stripeIdentityUrl !== '' ? $stripeIdentityUrl : null,
                'verification' => $this->formatVerification($verification),
                'bootstrap' => $this->buildBootstrapPayload($verification),
            ])
            ->setMessage($awaitingWebhook ? 'KYC submitted and awaiting webhook decision' : 'KYC verification submitted')
            ->toApiResponse();
    }

    public function stripeIdentitySession(Request $request, int $verificationId)
    {
        $customer = Auth::guard('sanctum')->user();

        $request->validate([
            'license_number' => ['nullable', 'string', 'max:120'],
        ]);

        $verification = CustomerKycVerification::query()
            ->where('customer_id', $customer->id)
            ->with('documents')
            ->findOrFail($verificationId);

        if (in_array((string) $verification->status, ['approved', 'rejected', 'manual_review', 'requires_input', 'canceled', 'verified'], true)) {
            $previousVerification = $verification;
            $verification = $this->kycVerificationService->start($customer)->load('documents');
            $this->kycVerificationService->copyDocumentsToVerification($previousVerification, $verification);
        }

        if (! $this->kycVerificationService->hasRequiredDocuments($verification)) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(422)
                ->setMessage('Please upload license front, license back, and selfie before starting verification.')
                ->setData([
                    'bootstrap' => $this->buildBootstrapPayload($verification),
                ])
                ->toApiResponse();
        }

        try {
            $result = $this->kycVerificationService->createStripeIdentitySessionForModal($verification, [
                'license_number' => (string) $request->input('license_number', ''),
            ]);
        } catch (\InvalidArgumentException $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage())
                ->setCode(422)
                ->toApiResponse();
        } catch (\Throwable $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage())
                ->setCode(422)
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setData([
                'client_secret' => $result['client_secret'],
                'verification_id' => $result['verification']->id,
                'session_status' => (string) $result['verification']->status,
                'awaiting_webhook' => true,
                'requires_retry' => false,
                'verification' => $this->formatVerification($result['verification']),
            ])
            ->setMessage('Stripe Identity session ready')
            ->toApiResponse();
    }

    public function bootstrap()
    {
        $customer = Auth::guard('sanctum')->user();

        $verification = CustomerKycVerification::query()
            ->where('customer_id', $customer->id)
            ->with('documents')
            ->when($customer->kyc_current_verification_id, function ($query) use ($customer): void {
                $query->where('id', $customer->kyc_current_verification_id);
            })
            ->latest('id')
            ->first();

        if (! $verification) {
            $verification = $this->kycVerificationService->start($customer)->load('documents');
        }

        return $this
            ->httpResponse()
            ->setData([
                'verification' => $this->formatVerification($verification),
                'bootstrap' => $this->buildBootstrapPayload($verification),
            ])
            ->toApiResponse();
    }

    public function status()
    {
        $customer = Auth::guard('sanctum')->user();

        $verification = CustomerKycVerification::query()
            ->where('customer_id', $customer->id)
            ->latest('id')
            ->with('documents')
            ->first();

        return $this
            ->httpResponse()
            ->setData([
                'kyc_status' => $customer->kyc_status,
                'kyc_level' => $customer->kyc_level,
                'verification' => $verification ? $this->formatVerification($verification) : null,
                'bootstrap' => $verification ? $this->buildBootstrapPayload($verification) : null,
                'retry_hints' => $this->buildRetryHints((array) data_get($verification, 'decision_reasons', [])),
            ])
            ->toApiResponse();
    }

    protected function buildBootstrapPayload(CustomerKycVerification $verification): array
    {
        $documents = $verification->relationLoaded('documents')
            ? $verification->documents
            : $verification->documents()->get();

        $required = ['license_front', 'license_back', 'selfie'];
        $uploaded = [];

        foreach ($required as $type) {
            $uploaded[$type] = (bool) $documents->firstWhere('document_type', $type);
        }

        return [
            'verification_id' => $verification->id,
            'verification_status' => (string) $verification->status,
            'required_documents' => $required,
            'uploaded_documents' => $uploaded,
            'can_create_stripe_session' => ! in_array(false, $uploaded, true),
            'awaiting_webhook' => (string) $verification->status === 'pending' && (string) $verification->provider_reference !== '',
        ];
    }

    protected function formatVerification(CustomerKycVerification $verification): array
    {
        return [
            'id' => $verification->id,
            'status' => (string) $verification->status,
            'provider' => (string) $verification->provider,
            'provider_reference' => (string) $verification->provider_reference,
            'ocr_confidence_score' => $verification->ocr_confidence_score,
            'face_match_score' => $verification->face_match_score,
            'risk_score' => $verification->risk_score,
            'license_valid' => (bool) $verification->license_valid,
            'license_expiry_date' => optional($verification->license_expiry_date)->toDateString(),
            'license_number' => $verification->license_number,
            'decision_reasons' => (array) ($verification->decision_reasons ?? []),
            'ocr_payload' => (array) ($verification->ocr_payload ?? []),
            'created_at' => optional($verification->created_at)->toIso8601String(),
            'updated_at' => optional($verification->updated_at)->toIso8601String(),
        ];
    }

    protected function buildRetryHints(array $decisionReasons): array
    {
        $messages = [];

        foreach ($decisionReasons as $reason) {
            $messages[] = match ((string) $reason) {
                'selfie_face_mismatch' => 'Selfie does not match document photo. Retry in good lighting.',
                'document_expired' => 'Document appears expired. Upload a valid, non-expired document.',
                'document_type_not_supported' => 'Document type is not supported. Use passport, license, or national ID.',
                'stripe_ocr_required_fields_missing', 'ocr_required_fields_missing' => 'Required OCR fields are missing. Retake clear images.',
                'stripe_verification_failed' => 'Stripe could not verify submission. Retry with clearer photos.',
                default => 'Verification requires review. Please retry or contact support.',
            };
        }

        return array_values(array_unique($messages));
    }
}
