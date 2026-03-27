<?php

namespace Database\Seeders\Themes\Main;

use Botble\Base\Supports\BaseSeeder;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Enums\RevenueTypeEnum;
use Botble\CarRentals\Facades\InvoiceHelper;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingCar;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Models\Revenue;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingSeeder extends BaseSeeder
{
    public function run(): void
    {
        Booking::query()->truncate();
        BookingCar::query()->truncate();
        DB::table('cr_booking_service')->truncate();
        DB::table('cr_invoice_items')->truncate();
        DB::table('cr_invoices')->truncate();
        Revenue::query()->truncate();

        Payment::query()->truncate();

        $customers = Customer::query()->where('is_vendor', false)->get();
        $currencyId = get_application_currency()->getKey();
        $vendorIds = Customer::query()->where('is_vendor', true)->pluck('id');

        // Get cars grouped by vendor
        $vendorCars = Car::query()->whereNotNull('vendor_id')->get()->groupBy('vendor_id');
        $nonVendorCars = Car::query()->whereNull('vendor_id')->get();

        $total = 80; // Increase total bookings for better demo data

        for ($i = 0; $i < $total; $i++) {
            $customer = $customers->random();

            // 70% chance of booking vendor cars
            if ($i % 10 <= 6 && $vendorCars->isNotEmpty()) {
                // Pick a random vendor and their car
                $vendorId = $vendorIds->random();
                if ($vendorCars->has($vendorId) && $vendorCars[$vendorId]->isNotEmpty()) {
                    $car = $vendorCars[$vendorId]->random();
                } else {
                    // Fallback to any vendor car
                    $car = $vendorCars->flatten()->random();
                    $vendorId = $car->vendor_id;
                }
            } else {
                // Book non-vendor cars
                $car = $nonVendorCars->isNotEmpty() ? $nonVendorCars->random() : $vendorCars->flatten()->random();
                $vendorId = $car->vendor_id;
            }

            // Generate booking dates - mix of past, current, and future bookings
            if ($i < 30) {
                // Past bookings (completed)
                $time = Carbon::now()->subDays(rand(1, 60));
                $status = Arr::random([
                    BookingStatusEnum::COMPLETED,
                    BookingStatusEnum::COMPLETED,
                    BookingStatusEnum::COMPLETED,
                    BookingStatusEnum::CANCELLED,
                ]);
            } elseif ($i < 50) {
                // Current/recent bookings (in progress)
                $time = Carbon::now()->subDays(rand(0, 7));
                $status = Arr::random([
                    BookingStatusEnum::PROCESSING,
                    BookingStatusEnum::PROCESSING,
                    BookingStatusEnum::COMPLETED,
                ]);
            } else {
                // Future bookings (pending/processing)
                $time = Carbon::now()->addDays(rand(1, 30));
                $status = Arr::random([
                    BookingStatusEnum::PENDING,
                    BookingStatusEnum::PROCESSING,
                    BookingStatusEnum::PROCESSING,
                ]);
            }

            $rentalDays = rand(1, 7);
            $rentalRate = $car->rental_rate ?? rand(30, 150);
            $totalAmount = $rentalRate * $rentalDays;

            $bookingCar = new BookingCar([
                'car_id' => $car->id,
                'car_name' => $car->name,
                'price' => $totalAmount,
                'currency_id' => $currencyId,
                'rental_start_date' => $time->toDateString(),
                'rental_end_date' => $time->clone()->addDays($rentalDays)->toDateString(),
            ]);

            $booking = Booking::query()->forceCreate([
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'transaction_id' => Str::upper(Str::random(32)),
                'amount' => $totalAmount,
                'sub_total' => $totalAmount,
                'tax_amount' => rand(1, 100) <= 30 ? round($totalAmount * 0.1, 2) : 0,
                'currency_id' => $currencyId,
                'status' => $status,
                'vendor_id' => $vendorId,
                'created_at' => $time,
                'updated_at' => $time,
            ]);

            $bookingCar->booking()->associate($booking)->save();

            $booking->update([
                'booking_number' => Booking::generateUniqueBookingNumber(),
            ]);

            $payment = Payment::query()->create([
                'amount' => $booking->amount,
                'currency' => 'USD',
                'user_id' => $customer->id,
                'charge_id' => strtoupper(Str::random(12)),
                'payment_channel' => Arr::random(PaymentMethodEnum::values()),
                'status' => $booking->status->getValue() === BookingStatusEnum::COMPLETED ? PaymentStatusEnum::COMPLETED : PaymentStatusEnum::PENDING,
                'order_id' => $booking->getKey(),
                'payment_type' => 'direct',
                'customer_id' => $customer->id,
                'customer_type' => $customer::class,
                'created_at' => $time,
                'updated_at' => $time,
            ]);

            $booking->payment()->associate($payment)->save();

            InvoiceHelper::store($booking);

            // Create revenue record and update vendor balance for completed bookings with vendors
            if ($vendorId && $status === BookingStatusEnum::COMPLETED) {
                $vendor = Customer::query()->find($vendorId);
                if ($vendor) {
                    // Calculate commission fee
                    $bookingAmountWithoutTax = $booking->amount - $booking->tax_amount;

                    // Use default commission rate (20% by default)
                    $commissionRate = 20; // You can adjust this or get from settings
                    $commissionFee = $bookingAmountWithoutTax * ($commissionRate / 100);

                    // Calculate vendor earnings
                    $vendorEarnings = $bookingAmountWithoutTax - $commissionFee;

                    // Create revenue record
                    Revenue::query()->create([
                        'customer_id' => $vendor->id,
                        'booking_id' => $booking->id,
                        'sub_amount' => $bookingAmountWithoutTax,
                        'fee' => $commissionFee,
                        'amount' => $vendorEarnings,
                        'current_balance' => $vendor->balance + $vendorEarnings,
                        'currency' => 'USD',
                        'description' => 'Earnings from booking #' . $booking->booking_number . ' for ' . $car->name,
                        'type' => RevenueTypeEnum::BOOKING_COMPLETED,
                        'created_at' => $time,
                        'updated_at' => $time,
                    ]);

                    // Update vendor balance
                    $vendor->balance += $vendorEarnings;
                    $vendor->save();
                }
            }
        }
    }
}
