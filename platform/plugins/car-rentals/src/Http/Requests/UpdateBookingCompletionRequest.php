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
        ];
    }

    public function attributes(): array
    {
        return [
            'completion_miles' => trans('plugins/car-rentals::booking.completion_miles'),
            'completion_gas_level' => trans('plugins/car-rentals::booking.completion_gas_level'),
            'completion_damage_images' => trans('plugins/car-rentals::booking.damage_images'),
            'completion_notes' => trans('plugins/car-rentals::booking.completion_notes'),
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
        ];
    }
}
