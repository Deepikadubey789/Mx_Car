<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Models\Car;

class DeliveryController extends BaseApiController
{
    /**
     * Get delivery options for a specific car
     *
     * @group Car Rentals - Public
     */
    public function getCarDeliveryOptions(int $id)
    {
        // Find the car and eager load its attached delivery locations
        $car = Car::query()->with('deliveryLocations')->findOrFail($id);

        // If delivery is disabled or no locations are attached, return an empty array safely
        if (!$car->is_delivery_enabled || $car->deliveryLocations->count() === 0) {
            return response()->json([
                'error' => false,
                'data' => [],
                'free_delivery_days_threshold' => null,
                'max_delivery_distance_miles' => null,
            ]);
        }

        // Map through the locations and format them for the mobile app
        $locations = $car->deliveryLocations->map(function ($location) {
            // Determine if this specific option requires a custom text address
            // by looking for the words "custom" or "address" in the zone name
            $requiresAddress = (stripos($location->name, 'custom') !== false || stripos($location->name, 'address') !== false);

            return [
                'id' => $location->id,
                'name' => $location->name,
                'fee_amount' => (float) $location->fee_amount,
                'formatted_fee' => format_price($location->fee_amount),
                'requires_custom_address' => $requiresAddress,
            ];
        });

        // Return the formatted JSON response to the mobile developers
        return response()->json([
            'error' => false,
            'data' => $locations,
            'free_delivery_days_threshold' => $car->free_delivery_days_threshold,
            'max_delivery_distance_miles' => $car->max_delivery_distance_miles,
        ]);
    }
}