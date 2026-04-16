<?php

namespace Botble\CarRentals\Console;

use Illuminate\Console\Command;
use Botble\CarRentals\Models\VendorQualityScore;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\CarReview;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Carbon\Carbon;

class CalculateVendorQualityCommand extends Command
{
    protected $signature   = 'car-rentals:calculate-vendor-quality';
    protected $description = 'Calculate and update vendor quality scores and badges';

    public function handle(): int
    {
        $vendors = Customer::query()->where('is_vendor', 1)->get();

        $this->info("Processing {$vendors->count()} vendors...");
        $bar = $this->output->createProgressBar($vendors->count());
        $bar->start();

        foreach ($vendors as $vendor) {
            $this->calculateForVendor($vendor->id);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Vendor quality scores updated successfully.');

        return self::SUCCESS;
    }

    private function calculateForVendor(int $vendorId): void
    {
        $bookings = Booking::query()->where('vendor_id', $vendorId)->get();
        $totalBookings     = $bookings->count();
        $completedBookings = $bookings->where('status', BookingStatusEnum::COMPLETED)->count();
        $cancelledBookings = $bookings->where('status', BookingStatusEnum::CANCELLED)->count();

        // ── Acceptance rate ──────────────────────────────────────────────
        $acceptedBookings     = $bookings->whereIn('status', [
            BookingStatusEnum::PROCESSING,
            BookingStatusEnum::COMPLETED,
        ])->count();
        $decisionableBookings = $acceptedBookings + $cancelledBookings;
        $acceptanceRate       = $decisionableBookings > 0
            ? round(($acceptedBookings / $decisionableBookings) * 100, 2)
            : 100; // naya vendor — benefit of doubt

        // Acceptance score (max 10)
        $acceptanceScore = match(true) {
            $acceptanceRate >= 95 => 10,
            $acceptanceRate >= 85 => 8,
            $acceptanceRate >= 75 => 6,
            $acceptanceRate >= 60 => 4,
            default               => 2,
        };

        // ── Completion rate ──────────────────────────────────────────────
        $completionRate = $totalBookings > 0
            ? round(($completedBookings / $totalBookings) * 100, 2)
            : 0;

        // Completion score (max 20)
        $completionScore = match(true) {
            $completionRate >= 95 => 20,
            $completionRate >= 90 => 16,
            $completionRate >= 80 => 12,
            $completionRate >= 70 => 8,
            default               => 4,
        };

        // ── Cancellation score (max 20) ──────────────────────────────────
        $cancellationRate  = $totalBookings > 0
            ? ($cancelledBookings / $totalBookings) * 100
            : 0;
        $cancellationScore = match(true) {
            $cancellationRate <= 1  => 20,
            $cancellationRate <= 3  => 16,
            $cancellationRate <= 5  => 12,
            $cancellationRate <= 10 => 8,
            default                 => 4,
        };

        // ── Rating score (max 30) ────────────────────────────────────────
        $reviews   = CarReview::query()
            ->whereHas('car', fn($q) => $q->where('author_id', $vendorId))
            ->get();
        $avgRating = $reviews->count() > 0 ? round($reviews->avg('star'), 2) : 0;
        $ratingScore = match(true) {
            $avgRating >= 4.8 => 30,
            $avgRating >= 4.5 => 25,
            $avgRating >= 4.0 => 20,
            $avgRating >= 3.5 => 14,
            $avgRating >= 3.0 => 8,
            default           => 0,
        };

        // ── Response score (max 20) ──────────────────────────────────────
        $vendor           = Customer::find($vendorId);
        $avgResponseHours = $vendor ? (float) $vendor->avg_response_hours : 24;
        $responseScore    = match(true) {
            $avgResponseHours <= 1  => 20,
            $avgResponseHours <= 3  => 16,
            $avgResponseHours <= 6  => 12,
            $avgResponseHours <= 12 => 8,
            default                 => 4,
        };

        // ── Total (max 100) ──────────────────────────────────────────────
        // Rating(30) + Completion(20) + Cancellation(20) + Response(20) + Acceptance(10) = 100
        $totalScore = $ratingScore
                    + $completionScore
                    + $cancellationScore
                    + $responseScore
                    + $acceptanceScore;

        // ── Badge tier ───────────────────────────────────────────────────
        $badgeTier = match(true) {
            $totalScore >= 80 && $completionRate >= 50 => 'all_star',
            $totalScore >= 65 && $completionRate >= 40 => 'top_host',
            $totalScore >= 50 && $completionRate >= 20 => 'rising_star',
            default           => 'none',
        };

        $existing = VendorQualityScore::where('vendor_id', $vendorId)->first();

        VendorQualityScore::updateOrCreate(
            ['vendor_id' => $vendorId],
            [
                'total_score'        => $totalScore,
                'rating_score'       => $ratingScore,
                'completion_score'   => $completionScore,
                'cancellation_score' => $cancellationScore,
                'response_score'     => $responseScore,
                'acceptance_rate'    => $acceptanceRate,   // ← NEW
                'avg_rating'         => $avgRating,
                'completion_rate'    => $completionRate,
                'total_bookings'     => $totalBookings,
                'completed_bookings' => $completedBookings,
                'cancelled_bookings' => $cancelledBookings,
                'avg_response_hours' => $avgResponseHours,
                'badge_tier'         => $badgeTier,
                // Preserve admin override if already set
                'badge_override'     => $existing?->badge_override ?? false,
                'override_badge'     => $existing?->override_badge ?? null,
                'last_calculated_at' => Carbon::now(),
            ]
        );
    }
}