<?php

namespace Botble\CarRentals\Services;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\Customer;

class RiskBasedDepositService
{
    public function assess(float $baseDepositAmount, Car $car, ?Customer $customer = null): array
    {
        $baseDepositAmount = round(max(0, $baseDepositAmount), 2);

        if (! $this->isEnabled()) {
            return [
                'enabled' => false,
                'base_amount' => $baseDepositAmount,
                'final_amount' => $baseDepositAmount,
                'multiplier' => 1.0,
                'risk_level' => 'standard',
                'reasons' => [],
                'category_multiplier' => 1.0,
                'type_multiplier' => 1.0,
                'applied_vehicle_multiplier' => 1.0,
            ];
        }

        $reasons = [];

        $defaultMultiplier = $this->getFloat('deposit_risk_default_multiplier', 1.0, 0.1);
        $profileMultiplier = 1.0;

        if (! $customer || ! $customer->exists) {
            $guestMultiplier = $this->getFloat('deposit_risk_guest_multiplier', 1.15, 0.1);
            $profileMultiplier *= $guestMultiplier;
            $reasons[] = __('Guest checkout risk factor');
        } else {
            if (! $customer->is_verified) {
                $unverifiedMultiplier = $this->getFloat('deposit_risk_unverified_multiplier', 1.2, 0.1);
                $profileMultiplier *= $unverifiedMultiplier;
                $reasons[] = __('Unverified renter profile');
            }

            $completedBookings = $customer->bookings()
                ->where('status', BookingStatusEnum::COMPLETED)
                ->count();

            $lowHistoryThreshold = $this->getInt('deposit_risk_low_history_threshold', 3, 0);
            if ($completedBookings < $lowHistoryThreshold) {
                $lowHistoryMultiplier = $this->getFloat('deposit_risk_low_history_multiplier', 1.15, 0.1);
                $profileMultiplier *= $lowHistoryMultiplier;
                $reasons[] = __('Low completed booking history');
            }

            $avgRating = (float) $customer->receivedReviews()
                ->where('status', BaseStatusEnum::PUBLISHED)
                ->avg('star');

            $lowRatingThreshold = $this->getFloat('deposit_risk_low_rating_threshold', 3.5, 0);
            if ($avgRating > 0 && $avgRating < $lowRatingThreshold) {
                $lowRatingMultiplier = $this->getFloat('deposit_risk_low_rating_multiplier', 1.1, 0.1);
                $profileMultiplier *= $lowRatingMultiplier;
                $reasons[] = __('Low renter rating');
            }

            $hasEscalationOrDamage = $customer->bookings()
                ->where(function ($query): void {
                    $query
                        ->where('is_escalated', true)
                        ->orWhere(function ($innerQuery): void {
                            $innerQuery
                                ->whereNotNull('completion_damage_images')
                                ->where('completion_damage_images', '!=', '[]');
                        });
                })
                ->exists();

            if ($hasEscalationOrDamage) {
                $escalationMultiplier = $this->getFloat('deposit_risk_escalation_multiplier', 1.25, 0.1);
                $profileMultiplier *= $escalationMultiplier;
                $reasons[] = __('Escalation or damage history');
            }
        }

        $categoryMultiplier = $this->resolveCategoryMultiplier($car);
        $typeMultiplier = $this->resolveTypeMultiplier($car);
        $appliedVehicleMultiplier = $categoryMultiplier > 1 ? $categoryMultiplier : $typeMultiplier;

        if ($categoryMultiplier > 1) {
            $reasons[] = __('Car category risk multiplier');
        } elseif ($typeMultiplier > 1) {
            $reasons[] = __('Vehicle type risk multiplier');
        }

        $multiplier = round(max(0.1, $defaultMultiplier * $profileMultiplier * $appliedVehicleMultiplier), 4);
        $finalAmount = round($baseDepositAmount * $multiplier, 2);

        return [
            'enabled' => true,
            'base_amount' => $baseDepositAmount,
            'final_amount' => $finalAmount,
            'multiplier' => $multiplier,
            'risk_level' => $this->resolveRiskLevel($multiplier),
            'reasons' => array_values(array_unique($reasons)),
            'category_multiplier' => $categoryMultiplier,
            'type_multiplier' => $typeMultiplier,
            'applied_vehicle_multiplier' => $appliedVehicleMultiplier,
        ];
    }

    public function isEnabled(): bool
    {
        return (bool) CarRentalsHelper::getSetting('deposit_risk_enabled', false);
    }

    protected function resolveRiskLevel(float $multiplier): string
    {
        $mediumThreshold = $this->getFloat('deposit_risk_medium_threshold', 1.1, 1);
        $highThreshold = $this->getFloat('deposit_risk_high_threshold', 1.35, 1);

        if ($multiplier >= $highThreshold) {
            return 'high';
        }

        if ($multiplier >= $mediumThreshold) {
            return 'medium';
        }

        return 'low';
    }

    protected function resolveCategoryMultiplier(Car $car): float
    {
        $map = $this->decodeMultiplierMap((string) CarRentalsHelper::getSetting('deposit_category_multipliers_json', '{}'));
        if (! $map) {
            return 1.0;
        }

        $categoryIds = $car->categories()->pluck('cr_car_categories.id')->all();

        $best = 1.0;

        foreach ($categoryIds as $categoryId) {
            $multiplier = $map[(string) $categoryId] ?? 1.0;
            if ($multiplier > $best) {
                $best = $multiplier;
            }
        }

        return $best;
    }

    protected function resolveTypeMultiplier(Car $car): float
    {
        $typeId = $car->vehicle_type_id;
        if (! $typeId) {
            return 1.0;
        }

        $map = $this->decodeMultiplierMap((string) CarRentalsHelper::getSetting('deposit_type_multipliers_json', '{}'));

        return (float) ($map[(string) $typeId] ?? 1.0);
    }

    protected function decodeMultiplierMap(string $json): array
    {
        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            return [];
        }

        $map = [];

        foreach ($decoded as $key => $value) {
            if (! is_numeric($value)) {
                continue;
            }

            $map[(string) $key] = max(0.1, (float) $value);
        }

        return $map;
    }

    protected function getFloat(string $key, float $default, float $min): float
    {
        return max($min, (float) CarRentalsHelper::getSetting($key, $default));
    }

    protected function getInt(string $key, int $default, int $min): int
    {
        return max($min, (int) CarRentalsHelper::getSetting($key, $default));
    }
}
