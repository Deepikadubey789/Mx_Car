<?php

namespace Botble\CarRentals\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class HostProtectionPlanRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'description' => 'nullable|string',
            'revenue_share_percentage' => 'required|numeric|min:0|max:100',
            'deductible_amount' => 'required|numeric|min:0',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}