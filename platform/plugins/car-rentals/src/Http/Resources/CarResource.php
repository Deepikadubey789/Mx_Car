<?php

namespace Botble\CarRentals\Http\Resources;

use Botble\Media\Facades\RvMedia;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
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
            'images' => array_map(function ($image) {
                return [
                    'url' => RvMedia::getImageUrl($image),
                    'thumb' => RvMedia::getImageUrl($image, 'thumb'),
                ];
            }, $this->getImages()),
            'rental_rate' => $this->rental_rate,
            'rental_type' => $this->rental_type,
            'price_text' => $this->price_text,
            'year' => $this->year,
            'mileage' => $this->mileage,
            'horsepower' => $this->horsepower,
            'number_of_seats' => $this->number_of_seats,
            'number_of_doors' => $this->number_of_doors,
            'location' => $this->current_location,
            'is_featured' => $this->is_featured,
            'avg_review' => $this->avg_review,
            'reviews_count' => $this->reviews_count,
            'make' => $this->whenLoaded('make', function () {
                return [
                    'id' => $this->make->id,
                    'name' => $this->make->name,
                ];
            }),
            'type' => $this->whenLoaded('type', function () {
                return [
                    'id' => $this->type->id,
                    'name' => $this->type->name,
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
