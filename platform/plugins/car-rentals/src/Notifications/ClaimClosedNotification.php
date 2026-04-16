<?php

namespace Botble\CarRentals\Notifications;

use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Botble\CarRentals\Notifications\Concerns\BuildsClaimPublicNotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClaimClosedNotification extends Notification
{
    use Queueable;
    use BuildsClaimPublicNotificationPayload;

    public function __construct(
        protected BookingClaim $claim,
        protected Booking $booking
    ) {
    }

    public function via(object $notifiable): array
    {
        return $this->availableChannels();
    }

    public function toMail(object $notifiable): MailMessage
    {
        return $this->makeMailMessage(
            $notifiable,
            trans('plugins/car-rentals::disputes.notification_claim_closed_subject'),
            trans('plugins/car-rentals::disputes.notification_claim_closed_intro'),
            $this->booking,
            $this->claim,
            $this->claim->approved_amount !== null
                ? trans('plugins/car-rentals::disputes.notification_claim_closed_amount', ['amount' => (string) $this->claim->approved_amount])
                : null
        );
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->baseDatabasePayload(
            $this->booking,
            $this->claim,
            trans('plugins/car-rentals::disputes.notification_claim_closed_title'),
            trans('plugins/car-rentals::disputes.notification_claim_closed_message', [
                'booking' => $this->bookingLabel($this->booking),
                'status' => $this->claimStatusLabel($this->claim),
            ]),
            'claim-closed'
        );
    }
}
