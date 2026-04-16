<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Botble\CarRentals\Notifications\ClaimClosedNotification;
use Botble\CarRentals\Notifications\ClaimDocsRequestedNotification;
use Botble\CarRentals\Notifications\ClaimFinancialOutcomeNotification;
use Botble\CarRentals\Notifications\ClaimOpenedNotification;
use Botble\CarRentals\Notifications\ClaimStatusUpdatedNotification;
use Illuminate\Support\Collection;

class ClaimNotificationDispatcher
{
    public function notifyOpened(Booking $booking, BookingClaim $claim): void
    {
        $this->notifyRecipients($booking, new ClaimOpenedNotification($claim, $booking), 'claim_notification_opened', [
            'claim_id' => $claim->id,
        ]);
    }

    public function notifyDocsRequested(Booking $booking, BookingClaim $claim): void
    {
        $this->notifyRecipients($booking, new ClaimDocsRequestedNotification($claim, $booking), 'claim_notification_docs_requested', [
            'claim_id' => $claim->id,
        ]);
    }

    public function notifyStatusUpdated(Booking $booking, BookingClaim $claim): void
    {
        $this->notifyRecipients($booking, new ClaimStatusUpdatedNotification($claim, $booking), 'claim_notification_status_updated', [
            'claim_id' => $claim->id,
            'status' => $claim->status,
        ]);
    }

    public function notifyClosed(Booking $booking, BookingClaim $claim): void
    {
        $this->notifyRecipients($booking, new ClaimClosedNotification($claim, $booking), 'claim_notification_closed', [
            'claim_id' => $claim->id,
            'status' => $claim->status,
        ]);
    }

    public function notifyFinancialOutcome(Booking $booking, BookingClaim $claim): void
    {
        $this->notifyRecipients($booking, new ClaimFinancialOutcomeNotification($claim, $booking), 'claim_notification_financial_outcome', [
            'claim_id' => $claim->id,
            'outcome_action' => $claim->outcome_action,
            'settlement_status' => $claim->settlement_status,
        ]);
    }

    protected function recipients(Booking $booking): Collection
    {
        $recipients = collect([
            $booking->customer,
            $booking->vendor,
        ])->filter(fn ($recipient) => $recipient && $recipient->getKey() && ! empty($recipient->email));

        return $recipients->unique(fn ($recipient) => strtolower((string) $recipient->email))->values();
    }

    protected function notifyRecipients(Booking $booking, object $notification, string $auditAction, array $metadata = []): void
    {
        $recipients = $this->recipients($booking);

        foreach ($recipients as $recipient) {
            $recipient->notify($notification);
        }

        if ($recipients->isNotEmpty()) {
            app(SupportActionRecorder::class)->record($booking, $auditAction, null, array_merge($metadata, [
                'recipient_count' => $recipients->count(),
                'recipient_emails' => $recipients->pluck('email')->all(),
            ]));
        }
    }
}
