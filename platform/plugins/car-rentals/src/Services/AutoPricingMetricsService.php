<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\DemandPricingRecommendation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AutoPricingMetricsService
{
    /**
     * Get comprehensive auto-pricing metrics for a date range
     */
    public function getMetrics(Carbon $startDate, Carbon $endDate): array
    {
        $appliedCount = $this->getAppliedCount($startDate, $endDate);
        $avgConfidence = $this->getAverageConfidence($startDate, $endDate);
        $reasonCodes = $this->getReasonCodeDistribution($startDate, $endDate);
        $carsStats = $this->getCarsAutoApplyStats();
        $topReasons = $this->getTopReasons($reasonCodes);
        $estimatedRevenue = $this->estimateRevenueImpact($appliedCount);

        return [
            'applied_count' => $appliedCount,
            'avg_confidence' => round($avgConfidence, 4),
            'total_value_applied' => $this->getTotalAppliedValue($startDate, $endDate),
            'reason_codes' => $reasonCodes,
            'top_reasons' => $topReasons,
            'cars_with_auto_apply' => $carsStats['enabled'],
            'cars_paused' => $carsStats['paused'],
            'cars_disabled' => $carsStats['disabled'],
            'estimated_revenue_impact' => $estimatedRevenue,
            'success_rate' => $appliedCount > 0 ? round(($appliedCount / $this->getTotalPendingRecommendations($startDate, $endDate)) * 100, 2) : 0,
        ];
    }

    /**
     * Get daily breakdown of auto-apply activity
     */
    public function getDailyMetrics(Carbon $startDate, Carbon $endDate): array
    {
        $dailyData = DemandPricingRecommendation::query()
            ->where('status', 'applied')
            ->where('applied_by', 0) // Auto-applied
            ->whereBetween('applied_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(applied_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(confidence_score) as avg_confidence'),
                DB::raw('AVG(recommended_value) as avg_price'),
                DB::raw('SUM(recommended_value) as total_value')
            )
            ->groupBy(DB::raw('DATE(applied_at)'))
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                    'avg_confidence' => round($item->avg_confidence, 4),
                    'avg_price' => round($item->avg_price, 2),
                    'total_value' => round($item->total_value, 2),
                ];
            });

        return $dailyData->all();
    }

    /**
     * Get per-car auto-apply statistics
     */
    public function getPerCarMetrics(Carbon $startDate, Carbon $endDate): array
    {
        return Car::query()
            ->with(['pricingPolicy'])
            ->select('id', 'name')
            ->get()
            ->map(function ($car) use ($startDate, $endDate) {
                $applied = DemandPricingRecommendation::query()
                    ->where('car_id', $car->id)
                    ->where('status', 'applied')
                    ->where('applied_by', 0)
                    ->whereBetween('applied_at', [$startDate, $endDate])
                    ->count();

                $policy = $car->pricingPolicy;

                return [
                    'car_id' => $car->id,
                    'car_name' => $car->name,
                    'applied_count' => $applied,
                    'auto_apply_enabled' => $policy?->demand_auto_apply_enabled ? true : false,
                    'is_paused' => $policy?->demand_auto_apply_paused_until && $policy->demand_auto_apply_paused_until > now(),
                    'confidence_threshold' => $policy?->demand_auto_apply_min_confidence ?? 0.70,
                ];
            })
            ->filter(function ($item) {
                return $item['auto_apply_enabled'] || $item['applied_count'] > 0;
            })
            ->sortByDesc('applied_count')
            ->values()
            ->all();
    }

    /**
     * Get reason code distribution for auto-applied recommendations
     */
    protected function getReasonCodeDistribution(Carbon $startDate, Carbon $endDate): array
    {
        $recommendations = DemandPricingRecommendation::query()
            ->where('status', 'applied')
            ->where('applied_by', 0)
            ->whereBetween('applied_at', [$startDate, $endDate])
            ->pluck('reason_codes')
            ->flatten()
            ->countBy()
            ->toArray();

        return $recommendations;
    }

    /**
     * Get top 3 most common reason codes
     */
    protected function getTopReasons(array $reasonCodes): array
    {
        arsort($reasonCodes);
        return array_slice($reasonCodes, 0, 3, true);
    }

    /**
     * Get applied recommendation count
     */
    protected function getAppliedCount(Carbon $startDate, Carbon $endDate): int
    {
        return DemandPricingRecommendation::query()
            ->where('status', 'applied')
            ->where('applied_by', 0) // Auto-applied
            ->whereBetween('applied_at', [$startDate, $endDate])
            ->count();
    }

    /**
     * Get average confidence score of applied recommendations
     */
    protected function getAverageConfidence(Carbon $startDate, Carbon $endDate): float
    {
        return (float) (DemandPricingRecommendation::query()
            ->where('status', 'applied')
            ->where('applied_by', 0)
            ->whereBetween('applied_at', [$startDate, $endDate])
            ->avg('confidence_score') ?? 0);
    }

    /**
     * Get total value of applied recommendations
     */
    protected function getTotalAppliedValue(Carbon $startDate, Carbon $endDate): float
    {
        return (float) (DemandPricingRecommendation::query()
            ->where('status', 'applied')
            ->where('applied_by', 0)
            ->whereBetween('applied_at', [$startDate, $endDate])
            ->sum('recommended_value') ?? 0);
    }

    /**
     * Get total pending recommendations in range
     */
    protected function getTotalPendingRecommendations(Carbon $startDate, Carbon $endDate): int
    {
        return (int) DemandPricingRecommendation::query()
            ->whereBetween('recommendation_date', [$startDate, $endDate])
            ->count();
    }

    /**
     * Get cars with auto-apply enabled/paused/disabled
     */
    protected function getCarsAutoApplyStats(): array
    {
        $enabled = Car::query()
            ->whereHas('pricingPolicy', function ($query): void {
                $query->where('demand_auto_apply_enabled', true)
                    ->where(function ($query): void {
                        $query->whereNull('demand_auto_apply_paused_until')
                            ->orWhere('demand_auto_apply_paused_until', '<', now());
                    });
            })
            ->count();

        $paused = Car::query()
            ->whereHas('pricingPolicy', function ($query): void {
                $query->where('demand_auto_apply_enabled', true)
                    ->where('demand_auto_apply_paused_until', '>', now());
            })
            ->count();

        $disabled = Car::query()
            ->whereHas('pricingPolicy', function ($query): void {
                $query->where('demand_auto_apply_enabled', false);
            })
            ->count();

        return [
            'enabled' => $enabled,
            'paused' => $paused,
            'disabled' => $disabled,
        ];
    }

    /**
     * Estimate revenue impact (rough calculation)
     * This is a naive estimate: average recommended value × count × estimated occupancy rate
     */
    protected function estimateRevenueImpact(int $appliedCount): float
    {
        // Very rough estimate: assume 70% occupancy rate and average $150 per day
        $avgPrice = 150;
        $occupancyRate = 0.70;

        return round($appliedCount * $avgPrice * $occupancyRate, 2);
    }

    /**
     * Get time series data for charts
     */
    public function getTimeSeriesData(Carbon $startDate, Carbon $endDate, string $groupBy = 'day'): array
    {
        $groupFormat = match ($groupBy) {
            'week' => '%Y-W%v',
            'month' => '%Y-%m',
            'day' => '%Y-%m-%d',
            default => '%Y-%m-%d',
        };

        return DemandPricingRecommendation::query()
            ->where('status', 'applied')
            ->where('applied_by', 0)
            ->whereBetween('applied_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE_FORMAT(applied_at, '$groupFormat') as period"),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(confidence_score) as avg_confidence')
            )
            ->groupBy(DB::raw("DATE_FORMAT(applied_at, '$groupFormat')"))
            ->orderBy('period')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => $item->period,
                    'count' => $item->count,
                    'avg_confidence' => round($item->avg_confidence, 4),
                ];
            })
            ->all();
    }
}
