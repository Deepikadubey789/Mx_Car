<?php

namespace Botble\CarRentals\Commands;

use Botble\CarRentals\Models\DemandPricingRecommendation;
use Illuminate\Console\Command;

class CleanupExpiredRecommendationsCommand extends Command
{
    protected $signature = 'car-rentals:cleanup-expired-recommendations';
    protected $description = 'Remove expired pending demand pricing recommendations';

    public function handle(): int
    {
        $now = now();

        $deleted = DemandPricingRecommendation::query()
            ->where('status', 'pending')
            ->where('expires_at', '<', $now)
            ->delete();

        if ($deleted === 0) {
            $this->info('No expired recommendations to clean up.');
            return self::SUCCESS;
        }

        $this->info("✓ Cleaned up $deleted expired recommendation(s)");
        return self::SUCCESS;
    }
}
