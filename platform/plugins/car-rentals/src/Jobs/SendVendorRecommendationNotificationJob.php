<?php

namespace Botble\CarRentals\Jobs;

use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Notifications\VendorHighConfidenceRecommendationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class SendVendorRecommendationNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected Customer $vendor,
        protected Collection $recommendations,
        protected string $notificationType = 'normal',
    ) {}

    public function handle(): void
    {
        if (! $this->vendor || ! $this->vendor->is_vendor) {
            return;
        }

        // Check if vendor has disabled notifications
        if (! $this->getVendorNotificationPreference($this->vendor->id)) {
            return;
        }

        Notification::send(
            $this->vendor,
            new VendorHighConfidenceRecommendationNotification(
                $this->recommendations,
                $this->notificationType
            )
        );
    }

    /**
     * Get vendor's notification preference (could extend with settings later)
     */
    protected function getVendorNotificationPreference(int $vendorId): bool
    {
        // TODO: Check vendor setting for demand pricing notifications
        // For now, default to true
        return true;
    }
}
