<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Models\CarReview;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Models\VendorQualityScore;
use Illuminate\Support\Facades\DB;

class VendorQualityScoreService
{
    // Badge thresholds
    const ALL_STAR_THRESHOLD  = 90;
    const TOP_HOST_THRESHOLD  = 75;
    const RISING_STAR_THRESHOLD = 60;

    // Score weights (total = 100)
    const WEIGHT_RATING      = 35;
    const WEIGHT_COMPLETION  = 25;
    const WEIGHT_CANCELLATION = 20;
    const WEIGHT_RESPONSE    = 20;

    public function calculateForVendor(int $vendorId): VendorQualityScore
    {
        $bookings = DB::table('cr_bookings')
            ->where('vendor_id', $vendorId)
            ->whereIn('status', [
                BookingStatusEnum::COMPLETED,
                BookingStatusEnum::CANCELLED,
            ])
            ->select('status', 'cancelled_at', 'completed_at')
            ->get();

        $totalBookings     = $bookings->count();
        $completedBookings = $bookings->where('status', BookingStatusEnum::COMPLETED)->count();
        $cancelledBookings = $bookings->where('status', BookingStatusEnum::CANCELLED)->count();

        // Completion rate score (0-100)
        $completionRate = $totalBookings > 0
            ? ($completedBookings / $totalBookings) * 100
            : 0;

        // Cancellation score (0-100) — cancellation jitni kam, score utna zyada
        $cancellationScore = $totalBookings > 0
            ? (1 - ($cancelledBookings / $totalBookings)) * 100
            : 100;

        // Rating score (0-100)
        $reviewData = DB::table('cr_car_reviews')
            ->join('cr_cars', 'cr_car_reviews.car_id', '=', 'cr_cars.id')
            ->where('cr_cars.author_id', $vendorId)
            ->where('cr_car_reviews.status', 'published')
            ->selectRaw('AVG(star) as avg_star, COUNT(*) as total')
            ->first();

        $avgRating   = $reviewData ? (float) $reviewData->avg_star : 0;
        $ratingScore = ($avgRating / 5) * 100; // 5-star scale to 100

        // Response score — avg_response_hours se calculate
        $vendor = Customer::find($vendorId);
        $avgResponseHours = $vendor ? (float) $vendor->avg_response_hours : 24;
        $responseScore = $this->calculateResponseScore($avgResponseHours);

        // Total weighted score
        $totalScore = (
            ($ratingScore      * self::WEIGHT_RATING      / 100) +
            ($completionRate   * self::WEIGHT_COMPLETION  / 100) +
            ($cancellationScore * self::WEIGHT_CANCELLATION / 100) +
            ($responseScore    * self::WEIGHT_RESPONSE    / 100)
        );

        // Badge tier determine karo
        $badgeTier = $this->determineBadgeTier($totalScore, $totalBookings);

        // Upsert — exist kare to update, nahi to create
        $score = VendorQualityScore::updateOrCreate(
            ['vendor_id' => $vendorId],
            [
                'rating_score'        => round($ratingScore, 2),
                'completion_rate'     => round($completionRate, 2),
                'cancellation_score'  => round($cancellationScore, 2),
                'response_score'      => round($responseScore, 2),
                'total_score'         => round($totalScore, 2),
                'badge_tier'          => $badgeTier,
                'total_bookings'      => $totalBookings,
                'completed_bookings'  => $completedBookings,
                'cancelled_bookings'  => $cancelledBookings,
                'avg_rating'          => round($avgRating, 2),
                'avg_response_hours'  => round($avgResponseHours, 2),
                'last_calculated_at'  => now(),
            ]
        );

        return $score;
    }

    public function calculateForAllVendors(): void
    {
        Customer::where('is_vendor', 1)
            ->select('id')
            ->chunk(50, function ($vendors) {
                foreach ($vendors as $vendor) {
                    $this->calculateForVendor($vendor->id);
                }
            });
    }

    private function calculateResponseScore(float $avgResponseHours): float
    {
        // 1 hour ya kam = 100, 2 hours = 90, 6 hours = 70, 24 hours = 40, 48+ hours = 0
        if ($avgResponseHours <= 1)  return 100;
        if ($avgResponseHours <= 2)  return 90;
        if ($avgResponseHours <= 6)  return 70;
        if ($avgResponseHours <= 12) return 55;
        if ($avgResponseHours <= 24) return 40;
        if ($avgResponseHours <= 48) return 20;
        return 0;
    }

    private function determineBadgeTier(float $totalScore, int $totalBookings): ?string
    {
        // Minimum 3 bookings hone chahiye badge ke liye
        if ($totalBookings < 3) return null;

        if ($totalScore >= self::ALL_STAR_THRESHOLD)    return 'all_star';
        if ($totalScore >= self::TOP_HOST_THRESHOLD)    return 'top_host';
        if ($totalScore >= self::RISING_STAR_THRESHOLD) return 'rising_star';

        return null;
    }
}
