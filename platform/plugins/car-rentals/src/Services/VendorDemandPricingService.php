<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\DemandPricingRecommendation;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VendorDemandPricingService
{
    /**
     * Get all recommendations for a specific vendor (filtered by their cars)
     */
    public function getVendorRecommendations(
        int $vendorId,
        ?string $status = 'pending',
        ?Carbon $dateFrom = null,
        ?Carbon $dateTo = null,
        ?int $limit = null,
    ): Collection {
        $query = DemandPricingRecommendation::query()
            ->whereHas('car', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });

        if ($status) {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->where('recommendation_date', '>=', $dateFrom->toDateString());
        }

        if ($dateTo) {
            $query->where('recommendation_date', '<=', $dateTo->toDateString());
        }

        $query->orderBy('confidence_score', 'desc')
            ->orderBy('recommendation_date', 'asc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get()->map(fn ($rec) => $this->formatForVendor($rec));
    }

    /**
     * Get recommendations grouped by car for a vendor
     */
    public function getRecommendationsByCar(
        int $vendorId,
        ?int $carId = null,
        ?string $status = 'pending',
    ): Collection {
        $recommendations = $this->getVendorRecommendations($vendorId, $status);

        if ($carId) {
            $recommendations = $recommendations->filter(fn ($rec) => $rec->car_id == $carId);
        }

        return $recommendations->groupBy('car_id');
    }

    /**
     * Get recommendations grouped by date for a vendor
     */
    public function getRecommendationsByDate(
        int $vendorId,
        ?Carbon $dateFrom = null,
        ?Carbon $dateTo = null,
        ?string $status = 'pending',
    ): Collection {
        $dateFrom ??= Carbon::now()->startOfDay();
        $dateTo ??= Carbon::now()->addDays(30)->endOfDay();

        $recommendations = $this->getVendorRecommendations($vendorId, $status, $dateFrom, $dateTo);

        return $recommendations->groupBy(fn ($rec) => $rec->recommendation_date->format('Y-m-d'));
    }

    /**
     * Format recommendation for vendor display (add human-readable signals)
     */
    public function formatForVendor(DemandPricingRecommendation $recommendation): DemandPricingRecommendation
    {
        // Add formatted signals breakdown
        $recommendation->formatted_signals = $this->formatSignalsBreakdown($recommendation);

        // Add estimated revenue impact
        $recommendation->formatted_revenue_impact = $this->formatRevenueImpact($recommendation);

        // Add confidence label
        $recommendation->confidence_label = $this->getConfidenceLabel($recommendation->confidence_score);

        // Add adjustment status
        $recommendation->adjustment_status = $this->getAdjustmentStatus($recommendation);

        return $recommendation;
    }

    /**
     * Format signals as human-readable breakdown
     * Example: "45% from conversion (9 bookings / 200 views) + 35% from supply pressure + 20% from views + 8% weekend"
     */
    public function formatSignalsBreakdown(DemandPricingRecommendation $recommendation): array
    {
        $signals = [];

        // Extract from recommendation data if stored, otherwise estimate
        $demandScore = $recommendation->demand_score ?? 0;
        $reasonCodes = $recommendation->reason_codes ?? [];

        // Conversion signal (45% weight)
        if (in_array('high_conversion', $reasonCodes)) {
            $signals[] = [
                'weight' => '45%',
                'factor' => 'High Conversion Rate',
                'description' => 'Multiple bookings relative to views',
                'icon' => 'ti-trending-up',
            ];
        }

        // Supply pressure signal (35% weight)
        if (in_array('low_near_term_supply', $reasonCodes)) {
            $signals[] = [
                'weight' => '35%',
                'factor' => 'Supply Pressure',
                'description' => 'Car has active bookings on this date',
                'icon' => 'ti-layout-grid',
            ];
        }

        // Views signal (20% weight)
        if (in_array('high_recent_views', $reasonCodes)) {
            $signals[] = [
                'weight' => '20%',
                'factor' => 'High View Volume',
                'description' => 'Strong interest in your car',
                'icon' => 'ti-eye',
            ];
        }

        // Weekend boost (8% flat)
        if (in_array('weekend_demand', $reasonCodes)) {
            $signals[] = [
                'weight' => '8%',
                'factor' => 'Weekend Boost',
                'description' => 'Higher demand on weekends',
                'icon' => 'ti-calendar-event',
            ];
        }

        // Baseline signal
        if (in_array('baseline_signal', $reasonCodes)) {
            $signals[] = [
                'weight' => '—',
                'factor' => 'Baseline Signal',
                'description' => 'Standard pricing recommendation based on historical data',
                'icon' => 'ti-baseline',
            ];
        }

        return $signals;
    }

    /**
     * Get human-readable confidence label
     */
    public function getConfidenceLabel(float $confidenceScore): array
    {
        if ($confidenceScore >= 0.85) {
            return [
                'label' => 'Very High',
                'color' => 'success',
                'description' => 'Very confident recommendation - strong signals support this price',
                'icon' => 'ti-circle-check',
            ];
        } elseif ($confidenceScore >= 0.75) {
            return [
                'label' => 'High',
                'color' => 'success',
                'description' => 'Confident recommendation - good data supports this price',
                'icon' => 'ti-circle-half-2',
            ];
        } elseif ($confidenceScore >= 0.70) {
            return [
                'label' => 'Good',
                'color' => 'info',
                'description' => 'Moderate confidence - reasonable signals for this price',
                'icon' => 'ti-info-circle',
            ];
        } else {
            return [
                'label' => 'Low',
                'color' => 'warning',
                'description' => 'Lower confidence - limited data for this price',
                'icon' => 'ti-alert-circle',
            ];
        }
    }

    /**
     * Get current adjustment status (applied/adjusted/not-adjusted)
     */
    public function getAdjustmentStatus(DemandPricingRecommendation $recommendation): array
    {
        if ($recommendation->status === 'applied') {
            if ($recommendation->adjustment_applied) {
                return [
                    'status' => 'adjusted',
                    'label' => 'Applied (Adjusted)',
                    'description' => sprintf(
                        'You adjusted this by %s$%.2f',
                        $recommendation->adjustment_applied > 0 ? '+' : '',
                        $recommendation->adjustment_applied
                    ),
                ];
            }

            return [
                'status' => 'applied',
                'label' => 'Applied',
                'description' => 'Applied the recommended price as-is',
            ];
        }

        if ($recommendation->status === 'dismissed') {
            return [
                'status' => 'dismissed',
                'label' => 'Dismissed',
                'description' => $recommendation->rejected_reason ? "Reason: {$recommendation->rejected_reason}" : 'You rejected this recommendation',
            ];
        }

        return [
            'status' => 'pending',
            'label' => 'Pending',
            'description' => 'Awaiting your decision',
        ];
    }

    /**
     * Format estimated revenue impact for display
     * Example: "$X extra per booking × estimated Y bookings = $Z total"
     */
    public function formatRevenueImpact(DemandPricingRecommendation $recommendation): string
    {
        if (! $recommendation->estimated_revenue_impact) {
            return 'Impact not calculated';
        }

        $priceDelta = $recommendation->recommended_value - ($recommendation->local_baseline_price ?? 0);
        $estimatedBookings = max(1, intval(abs($recommendation->estimated_revenue_impact) / max(1, abs($priceDelta))));

        if ($recommendation->estimated_revenue_impact > 0) {
            return sprintf(
                '+$%.2f/booking × ~%d bookings = +$%.2f estimated',
                $priceDelta,
                $estimatedBookings,
                $recommendation->estimated_revenue_impact
            );
        }

        return sprintf(
            '-$%.2f/booking × ~%d bookings = -$%.2f',
            abs($priceDelta),
            $estimatedBookings,
            abs($recommendation->estimated_revenue_impact)
        );
    }

    /**
     * Get count of pending recommendations for a vendor
     */
    public function getPendingCount(int $vendorId): int
    {
        return DemandPricingRecommendation::query()
            ->whereHas('car', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->where('status', 'pending')
            ->count();
    }

    /**
     * Get top N recommendations by confidence for vendor
     */
    public function getTopRecommendations(int $vendorId, int $limit = 5): Collection
    {
        return $this->getVendorRecommendations($vendorId, 'pending', null, null, $limit);
    }

    /**
     * Get recommendations that are expiring soon (within 24 hours)
     */
    public function getExpiringRecommendations(int $vendorId, int $hoursThreshold = 24): Collection
    {
        return DemandPricingRecommendation::query()
            ->whereHas('car', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->where('status', 'pending')
            ->whereBetween('expires_at', [
                Carbon::now(),
                Carbon::now()->addHours($hoursThreshold),
            ])
            ->orderBy('expires_at', 'asc')
            ->get()
            ->map(fn ($rec) => $this->formatForVendor($rec));
    }

    /**
     * Get performance history: applied recommendations and what actually happened
     */
    public function getPerformanceHistory(
        int $vendorId,
        ?Carbon $dateFrom = null,
        ?Carbon $dateTo = null,
    ): Collection {
        $dateFrom ??= Carbon::now()->subDays(30);
        $dateTo ??= Carbon::now();

        return DemandPricingRecommendation::query()
            ->whereHas('car', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->where('status', 'applied')
            ->whereBetween('applied_at', [$dateFrom, $dateTo])
            ->orderBy('applied_at', 'desc')
            ->get()
            ->map(function (DemandPricingRecommendation $rec) {
                $rec->revenue_impact_actual = $this->calculateActualRevenueImpact($rec);

                return $rec;
            });
    }

    /**
     * Calculate actual revenue impact from a recommendation that was applied
     */
    private function calculateActualRevenueImpact(DemandPricingRecommendation $recommendation): float
    {
        // TODO: When we have booking data after price applied, calculate actual revenue
        // For now, return estimated
        return $recommendation->estimated_revenue_impact ?? 0;
    }
}
