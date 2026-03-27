<?php

namespace Botble\CarRentals\Http\Requests;

use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CreateBookingRequest extends Request
{
    public function rules(): array
    {
        return [
            'car_id' => ['required', 'exists:cr_cars,id'],
            'rental_start_date' => ['required', 'date', 'after_or_equal:today'],
            'rental_end_date' => ['required', 'date', 'after_or_equal:rental_start_date'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],

            'note' => ['nullable', 'string', 'max:10000'],
            'coupon_code' => ['nullable', 'string', 'max:20'],
            'coupon_amount' => ['nullable', 'numeric', 'min:0'],
            'services' => ['nullable', 'array'],
            'status' => ['required', 'string'],
            'customer_id' => ['nullable', 'exists:cr_customers,id'],
            'payment_method' => ['required', 'string', Rule::in(PaymentMethodEnum::values())],
            'payment_status' => ['required', 'string', Rule::in(PaymentStatusEnum::values())],
            'transaction_id' => ['nullable', 'string', 'max:60'],
        ];
    }
}
