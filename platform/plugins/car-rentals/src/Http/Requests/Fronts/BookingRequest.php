<?php

namespace Botble\CarRentals\Http\Requests\Fronts;

use Botble\Support\Http\Requests\Request;

class BookingRequest extends Request
{
    public function rules(): array
    {
        return [
            'car_id' => ['required', 'exists:cr_cars,id'],
            'rental_start_date' => ['required', 'string', 'date', 'after_or_equal:today'],
            'rental_start_time' => ['required', 'string', 'date_format:H:i'],
            'rental_end_date' => ['required', 'string', 'date', 'after_or_equal:rental_start_date'],
            'rental_end_time' => ['required', 'string', 'date_format:H:i'],
        ];
    }
}
