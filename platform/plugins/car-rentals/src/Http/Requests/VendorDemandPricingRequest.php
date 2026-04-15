<?php

namespace Botble\CarRentals\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorDemandPricingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('customer')->check() && auth('customer')->user()->is_vendor;
    }

    public function rules(): array
    {
        return [
            'adjusted_price' => ['sometimes', 'numeric', 'min:1', 'max:99999.99'],
            'adjustment_notes' => ['sometimes', 'string', 'max:500'],
            'rejected_reason' => ['sometimes', 'in:too_high,too_low,inventory_issue,not_applicable,other'],
            'vendor_notes' => ['sometimes', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'adjusted_price.numeric' => 'Price must be a valid number',
            'adjusted_price.min' => 'Price must be at least $1',
            'adjusted_price.max' => 'Price cannot exceed $99,999.99',
            'adjustment_notes.max' => 'Notes cannot exceed 500 characters',
            'rejected_reason.in' => 'Please select a valid rejection reason',
            'vendor_notes.max' => 'Notes cannot exceed 500 characters',
        ];
    }
}
