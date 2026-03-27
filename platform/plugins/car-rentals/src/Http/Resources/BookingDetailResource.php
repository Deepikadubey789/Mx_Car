<?php

namespace Botble\CarRentals\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_number' => $this->booking_number,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
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
            'completion_miles' => $this->completion_miles,
            'completion_gas_level' => $this->completion_gas_level,
            'completion_damage_images' => $this->completion_damage_images,
            'completion_notes' => $this->completion_notes,
            'completed_at' => $this->completed_at,
            'car' => $this->whenLoaded('car', function () {
                return $this->car && $this->car->car ? [
                    'id' => $this->car->car->id,
                    'name' => $this->car->car->name,
                    'description' => $this->car->car->description,
                    'images' => $this->car->car->images,
                    'rental_rate' => $this->car->car->rental_rate,
                    'rental_type' => $this->car->car->rental_type,
                    'year' => $this->car->car->year,
                    'number_of_seats' => $this->car->car->number_of_seats,
                    'number_of_doors' => $this->car->car->number_of_doors,
                    'license_plate' => $this->car->car->license_plate,
                    'make' => $this->car->car->make ? [
                        'id' => $this->car->car->make->id,
                        'name' => $this->car->car->make->name,
                        'logo' => $this->car->car->make->logo,
                    ] : null,
                    'type' => $this->car->car->type ? [
                        'id' => $this->car->car->type->id,
                        'name' => $this->car->car->type->name,
                    ] : null,
                    'transmission' => $this->car->car->transmission ? [
                        'id' => $this->car->car->transmission->id,
                        'name' => $this->car->car->transmission->name,
                    ] : null,
                    'fuel' => $this->car->car->fuel ? [
                        'id' => $this->car->car->fuel->id,
                        'name' => $this->car->car->fuel->name,
                    ] : null,
                    'location' => $this->car->car->location,
                    'pickup_city' => $this->car->pickupCity ? [
                        'id' => $this->car->pickupCity->id,
                        'name' => $this->car->pickupCity->name,
                        'state' => $this->car->pickupCity->state ? $this->car->pickupCity->state->name : null,
                        'country' => $this->car->pickupCity->country ? $this->car->pickupCity->country->name : null,
                    ] : null,
                    'return_city' => $this->car->returnCity ? [
                        'id' => $this->car->returnCity->id,
                        'name' => $this->car->returnCity->name,
                        'state' => $this->car->returnCity->state ? $this->car->returnCity->state->name : null,
                        'country' => $this->car->returnCity->country ? $this->car->returnCity->country->name : null,
                    ] : null,
                ] : null;
            }),
            'services' => $this->whenLoaded('services', function () {
                return $this->services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'description' => $service->description,
                        'price' => $service->price,
                        'image' => $service->image,
                    ];
                });
            }),
            'currency' => $this->whenLoaded('currency', function () {
                return $this->currency ? [
                    'id' => $this->currency->id,
                    'title' => $this->currency->title,
                    'symbol' => $this->currency->symbol,
                    'is_default' => $this->currency->is_default,
                    'exchange_rate' => $this->currency->exchange_rate,
                ] : null;
            }),
            'payment' => $this->whenLoaded('payment', function () {
                return $this->payment ? [
                    'id' => $this->payment->id,
                    'status' => $this->payment->status,
                    'payment_channel' => $this->payment->payment_channel,
                    'amount' => $this->payment->amount,
                    'refunded_amount' => $this->payment->refunded_amount,
                    'charge_id' => $this->payment->charge_id,
                    'created_at' => $this->payment->created_at,
                ] : null;
            }),
            'invoice' => $this->whenLoaded('invoice', function () {
                return $this->invoice ? [
                    'id' => $this->invoice->id,
                    'code' => $this->invoice->code,
                    'created_at' => $this->invoice->created_at,
                ] : null;
            }),
            'vendor' => $this->whenLoaded('vendor', function () {
                return $this->vendor ? [
                    'id' => $this->vendor->id,
                    'name' => $this->vendor->name,
                    'phone' => $this->vendor->phone,
                    'email' => $this->vendor->email,
                ] : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
