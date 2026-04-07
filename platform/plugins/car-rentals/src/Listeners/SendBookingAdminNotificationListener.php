<?php

namespace Botble\CarRentals\Listeners;

use Botble\CarRentals\Events\BookingCreated;
use Illuminate\Support\Facades\DB;

class SendBookingAdminNotificationListener
{
    public function handle(BookingCreated $event): void
    {
        $booking = $event->booking;

        DB::table('admin_notifications')->insert([
            'title' => 'New Booking Received #' . $booking->booking_number,
            'description' => $booking->customer_name . ' booked ' . $booking->car->car_name,
            'action_label' => 'View Booking',
            'action_url' => route('car-rentals.bookings.edit', $booking->id),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}