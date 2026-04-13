<?php

namespace Botble\CarRentals\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class GuestProtectionPlanRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'description' => 'nullable|string',
            'daily_fee' => 'required|numeric|min:0',
            'deductible_amount' => 'required|numeric|min:0',
            'liability_limit' => 'nullable|numeric|min:0',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}