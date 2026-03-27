<?php

namespace Botble\CarRentals\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_number' => $this->booking_number,
            'status' => $this->status,
            'amount' => $this->amount,
            'sub_total' => $this->sub_total,
            'tax_amount' => $this->tax_amount,
            'coupon_amount' => $this->coupon_amount,
            'coupon_code' => $this->coupon_code,
            'pickup_date' => $this->car->pickup_date ?? null,
            'return_date' => $this->car->return_date ?? null,
            'pickup_time' => $this->car->pickup_time ?? null,
            'return_time' => $this->car->return_time ?? null,
            'note' => $this->note,
            'car' => $this->whenLoaded('car', function () {
                return $this->car && $this->car->car ? [
                    'id' => $this->car->car->id,
                    'name' => $this->car->car->name,
                    'images' => $this->car->car->images,
                    'rental_rate' => $this->car->car->rental_rate,
                    'make' => $this->car->car->make ? [
                        'id' => $this->car->car->make->id,
                        'name' => $this->car->car->make->name,
                    ] : null,
                ] : null;
            }),
            'services' => $this->whenLoaded('services', function () {
                return $this->services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->price,
                    ];
                });
            }),
            'currency' => $this->whenLoaded('currency', function () {
                return $this->currency ? [
                    'id' => $this->currency->id,
                    'title' => $this->currency->title,
                    'symbol' => $this->currency->symbol,
                ] : null;
            }),
            'payment' => $this->whenLoaded('payment', function () {
                return $this->payment ? [
                    'id' => $this->payment->id,
                    'status' => $this->payment->status,
                    'payment_channel' => $this->payment->payment_channel,
                    'amount' => $this->payment->amount,
                ] : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
