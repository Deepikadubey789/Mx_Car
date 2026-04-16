<?php

namespace Botble\CarRentals\Notifications;

use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Schema;

class ClaimAssignmentNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected BookingClaim $claim,
        protected Booking $booking
    ) {
    }

    public function via(object $notifiable): array
    {
        return (Schema::hasTable('notifications') || Schema::hasTable('notification')) ? ['database'] : [];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Claim assigned',
            'message' => sprintf(
                'Claim #%d for booking %s is assigned to you.',
                $this->claim->id,
                $this->booking->booking_number ?: ('#' . $this->booking->id)
            ),
            'type' => 'claims-assignment',
            'action_url' => route('car-rentals.bookings.edit', $this->booking->id) . '#trip-timeline-casefile',
            'claim_id' => $this->claim->id,
            'booking_id' => $this->booking->id,
        ];
    }
}
