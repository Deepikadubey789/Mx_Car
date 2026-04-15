<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Enums\CarDateValueTypeEnum;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarDate;
use Botble\CarRentals\Models\DemandPricingRecommendation;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DemandPricingRecommendationService
{
    public function generateForCar(Car $car, Carbon $startDate, Carbon $endDate): void
    {
        $policy = $car->pricingPolicy;

        if (! $policy || ! $policy->demand_recommendations_enabled) {
            return;
        }

        $currentDate = $startDate->copy()->startOfDay();
        $lastDate = $endDate->copy()->startOfDay();

        while ($currentDate->lte($lastDate)) {
            $signals = $this->buildSignals($car, $currentDate);
            $existing = DemandPricingRecommendation::query()
                ->where('car_id', $car->getKey())
                ->whereDate('recommendation_date', $currentDate->toDateString())
                ->first();

            if ($existing && in_array($existing->status, ['applied', 'dismissed'], true)) {
                $currentDate->addDay();
                continue;
            }

            $baseRate = $this->getBaseRate($car, $currentDate);
            $recommendation = $this->buildRecommendedValue($baseRate, $signals['demand_score'], $policy);

            DemandPricingRecommendation::query()->updateOrCreate(
                [
                    'car_id' => $car->getKey(),
                    'recommendation_date' => $currentDate->toDateString(),
                ],
                [
                    'recommended_value' => $recommendation['recommended_value'],
                    'value_type' => CarDateValueTypeEnum::FIXED,
                    'demand_score' => $signals['demand_score'],
                    'confidence_score' => $signals['confidence_score'],
                    'reason_codes' => $signals['reason_codes'],
                    'status' => 'pending',
                    'generated_at' => now(),
                    'expires_at' => $currentDate->copy()->endOfDay(),
                ]
            );

            $currentDate->addDay();
        }

        $policy->forceFill([
            'demand_last_generated_at' => now(),
        ])->save();
    }

    public function applyRecommendation(DemandPricingRecommendation $recommendation, ?int $actorId = null): CarDate
    {
        $carDate = CarDate::query()->firstOrNew([
            'car_id' => $recommendation->car_id,
            'start_date' => $recommendation->recommendation_date,
        ]);

        $carDate->fill([
            'end_date' => $recommendation->recommendation_date,
            'value' => $recommendation->recommended_value,
            'value_type' => $recommendation->value_type,
            'active' => true,
        ]);

        $carDate->save();

        $recommendation->forceFill([
            'status' => 'applied',
            'applied_by' => $actorId,
            'applied_at' => now(),
        ])->save();

        return $carDate;
    }

    public function dismissRecommendation(DemandPricingRecommendation $recommendation, ?int $actorId = null): DemandPricingRecommendation
    {
        $recommendation->forceFill([
            'status' => 'dismissed',
            'applied_by' => $actorId,
            'applied_at' => now(),
        ])->save();

        return $recommendation;
    }

    public function getRecommendationsForRange(Car $car, Carbon $startDate, Carbon $endDate, array $statuses = ['pending']): Collection
    {
        return DemandPricingRecommendation::query()
            ->where('car_id', $car->getKey())
            ->whereBetween('recommendation_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($statuses, function ($query) use ($statuses): void {
                $query->whereIn('status', $statuses);
            })
            ->orderBy('recommendation_date')
            ->get();
    }

    public function getCalendarEvents(Car $car, Carbon $startDate, Carbon $endDate): array
    {
        return $this->getRecommendationsForRange($car, $startDate, $endDate)
            ->map(function (DemandPricingRecommendation $recommendation): array {
                $date = $recommendation->recommendation_date?->format('Y-m-d');

                return [
                    'id' => 'demand-recommendation-' . $recommendation->getKey(),
                    'title' => 'Suggested ' . format_price($recommendation->recommended_value),
                    'start' => $date,
                    'end' => $date,
                    'value' => $recommendation->recommended_value,
                    'value_type' => $recommendation->value_type,
                    'active' => $recommendation->status === 'pending' ? 1 : 0,
                    'backgroundColor' => $recommendation->status === 'pending' ? '#f59f00' : '#adb5bd',
                    'textColor' => '#ffffff',
                    'classNames' => ['demand-recommendation', $recommendation->status],
                    'recommendation_id' => $recommendation->getKey(),
                    'recommendation_status' => $recommendation->status,
                    'recommended_value' => $recommendation->recommended_value,
                    'demand_score' => $recommendation->demand_score,
                    'confidence_score' => $recommendation->confidence_score,
                    'reason_codes' => $recommendation->reason_codes ?? [],
                ];
            })
            ->all();
    }

    protected function buildSignals(Car $car, Carbon $date): array
    {
        $lookbackStart = $date->copy()->subDays(30)->startOfDay();
        $lookbackEnd = $date->copy()->subDay()->endOfDay();

        $views = (int) DB::table('cr_car_views')
            ->where('car_id', $car->getKey())
            ->whereBetween('date', [$lookbackStart->toDateString(), $lookbackEnd->toDateString()])
            ->sum('views');

        $bookingsInWindow = (int) DB::table('cr_booking_cars')
            ->join('cr_bookings', 'cr_booking_cars.booking_id', '=', 'cr_bookings.id')
            ->where('cr_booking_cars.car_id', $car->getKey())
            ->whereNotIn('cr_bookings.status', ['cancelled', 'failed'])
            ->whereDate('cr_booking_cars.rental_start_date', '>=', $lookbackStart->toDateString())
            ->whereDate('cr_booking_cars.rental_start_date', '<=', $lookbackEnd->toDateString())
            ->count();

        $futurePressure = (int) DB::table('cr_booking_cars')
            ->join('cr_bookings', 'cr_booking_cars.booking_id', '=', 'cr_bookings.id')
            ->where('cr_booking_cars.car_id', $car->getKey())
            ->whereNotIn('cr_bookings.status', ['cancelled', 'failed'])
            ->whereDate('cr_booking_cars.rental_start_date', '<=', $date->toDateString())
            ->whereDate('cr_booking_cars.rental_end_date', '>=', $date->toDateString())
            ->count();

        $conversionRate = $views > 0 ? min(1, $bookingsInWindow / $views) : 0;

        $isWeekend = in_array((int) $date->dayOfWeekIso, [5, 6, 7], true);
        $weekdayBoost = $isWeekend ? 0.08 : 0;

        $demandScore = min(
            1,
            max(
                0,
                ($conversionRate * 0.45)
                + (min(1, $futurePressure / 3) * 0.35)
                + (min(1, $views / 200) * 0.20)
                + $weekdayBoost
            )
        );

        $confidenceScore = min(
            1,
            max(0.1, (min(1, $views / 150) * 0.5) + (min(1, $bookingsInWindow / 20) * 0.5))
        );

        $reasonCodes = [];
        if ($views >= 100) {
            $reasonCodes[] = 'high_recent_views';
        }
        if ($conversionRate >= 0.03) {
            $reasonCodes[] = 'high_conversion';
        }
        if ($futurePressure >= 1) {
            $reasonCodes[] = 'low_near_term_supply';
        }
        if ($isWeekend) {
            $reasonCodes[] = 'weekend_demand';
        }

        if (! $reasonCodes) {
            $reasonCodes[] = 'baseline_signal';
        }

        return [
            'demand_score' => round($demandScore, 4),
            'confidence_score' => round($confidenceScore, 4),
            'reason_codes' => $reasonCodes,
        ];
    }

    protected function buildRecommendedValue(float $baseRate, float $demandScore, mixed $policy): array
    {
        $adjustmentPercent = 0.0;

        if ($demandScore >= 0.60) {
            $adjustmentPercent = min(20, (($demandScore - 0.60) / 0.40) * 20);
        } elseif ($demandScore <= 0.35) {
            $adjustmentPercent = -min(15, ((0.35 - $demandScore) / 0.35) * 15);
        }

        $maxDailyChange = $policy->demand_max_daily_change_percent;

        if ($maxDailyChange !== null) {
            $limit = abs((float) $maxDailyChange);
            $adjustmentPercent = max(-$limit, min($limit, $adjustmentPercent));
        }

        $recommendedValue = $baseRate + ($baseRate * $adjustmentPercent / 100);

        if ($policy->demand_min_price !== null) {
            $recommendedValue = max((float) $policy->demand_min_price, $recommendedValue);
        }

        if ($policy->demand_max_price !== null) {
            $recommendedValue = min((float) $policy->demand_max_price, $recommendedValue);
        }

        return [
            'recommended_value' => round(max(1, $recommendedValue), 2),
            'adjustment_percent' => round($adjustmentPercent, 2),
        ];
    }

    protected function getBaseRate(Car $car, Carbon $date): float
    {
        $nextDate = $date->copy()->addDay()->toDateString();

        return (float) $car->getCarRentalPrice($date->toDateString(), $nextDate);
    }
}
