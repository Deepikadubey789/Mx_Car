<?php

namespace Botble\CarRentals\Listeners;

use Botble\Base\Facades\EmailHandler;
use Botble\CarRentals\Events\BookingCreated;
use Botble\CarRentals\Models\Customer;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBookingConfirmationEmailListener implements ShouldQueue
{
    public function handle(BookingCreated $event): void
    {
        $mailer = EmailHandler::setModule(CAR_RENTALS_MODULE_SCREEN_NAME);

        $booking = $event->booking;

        $mailer->setVariableValues([
            'booking_code' => $booking->booking_number,
            'customer_name' => $booking->customer_name,
            'customer_email' => $booking->customer_email,
            'customer_phone' => $booking->customer_phone,
            'car_name' => $booking->car->car_name,
            'payment_method' => $booking->payment ? $booking->payment->payment_channel->label() : 'N/A',
            'pickup_address' => $booking->car->pickup_address_text,
            'return_address' => $booking->car->return_address_text,
            'rental_start_date' => $booking->car->rental_start_date_formatted,
            'rental_end_date' => $booking->car->rental_end_date_formatted,
            'note' => $booking->note,
        ]);

        $mailer->sendUsingTemplate('booking-confirm', $booking->customer_email);
        $mailer->sendUsingTemplate('booking-notice-to-admin');

        if ($booking->car->vendor_id) {
            $vendor = Customer::query()->where('id', $booking->car->vendor_id)
                ->where('is_vendor', true)
                ->first();

            if ($vendor && $vendor->email) {
                $mailer->setVariableValues([
                    'vendor_name' => $vendor->name,
                    'booking_code' => $booking->booking_number,
                    'customer_name' => $booking->customer_name,
                    'customer_email' => $booking->customer_email,
                    'customer_phone' => $booking->customer_phone,
                    'car_name' => $booking->car->car_name,
                    'payment_method' => $booking->payment ? $booking->payment->payment_channel->label() : 'N/A',
                    'pickup_address' => $booking->car->pickup_address_text,
                    'return_address' => $booking->car->return_address_text,
                    'rental_start_date' => $booking->car->rental_start_date->format('M d, Y H:i'),
                    'rental_end_date' => $booking->car->rental_end_date->format('M d, Y H:i'),
                    'note' => $booking->note,
                ]);

                $mailer->sendUsingTemplate('booking-notice-to-vendor', $vendor->email);
            }
        }
    }
}
