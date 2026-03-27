<?php

namespace Botble\CarRentals\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'content' => $this->content,
            'images' => $this->images,
            'license_plate' => $this->license_plate,
            'rental_rate' => $this->rental_rate,
            'rental_type' => $this->rental_type,
            'rental_available_types' => $this->rental_available_types,
            'price_text' => $this->price_text,
            'year' => $this->year,
            'mileage' => $this->mileage,
            'horsepower' => $this->horsepower,
            'number_of_seats' => $this->number_of_seats,
            'number_of_doors' => $this->number_of_doors,
            'location' => $this->current_location,
            'is_featured' => $this->is_featured,
            'is_used' => $this->is_used,
            'insurance_info' => $this->insurance_info,
            'vin' => $this->vin,
            'avg_review' => $this->avg_review,
            'reviews_count' => $this->reviews_count,
            'make' => $this->whenLoaded('make', function () {
                return [
                    'id' => $this->make->id,
                    'name' => $this->make->name,
                    'logo' => $this->make->logo,
                ];
            }),
            'type' => $this->whenLoaded('type', function () {
                return [
                    'id' => $this->type->id,
                    'name' => $this->type->name,
                    'icon' => $this->type->icon,
                ];
            }),
            'transmission' => $this->whenLoaded('transmission', function () {
                return [
                    'id' => $this->transmission->id,
                    'name' => $this->transmission->name,
                ];
            }),
            'fuel' => $this->whenLoaded('fuel', function () {
                return [
                    'id' => $this->fuel->id,
                    'name' => $this->fuel->name,
                ];
            }),
            'amenities' => $this->whenLoaded('amenities', function () {
                return $this->amenities->map(function ($amenity) {
                    return [
                        'id' => $amenity->id,
                        'name' => $amenity->name,
                        'icon' => $amenity->icon,
                    ];
                });
            }),
            'location' => $this->location,
            'city' => $this->whenLoaded('city', function () {
                return $this->city ? [
                    'id' => $this->city->id,
                    'name' => $this->city->name,
                    'state' => $this->city->state ? $this->city->state->name : null,
                    'country' => $this->city->country ? $this->city->country->name : null,
                ] : null;
            }),
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ];
                });
            }),
            'reviews' => $this->whenLoaded('reviews', function () {
                return $this->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'star' => $review->star,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at,
                        'customer' => $review->customer ? [
                            'id' => $review->customer->id,
                            'name' => $review->customer->name,
                            'avatar' => $review->customer->avatar_url,
                        ] : null,
                    ];
                });
            }),
            'vendor' => $this->whenLoaded('vendor', function () {
                return $this->vendor ? [
                    'id' => $this->vendor->id,
                    'name' => $this->vendor->name,
                    'avatar' => $this->vendor->avatar_url,
                    'phone' => $this->vendor->phone,
                ] : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
