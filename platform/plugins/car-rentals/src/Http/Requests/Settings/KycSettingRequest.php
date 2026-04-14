<?php

namespace Botble\CarRentals\Http\Requests\Settings;

use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class KycSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'deposit_risk_kyc_pending_multiplier' => ['nullable', 'numeric', 'min:0.1'],
            'kyc_face_match_threshold' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'kyc_ocr_confidence_threshold' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'kyc_manual_review_risk_threshold' => ['nullable', 'numeric', 'min:0', 'max:2'],
            'eligibility_restricted_category_ids' => ['nullable', 'array'],
            'eligibility_restricted_category_ids.*' => ['integer', 'exists:cr_car_categories,id'],
            'kyc_provider' => ['nullable', 'string', Rule::in(['stripe', 'mock'])],
            'kyc_stripe_enabled' => ['nullable', 'boolean'],
            'kyc_stripe_secret_key' => ['nullable', 'string', 'max:255'],
            'kyc_stripe_publishable_key' => ['nullable', 'string', 'max:255'],
            'kyc_stripe_webhook_secret' => ['nullable', 'string', 'max:255'],
            'fallback_to_mock_on_provider_error' => ['nullable', 'boolean'],
            'kyc_allow_non_biometric_fallback' => ['nullable', 'boolean'],
            'kyc_payload_retention_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
        ];
    }
}
