<?php

namespace Botble\CarRentals\Notifications\Concerns;

use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Schema;

trait BuildsClaimPublicNotificationPayload
{
    protected function availableChannels(): array
    {
        $channels = ['mail'];

        if (Schema::hasTable('notifications') || Schema::hasTable('notification')) {
            $channels[] = 'database';
        }

        return $channels;
    }

    protected function bookingLabel(Booking $booking): string
    {
        return (string) ($booking->booking_number ?: ('#' . $booking->id));
    }

    protected function claimStatusLabel(BookingClaim $claim): string
    {
        return str((string) $claim->status)->replace('_', ' ')->title()->toString();
    }

    protected function claimCategoryLabel(BookingClaim $claim): string
    {
        return (string) ($claim->category ?: 'General');
    }

    protected function baseDatabasePayload(Booking $booking, BookingClaim $claim, string $title, string $message, string $type): array
    {
        return [
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'booking_id' => $booking->id,
            'claim_id' => $claim->id,
        ];
    }

    protected function makeMailMessage(object $notifiable, string $subject, string $intro, Booking $booking, BookingClaim $claim, ?string $extraLine = null): MailMessage
    {
        $name = $notifiable->name ?? trans('plugins/car-rentals::disputes.notification_default_recipient');

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $name . ',')
            ->line($intro)
            ->line(trans('plugins/car-rentals::disputes.notification_booking_label', ['booking' => $this->bookingLabel($booking)]))
            ->line(trans('plugins/car-rentals::disputes.notification_claim_category_label', ['category' => $this->claimCategoryLabel($claim)]))
            ->line(trans('plugins/car-rentals::disputes.notification_claim_status_label', ['status' => $this->claimStatusLabel($claim)]));

        if ($extraLine) {
            $message->line($extraLine);
        }

        return $message;
    }
}
