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
            'price_lock_expired_message' => ['required', 'string', 'max:500'],
        ];
    }
}
