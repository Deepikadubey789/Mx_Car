<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Enums\ServicePriceTypeEnum;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\Coupon;
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
            $rentalDays = $pickupDate->diffInDays($returnDate) + 1;

            // Calculate base price
            $basePrice = $car->getCarRentalPrice($pickupDate->format('Y-m-d'), $returnDate->format('Y-m-d'));

            // Calculate services cost
            $servicesCost = 0;
            $services = [];
            if (! empty($data['services'])) {
                $servicesCollection = $car->services()->whereIn('id', $data['services'])->get();
                foreach ($servicesCollection as $service) {
                    if ($service->price_type == ServicePriceTypeEnum::PER_DAY) {
                        $servicesCost += $service->price * $rentalDays;
                    } else {
                        $servicesCost += $service->price;
                    }
                    $services[] = $service->id;
                }
            }

            // Apply coupon if provided
            $discount = 0;
            $couponId = null;
            if (! empty($data['coupon_code'])) {
                $coupon = Coupon::query()
                    ->where('code', $data['coupon_code'])
                    ->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where(function ($query): void {
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>=', now());
                    })
                    ->first();

                if ($coupon && $coupon->isValid()) {
                    $couponId = $coupon->id;
                    if ($coupon->type === 'percentage') {
                        $discount = ($basePrice + $servicesCost) * ($coupon->value / 100);
                    } else {
                        $discount = min($coupon->value, $basePrice + $servicesCost);
                    }
                }
            }

            // Calculate total amount
            $totalAmount = $basePrice + $servicesCost - $discount;

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
                'sub_total' => $basePrice,
                'amount' => $totalAmount,
                'discount_amount' => $discount,
                'coupon_id' => $couponId,
                'currency_id' => get_application_currency_id(),
                'note' => $data['note'] ?? null,
                'status' => BookingStatusEnum::PENDING,
                'code' => 'BK' . str_pad(Booking::query()->count() + 1, 6, '0', STR_PAD_LEFT),
            ]);

            // Attach services
            if (! empty($services)) {
                $booking->services()->sync($services);
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
