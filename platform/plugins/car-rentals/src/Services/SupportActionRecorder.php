<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingSupportAction;

class SupportActionRecorder
{
    public function record(Booking $booking, string $action, ?string $note = null, array $metadata = []): void
    {
        if (! $booking->getKey()) {
            return;
        }

        BookingSupportAction::query()->create([
            'booking_id' => $booking->id,
            'admin_id' => auth()->id(),
            'action' => $action,
            'note' => $note,
            'metadata' => $metadata === [] ? null : $metadata,
        ]);
    }
}
