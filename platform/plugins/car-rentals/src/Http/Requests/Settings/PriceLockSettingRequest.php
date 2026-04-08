<?php

namespace Botble\CarRentals\Http\Requests\Settings;

use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PriceLockSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'price_lock_duration_minutes' => ['required', 'integer', 'min:1', 'max:120'],
            'deposit_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'deposit_type' => ['required', 'string', Rule::in(['percentage', 'fixed'])],
            'deposit_fixed_amount' => ['nullable', 'numeric', 'min:0'],
            'fee_name' => ['nullable', 'string', 'max:120'],
            'fee_value' => ['required', 'numeric', 'min:0'],
            'deposit_risk_enabled' => ['nullable', 'boolean'],
            'deposit_risk_default_multiplier' => ['nullable', 'numeric', 'min:0.1'],
            'deposit_risk_guest_multiplier' => ['nullable', 'numeric', 'min:0.1'],
            'deposit_risk_unverified_multiplier' => ['nullable', 'numeric', 'min:0.1'],
            'deposit_risk_low_history_threshold' => ['nullable', 'integer', 'min:0'],
            'deposit_risk_low_history_multiplier' => ['nullable', 'numeric', 'min:0.1'],
            'deposit_risk_low_rating_threshold' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'deposit_risk_low_rating_multiplier' => ['nullable', 'numeric', 'min:0.1'],
            'deposit_risk_escalation_multiplier' => ['nullable', 'numeric', 'min:0.1'],
            'deposit_risk_medium_threshold' => ['nullable', 'numeric', 'min:1'],
            'deposit_risk_high_threshold' => ['nullable', 'numeric', 'min:1'],
            'deposit_category_multipliers_json' => ['nullable', 'json'],
            'deposit_type_multipliers_json' => ['nullable', 'json'],
            'price_lock_expired_message' => ['required', 'string', 'max:500'],
        ];
    }
}
