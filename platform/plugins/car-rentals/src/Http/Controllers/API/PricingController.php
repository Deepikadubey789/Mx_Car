<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\DeliveryLocation;
use Botble\CarRentals\Services\BookingService;
use Botble\CarRentals\Services\PricingQuoteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PricingController extends BaseApiController
{
    public function __construct(protected BookingService $bookingService)
    {
    }

    /**
     * Calculate rental pricing
     *
     * @group Car Rentals - Pricing
     */
    public function calculate(Request $request)
    {
        try {
            $validated = $request->validate([
                'car_id' => ['required', 'exists:cr_cars,id'],
                'pickup_date' => ['required', 'date', 'after_or_equal:today'],
                'return_date' => ['required', 'date', 'after:pickup_date'],
                'pickup_location_id' => ['nullable', 'exists:cities,id'],
                'return_location_id' => ['nullable', 'exists:cities,id'],
                'services' => ['nullable', 'array'],
                'services.*' => ['exists:cr_services,id'],
                'coupon_code' => ['nullable', 'string'],
                // NEW: Validate the delivery location
                'delivery_location_id' => ['nullable', 'exists:cr_delivery_locations,id'],
            ]);
        } catch (ValidationException $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(422)
                ->setMessage('Validation failed')
                ->setData(['errors' => $e->errors()])
                ->toApiResponse();
        }

        // NEW: Eager load the delivery locations
        $car = Car::with('deliveryLocations')->find($validated['car_id']);

        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car not found')
                ->toApiResponse();
        }

        $pickupDate = Carbon::parse($validated['pickup_date']);
        $returnDate = Carbon::parse($validated['return_date']);

        $days = max(1, $pickupDate->diffInDays($returnDate));

        // Let the existing service build the base quote
        $quoteData = app(PricingQuoteService::class)->buildQuote(
            $car,
            $pickupDate,
            $returnDate,
            $validated['services'] ?? [],
            [],
            $validated['coupon_code'] ?? null,
            null
        );

        $selectedServices = collect($quoteData['services'])->map(function ($service) use ($days) {
            $serviceTotal = $service->price_type == 'per_day'
                ? ((float) $service->price * $days)
                : (float) $service->price;

            return [
                'id' => $service->id,
                'name' => $service->name,
                'price' => (float) $service->price,
                'price_type' => $service->price_type?->getValue(),
                'total' => round($serviceTotal, 2),
            ];
        })->values()->all();

        // --- NEW: Delivery Pricing Logic ---
        $deliveryFee = 0.00;
        $deliveryMessage = 'No delivery requested.';
        $isFreeDeliveryEarned = false;

        if (!empty($validated['delivery_location_id']) && $car->is_delivery_enabled) {
            $requestedZone = DeliveryLocation::find($validated['delivery_location_id']);
            
            if ($requestedZone && $car->deliveryLocations->contains('id', $requestedZone->id)) {
                $deliveryFee = (float) $requestedZone->fee_amount;
                $deliveryMessage = "Delivery to {$requestedZone->name}";

                // Check Free Threshold
                if ($car->free_delivery_days_threshold && $days >= $car->free_delivery_days_threshold) {
                    $deliveryFee = 0.00;
                    $isFreeDeliveryEarned = true;
                    $deliveryMessage = "Delivery to {$requestedZone->name} (Free for {$car->free_delivery_days_threshold}+ days)";
                }
            }
        }
        // -----------------------------------

        $policyDiscount = (float) ($quoteData['policy_discount_amount'] ?? 0);
        $couponDiscount = (float) ($quoteData['coupon_amount'] ?? 0);
        $couponDetails = ! empty($validated['coupon_code'])
            ? [
                'code' => $validated['coupon_code'],
                'amount' => round($couponDiscount, 2),
            ]
            : null;

        $isAvailable = $car->isAvailableAt([
            'start_date' => Carbon::parse($validated['pickup_date']),
            'end_date' => Carbon::parse($validated['return_date']),
        ]);

        $response = [
            'car' => [
                'id' => $car->id,
                'name' => $car->name,
                'price_per_day' => $car->price,
            ],
            'rental_period' => [
                'pickup_date' => $pickupDate->format('Y-m-d'),
                'return_date' => $returnDate->format('Y-m-d'),
                'days' => $days,
            ],
            'pricing' => [
                'base_price' => round((float) $quoteData['rental_amount'], 2),
                'services_total' => round((float) $quoteData['service_amount'], 2),
                'subtotal' => round((float) $quoteData['subtotal'], 2),
                'policy_discount' => round($policyDiscount, 2),
                'tax_total' => round((float) $quoteData['tax_amount'], 2),
                'coupon_discount' => round($couponDiscount, 2),
                'fee_amount' => round((float) $quoteData['fee_amount'], 2),
                'deposit_amount' => round((float) $quoteData['deposit_amount'], 2),
                
                // NEW: Inject delivery fee into the pricing block
                'delivery_fee' => round($deliveryFee, 2),
                
                // NEW: Add delivery fee to the final payable total
                'total' => round((float) $quoteData['final_payable_amount'] + $deliveryFee, 2),
            ],
            // NEW: Add a delivery details block for the frontend UI
            'delivery' => [
                'message' => $deliveryMessage,
                'is_free_delivery_earned' => $isFreeDeliveryEarned,
            ],
            'services' => $selectedServices,
            'taxes' => [],
            'coupon' => $couponDetails,
            'availability' => [
                'is_available' => $isAvailable,
                'message' => $isAvailable ? 'Car is available for selected dates' : 'Car is not available for selected dates',
            ],
            'meta' => [
                'demand_recommendations' => $quoteData['demand_recommendations'] ?? [],
            ],
        ];

        return $this
            ->httpResponse()
            ->setData($response)
            ->toApiResponse();
    }
}<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\DeliveryLocation;
use Botble\CarRentals\Services\BookingService;
use Botble\CarRentals\Services\PricingQuoteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PricingController extends BaseApiController
{
    public function __construct(protected BookingService $bookingService)
    {
    }

    /**
     * Calculate rental pricing
     *
     * @group Car Rentals - Pricing
     */
    public function calculate(Request $request)
    {
        try {
            $validated = $request->validate([
                'car_id' => ['required', 'exists:cr_cars,id'],
                'pickup_date' => ['required', 'date', 'after_or_equal:today'],
                'return_date' => ['required', 'date', 'after:pickup_date'],
                'pickup_location_id' => ['nullable', 'exists:cities,id'],
                'return_location_id' => ['nullable', 'exists:cities,id'],
                'services' => ['nullable', 'array'],
                'services.*' => ['exists:cr_services,id'],
                'coupon_code' => ['nullable', 'string'],
                // NEW: Validate the delivery location
                'delivery_location_id' => ['nullable', 'exists:cr_delivery_locations,id'],
            ]);
        } catch (ValidationException $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(422)
                ->setMessage('Validation failed')
                ->setData(['errors' => $e->errors()])
                ->toApiResponse();
        }

        // NEW: Eager load the delivery locations
        $car = Car::with('deliveryLocations')->find($validated['car_id']);

        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car not found')
                ->toApiResponse();
        }

        $pickupDate = Carbon::parse($validated['pickup_date']);
        $returnDate = Carbon::parse($validated['return_date']);

        $days = max(1, $pickupDate->diffInDays($returnDate));

        // Let the existing service build the base quote
        $quoteData = app(PricingQuoteService::class)->buildQuote(
            $car,
            $pickupDate,
            $returnDate,
            $validated['services'] ?? [],
            [],
            $validated['coupon_code'] ?? null,
            null
        );

        $selectedServices = collect($quoteData['services'])->map(function ($service) use ($days) {
            $serviceTotal = $service->price_type == 'per_day'
                ? ((float) $service->price * $days)
                : (float) $service->price;

            return [
                'id' => $service->id,
                'name' => $service->name,
                'price' => (float) $service->price,
                'price_type' => $service->price_type?->getValue(),
                'total' => round($serviceTotal, 2),
            ];
        })->values()->all();

        // --- NEW: Delivery Pricing Logic ---
        $deliveryFee = 0.00;
        $deliveryMessage = 'No delivery requested.';
        $isFreeDeliveryEarned = false;

        if (!empty($validated['delivery_location_id']) && $car->is_delivery_enabled) {
            $requestedZone = DeliveryLocation::find($validated['delivery_location_id']);
            
            if ($requestedZone && $car->deliveryLocations->contains('id', $requestedZone->id)) {
                $deliveryFee = (float) $requestedZone->fee_amount;
                $deliveryMessage = "Delivery to {$requestedZone->name}";

                // Check Free Threshold
                if ($car->free_delivery_days_threshold && $days >= $car->free_delivery_days_threshold) {
                    $deliveryFee = 0.00;
                    $isFreeDeliveryEarned = true;
                    $deliveryMessage = "Delivery to {$requestedZone->name} (Free for {$car->free_delivery_days_threshold}+ days)";
                }
            }
        }
        // -----------------------------------

        $policyDiscount = (float) ($quoteData['policy_discount_amount'] ?? 0);
        $couponDiscount = (float) ($quoteData['coupon_amount'] ?? 0);
        $couponDetails = ! empty($validated['coupon_code'])
            ? [
                'code' => $validated['coupon_code'],
                'amount' => round($couponDiscount, 2),
            ]
            : null;

        $isAvailable = $car->isAvailableAt([
            'start_date' => Carbon::parse($validated['pickup_date']),
            'end_date' => Carbon::parse($validated['return_date']),
        ]);

        $response = [
            'car' => [
                'id' => $car->id,
                'name' => $car->name,
                'price_per_day' => $car->price,
            ],
            'rental_period' => [
                'pickup_date' => $pickupDate->format('Y-m-d'),
                'return_date' => $returnDate->format('Y-m-d'),
                'days' => $days,
            ],
            'pricing' => [
                'base_price' => round((float) $quoteData['rental_amount'], 2),
                'services_total' => round((float) $quoteData['service_amount'], 2),
                'subtotal' => round((float) $quoteData['subtotal'], 2),
                'policy_discount' => round($policyDiscount, 2),
                'tax_total' => round((float) $quoteData['tax_amount'], 2),
                'coupon_discount' => round($couponDiscount, 2),
                'fee_amount' => round((float) $quoteData['fee_amount'], 2),
                'deposit_amount' => round((float) $quoteData['deposit_amount'], 2),
                
                // NEW: Inject delivery fee into the pricing block
                'delivery_fee' => round($deliveryFee, 2),
                
                // NEW: Add delivery fee to the final payable total
                'total' => round((float) $quoteData['final_payable_amount'] + $deliveryFee, 2),
            ],
            // NEW: Add a delivery details block for the frontend UI
            'delivery' => [
                'message' => $deliveryMessage,
                'is_free_delivery_earned' => $isFreeDeliveryEarned,
            ],
            'services' => $selectedServices,
            'taxes' => [],
            'coupon' => $couponDetails,
            'availability' => [
                'is_available' => $isAvailable,
                'message' => $isAvailable ? 'Car is available for selected dates' : 'Car is not available for selected dates',
            ],
            'meta' => [
                'demand_recommendations' => $quoteData['demand_recommendations'] ?? [],
            ],
        ];

        return $this
            ->httpResponse()
            ->setData($response)
            ->toApiResponse();
    }
}