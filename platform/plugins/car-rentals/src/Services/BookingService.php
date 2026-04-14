<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\Customer;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $car = Car::query()->find($data['car_id']);

            // Calculate rental days
            $pickupDate = $data['pickup_date'] instanceof Carbon ? $data['pickup_date'] : Carbon::parse($data['pickup_date']);
            $returnDate = $data['return_date'] instanceof Carbon ? $data['return_date'] : Carbon::parse($data['return_date']);
            $rentalDays = max(1, $pickupDate->diffInDays($returnDate));
            $serviceIds = array_values(array_unique($data['services'] ?? []));

            $customer = ! empty($data['customer_id'])
                ? Customer::query()->find($data['customer_id'])
                : null;

            $quoteData = app(PricingQuoteService::class)->buildQuote(
                $car,
                $pickupDate,
                $returnDate,
                $serviceIds,
                [],
                $data['coupon_code'] ?? null,
                $customer
            );
            $eligibility = app(DriverEligibilityService::class)->evaluate($car, $customer, $quoteData['deposit_risk'] ?? null);

            if ($eligibility['state'] === 'blocked') {
                throw new \RuntimeException('Driver verification failed for this vehicle category.');
            }

            $couponAmount = (float) ($quoteData['coupon_amount'] ?? 0);
            $couponCode = ! empty($data['coupon_code']) && $couponAmount > 0 ? $data['coupon_code'] : null;

            // Create booking
            $booking = Booking::create([
                'customer_id' => $data['customer_id'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_address' => $data['customer_address'] ?? null,
                'customer_zip_code' => $data['customer_zip_code'] ?? null,
                'car_id' => $car->id,
                'vendor_id' => $car->vendor_id,
                'pickup_date' => $pickupDate,
                'return_date' => $returnDate,
                'pickup_time' => $data['pickup_time'] ?? null,
                'return_time' => $data['return_time'] ?? null,
                'pickup_location_id' => $data['pickup_location_id'] ?? null,
                'return_location_id' => get_car_rentals_setting('disable_one_way_rental', false)
                    ? ($data['pickup_location_id'] ?? null)
                    : ($data['return_location_id'] ?? null),
                'number_of_days' => $rentalDays,
                'sub_total' => (float) $quoteData['subtotal'],
                'amount' => (float) $quoteData['final_payable_amount'],
                'discount_amount' => $couponAmount,
                'coupon_code' => $couponCode,
                'coupon_amount' => $couponAmount,
                'tax_amount' => (float) $quoteData['tax_amount'],
                'fee_name' => (string) $quoteData['fee_name'],
                'fee_value' => (float) $quoteData['fee_value'],
                'fee_amount' => (float) $quoteData['fee_amount'],
                'deposit_base_amount' => (float) $quoteData['deposit_base_amount'],
                'deposit_amount' => (float) $quoteData['deposit_amount'],
                'deposit_type' => (string) $quoteData['deposit_type'],
                'deposit_rate' => (float) $quoteData['deposit_rate'],
                'deposit_risk_multiplier' => (float) data_get($quoteData, 'deposit_risk.multiplier', 1),
                'deposit_risk_level' => (string) data_get($quoteData, 'deposit_risk.risk_level', 'low'),
                'deposit_risk_reasons' => data_get($quoteData, 'deposit_risk.reasons', []),
                'eligibility_state' => $eligibility['state'],
                'eligibility_reasons' => $eligibility['reasons'],
                'kyc_verification_id' => $customer?->kyc_current_verification_id,
                'price_snapshot' => [
                    'rental_days' => (int) ($quoteData['rental_days'] ?? 1),
                    'base_rental_amount' => (float) ($quoteData['base_rental_amount'] ?? 0),
                    'policy_discount_amount' => (float) ($quoteData['policy_discount_amount'] ?? 0),
                    'policy_discount_pre_cap_amount' => (float) ($quoteData['policy_discount_pre_cap_amount'] ?? 0),
                    'policy_discount_capped' => (bool) ($quoteData['policy_discount_capped'] ?? false),
                    'policy_discount_cap_percent' => $quoteData['policy_discount_cap_percent'] !== null
                        ? (float) $quoteData['policy_discount_cap_percent']
                        : null,
                    'policy_discount_source' => (string) ($quoteData['policy_discount_source'] ?? ''),
                    'deposit_risk' => [
                        'enabled' => (bool) data_get($quoteData, 'deposit_risk.enabled', false),
                        'risk_level' => (string) data_get($quoteData, 'deposit_risk.risk_level', 'low'),
                        'multiplier' => (float) data_get($quoteData, 'deposit_risk.multiplier', 1),
                        'category_multiplier' => (float) data_get($quoteData, 'deposit_risk.category_multiplier', 1),
                        'type_multiplier' => (float) data_get($quoteData, 'deposit_risk.type_multiplier', 1),
                        'applied_vehicle_multiplier' => (float) data_get($quoteData, 'deposit_risk.applied_vehicle_multiplier', 1),
                        'reasons' => (array) data_get($quoteData, 'deposit_risk.reasons', []),
                    ],
                    'eligibility' => [
                        'state' => (string) $eligibility['state'],
                        'reasons' => (array) $eligibility['reasons'],
                    ],
                ],
                'distance_unit' => (string) ($quoteData['distance_unit'] ?? 'km'),
                'start_mileage' => $car->mileage !== null ? (int) $car->mileage : null,
                'start_mileage_snapshot' => $car->mileage !== null ? (int) $car->mileage : null,
                'included_distance_limit' => $quoteData['included_distance_limit'] !== null
                    ? (int) $quoteData['included_distance_limit']
                    : null,
                'distance_overage_billing_mode' => (string) ($quoteData['distance_overage_billing_mode'] ?? 'end_of_trip'),
                'extra_distance_unit_price' => (float) ($quoteData['extra_distance_unit_price'] ?? 0),
                'currency_id' => get_application_currency_id(),
                'note' => $data['note'] ?? null,
                'status' => BookingStatusEnum::PENDING,
                'code' => 'BK' . str_pad(Booking::query()->count() + 1, 6, '0', STR_PAD_LEFT),
            ]);

            // Attach services
            if (! empty($serviceIds)) {
                $booking->services()->sync($serviceIds);
            }

            // Load relationships
            $booking->load(['car.car', 'services', 'currency']);

            return $booking;
        });
    }

    public function processBooking(int $bookingId, ?string $chargeId = null): ?Booking
    {
        /**
         * @var Booking $booking
         */
        $booking = Booking::query()->find($bookingId);

        if (! $booking) {
            return null;
        }

        // Set vendor_id if not already set
        if (! $booking->vendor_id && $booking->car && $booking->car->car) {
            $car = $booking->car->car;
            if ($car->vendor_id) {
                $booking->vendor_id = $car->vendor_id;
                $booking->save();
            }
        }

        session()->put('booking_transaction_id', $booking->transaction_id);

        if ($chargeId && is_plugin_active('payment')) {
            $payment = Payment::query()->where(['charge_id' => $chargeId])->first();

            if ($payment) {
                $booking->payment_id = $payment->getKey();

                if ($payment->status == PaymentStatusEnum::COMPLETED) {
                    $booking->status = BookingStatusEnum::PROCESSING;
                }

                $booking->save();

                if ($booking->invoice()->exists()) {
                    $booking->invoice()->update([
                        'payment_id' => $payment->id,
                        'paid_at' => $payment->status == PaymentStatusEnum::COMPLETED ? Carbon::now() : null,
                    ]);
                }
            }
        }

        return $booking;
    }
}
