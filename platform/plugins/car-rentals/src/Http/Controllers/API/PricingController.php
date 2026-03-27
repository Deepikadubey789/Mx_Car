<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\Service;
use Botble\CarRentals\Models\Tax;
use Botble\CarRentals\Services\BookingService;
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

        $car = Car::query()->find($validated['car_id']);

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

        $days = abs($returnDate->diffInDays($pickupDate) ?: 1);

        $basePrice = $car->getCarRentalPrice($pickupDate->format('Y-m-d'), $returnDate->format('Y-m-d'));

        $servicesTotal = 0;
        $selectedServices = [];

        if (! empty($validated['services'])) {
            $services = Service::query()->whereIn('id', $validated['services'])->get();

            foreach ($services as $service) {
                $serviceCost = $service->price;

                if ($service->price_type == 'per_day') {
                    $serviceCost *= $days;
                }

                $servicesTotal += $serviceCost;
                $selectedServices[] = [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'price_type' => $service->price_type,
                    'total' => $serviceCost,
                ];
            }
        }

        $subtotal = $basePrice + $servicesTotal;

        $taxTotal = 0;
        $appliedTaxes = [];

        if (CarRentalsHelper::isTaxEnabled()) {
            $taxes = CarRentalsHelper::getAppliedTaxes();
            foreach ($taxes as $tax) {
                $taxAmount = ($subtotal * $tax->percentage) / 100;
                $taxTotal += $taxAmount;

                $appliedTaxes[] = [
                    'id' => $tax->id,
                    'title' => $tax->title,
                    'percentage' => $tax->percentage,
                    'amount' => round($taxAmount, 2),
                ];
            }
        }

        $couponDiscount = 0;
        $couponDetails = null;

        if (! empty($validated['coupon_code'])) {
            // TODO: Implement coupon validation and discount calculation
            // This would require a CouponService to validate and calculate discount
        }

        $total = $subtotal + $taxTotal - $couponDiscount;

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
                'base_price' => round($basePrice, 2),
                'services_total' => round($servicesTotal, 2),
                'subtotal' => round($subtotal, 2),
                'tax_total' => round($taxTotal, 2),
                'coupon_discount' => round($couponDiscount, 2),
                'total' => round($total, 2),
            ],
            'services' => $selectedServices,
            'taxes' => $appliedTaxes,
            'coupon' => $couponDetails,
            'availability' => [
                'is_available' => $isAvailable,
                'message' => $isAvailable ? 'Car is available for selected dates' : 'Car is not available for selected dates',
            ],
        ];

        return $this
            ->httpResponse()
            ->setData($response)
            ->toApiResponse();
    }
}
