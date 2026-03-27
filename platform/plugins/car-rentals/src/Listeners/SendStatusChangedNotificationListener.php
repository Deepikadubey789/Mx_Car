<?php

namespace Botble\CarRentals\Listeners;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\EmailHandler;
use Botble\CarRentals\Events\BookingStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendStatusChangedNotificationListener implements ShouldQueue
{
    public function handle(BookingStatusChanged $event): void
    {
        $mailer = EmailHandler::setModule(CAR_RENTALS_MODULE_SCREEN_NAME)
            ->addTemplateSettings(CAR_RENTALS_MODULE_SCREEN_NAME, config('plugins.car-rentals.email', []));

        $booking = $event->booking;

        $mailer->setVariableValues([
            'booking_name' => $booking->customer_name,
            'booking_date' => BaseHelper::formatDateTime($booking->created_at),
            'booking_status' => $booking->status->label(),
            'booking_link' => route('customer.bookings.show', $booking->transaction_id),
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

        $mailer->sendUsingTemplate('booking-status-changed', $booking->customer_email);
    }
}
