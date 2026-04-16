<?php

namespace Botble\CarRentals\Notifications;

use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Schema;

class ClaimSlaBreachNotification extends Notification
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
            'title' => 'Claim SLA breach',
            'message' => sprintf(
                'Claim #%d for booking %s has crossed its SLA due time.',
                $this->claim->id,
                $this->booking->booking_number ?: ('#' . $this->booking->id)
            ),
            'type' => 'claims-sla-breach',
            'action_url' => route('car-rentals.bookings.claims.index', [
                'status' => $this->claim->status,
            ]),
            'claim_id' => $this->claim->id,
            'booking_id' => $this->booking->id,
        ];
    }
}
