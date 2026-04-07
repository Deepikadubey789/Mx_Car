<?php

namespace Botble\CarRentals\Commands;

use Botble\CarRentals\Models\BookingCar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendReturnAlertCommand extends Command
{
    protected $signature = 'car-rentals:send-return-alerts';
    protected $description = 'Send return alert emails to customers on return date';

    public function handle(): int
    {
        $today = Carbon::today()->format('Y-m-d');

        $bookings = BookingCar::query()
            ->whereDate('rental_end_date', $today)
            ->with('booking')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No returns today — no alerts sent.');
            return self::SUCCESS;
        }

        foreach ($bookings as $bookingCar) {
            $booking = $bookingCar->booking;

            if (!$booking || !$booking->customer_email) {
                continue;
            }

            $data = [
                'customer_name'     => $booking->customer_name,
                'booking_code'      => $booking->booking_number,
                'car_name'          => $bookingCar->car_name,
                'rental_start_date' => Carbon::parse($bookingCar->rental_start_date)->format('M d, Y'),
                'rental_end_date'   => Carbon::parse($bookingCar->rental_end_date)->format('M d, Y'),
                'customer_phone'    => $booking->customer_phone,
                'customer_email'    => $booking->customer_email,
            ];

            Mail::send([], $data, function ($message) use ($booking, $data) {
                $message->to($booking->customer_email)
                    ->subject('Return Reminder: Please Return Your Car Today!')
                    ->html($this->buildHtml($data));
            });

            $this->info('Return alert sent to: ' . $booking->customer_email);
        }

        $this->info('All return alerts sent!');
        return self::SUCCESS;
    }

    private function buildHtml(array $data): string
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; border-radius: 8px;">
            <h2 style="color: #e74c3c; text-align: center;">🔔 Car Return Reminder!</h2>
            <p>Dear <strong>' . e($data['customer_name']) . '</strong>,</p>
            <p>This is a reminder that your car rental period ends <strong>today</strong>. Please return the car at the agreed time and location.</p>
            <hr>
            <h3>Booking Details</h3>
            <p><strong>Booking #:</strong> ' . e($data['booking_code']) . '</p>
            <p><strong>Car:</strong> ' . e($data['car_name']) . '</p>
            <p><strong>Pickup Date:</strong> ' . e($data['rental_start_date']) . '</p>
            <p><strong>Return Date:</strong> ' . e($data['rental_end_date']) . '</p>
            <hr>
            <h3>Customer Details</h3>
            <p><strong>Name:</strong> ' . e($data['customer_name']) . '</p>
            <p><strong>Phone:</strong> ' . e($data['customer_phone']) . '</p>
            <p><strong>Email:</strong> ' . e($data['customer_email']) . '</p>
            <br>
            <p style="color: #888; font-size: 12px; text-align: center;">©' . date('Y') . ' MxCar App. All Rights Reserved.</p>
        </div>';
    }
}