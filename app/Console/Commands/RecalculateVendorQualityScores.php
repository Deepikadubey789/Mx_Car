<?php

namespace App\Console\Commands;

use Botble\CarRentals\Services\VendorQualityScoreService;
use Illuminate\Console\Command;

class RecalculateVendorQualityScores extends Command
{
    protected $signature = 'vendor:recalculate-scores {--vendor_id= : Single vendor ka ID (optional)}';

    protected $description = 'Sabhi vendors ke quality scores aur badges recalculate karo';

    public function handle(VendorQualityScoreService $service): void
    {
        $vendorId = $this->option('vendor_id');

        if ($vendorId) {
            $this->info("Vendor #{$vendorId} score are getting calculate");
            $score = $service->calculateForVendor((int) $vendorId);
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Score',        $score->total_score],
                    ['Badge',              $score->badge_tier ?? 'No Badge'],
                    ['Rating Score',       $score->rating_score],
                    ['Completion Rate',    $score->completion_rate],
                    ['Cancellation Score', $score->cancellation_score],
                    ['Response Score',     $score->response_score],
                    ['Total Bookings',     $score->total_bookings],
                ]
            );
        } else {
            $this->info('all vendors scores are  calculated');
            $service->calculateForAllVendors();
            $this->info('✅ Done —  All vendors are updated');
        }
    }
}