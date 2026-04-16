<?php

namespace Botble\CarRentals\Notifications;

use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Botble\CarRentals\Notifications\Concerns\BuildsClaimPublicNotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClaimDocsRequestedNotification extends Notification
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
            trans('plugins/car-rentals::disputes.notification_claim_docs_requested_subject'),
            trans('plugins/car-rentals::disputes.notification_claim_docs_requested_intro'),
            $this->booking,
            $this->claim
        );
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->baseDatabasePayload(
            $this->booking,
            $this->claim,
            trans('plugins/car-rentals::disputes.notification_claim_docs_requested_title'),
            trans('plugins/car-rentals::disputes.notification_claim_docs_requested_message', [
                'booking' => $this->bookingLabel($this->booking),
            ]),
            'claim-docs-requested'
        );
    }
}
