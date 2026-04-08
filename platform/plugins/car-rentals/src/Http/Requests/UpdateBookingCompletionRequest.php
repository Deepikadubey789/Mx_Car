<?php

namespace Botble\CarRentals\Http\Requests;

use Botble\Support\Http\Requests\Request;

class UpdateBookingCompletionRequest extends Request
{
    public function rules(): array
    {
        return [
            'completion_miles' => ['nullable', 'integer', 'min:0'],
            'completion_gas_level' => ['nullable', 'string', 'in:empty,quarter,half,three_quarters,full'],
            'completion_damage_images' => ['nullable', 'array'],
            'completion_damage_images.*' => ['file', 'image', 'mimes:jpeg,jpg,png,gif', 'max:5120'], // 5MB max
            'existing_damage_images' => ['nullable', 'array'],
            'existing_damage_images.*' => ['string'],
            'completion_notes' => ['nullable', 'string', 'max:10000'],
            'deposit_settlement_action' => ['nullable', 'string', 'in:release,capture_partial,capture_full'],
            'deposit_capture_amount' => ['nullable', 'required_if:deposit_settlement_action,capture_partial', 'numeric', 'gt:0', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'completion_miles' => trans('plugins/car-rentals::booking.completion_miles'),
            'completion_gas_level' => trans('plugins/car-rentals::booking.completion_gas_level'),
            'completion_damage_images' => trans('plugins/car-rentals::booking.damage_images'),
            'completion_notes' => trans('plugins/car-rentals::booking.completion_notes'),
            'deposit_settlement_action' => trans('plugins/car-rentals::booking.deposit_settlement_action'),
            'deposit_capture_amount' => trans('plugins/car-rentals::booking.deposit_capture_amount'),
        ];
    }

    public function messages(): array
    {
        return [
            'completion_miles.integer' => trans('plugins/car-rentals::booking.validation.completion_miles_integer'),
            'completion_miles.min' => trans('plugins/car-rentals::booking.validation.completion_miles_min'),
            'completion_gas_level.in' => trans('plugins/car-rentals::booking.validation.completion_gas_level_invalid'),
            'completion_damage_images.*.image' => trans('plugins/car-rentals::booking.validation.damage_image_invalid'),
            'completion_damage_images.*.max' => trans('plugins/car-rentals::booking.validation.damage_image_max_size'),
            'completion_notes.max' => trans('plugins/car-rentals::booking.validation.completion_notes_max'),
            'deposit_settlement_action.in' => trans('plugins/car-rentals::booking.validation.deposit_settlement_action_invalid'),
            'deposit_capture_amount.numeric' => trans('plugins/car-rentals::booking.validation.deposit_capture_amount_numeric'),
            'deposit_capture_amount.required_if' => trans('plugins/car-rentals::booking.validation.deposit_capture_amount_required'),
            'deposit_capture_amount.gt' => trans('plugins/car-rentals::booking.validation.deposit_capture_amount_gt'),
            'deposit_capture_amount.min' => trans('plugins/car-rentals::booking.validation.deposit_capture_amount_min'),
        ];
    }
}
