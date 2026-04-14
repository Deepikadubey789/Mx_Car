<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\Customer;

class DriverEligibilityService
{
    public function evaluate(Car $car, ?Customer $customer = null, ?array $depositRisk = null): array
    {
        $reasons = [];
        $state = 'eligible';

        if (! $customer) {
            return [
                'state' => 'manual_review',
                'reasons' => ['guest_requires_manual_review'],
            ];
        }

        if (in_array($customer->kyc_status, ['failed'], true)) {
            $state = 'blocked';
            $reasons[] = 'kyc_failed';
        }

        if (in_array($customer->kyc_status, ['pending', 'manual_review', 'not_submitted'], true)) {
            $state = 'manual_review';
            $reasons[] = 'kyc_pending_review';
        }

        $restrictedCategoryIds = $this->decodeIntMap(CarRentalsHelper::getSetting('eligibility_restricted_category_ids', []));
        $carCategoryIds = $car->relationLoaded('categories')
            ? $car->categories->pluck('id')->all()
            : $car->categories()->pluck('cr_car_categories.id')->all();

        if ($restrictedCategoryIds && array_intersect($carCategoryIds, $restrictedCategoryIds) && $customer->kyc_level !== 'driver_verified') {
            $state = 'blocked';
            $reasons[] = 'category_requires_driver_verified_kyc';
        }

        if (($depositRisk['risk_level'] ?? null) === 'high') {
            if ($state !== 'blocked') {
                $state = 'manual_review';
            }
            $reasons[] = 'high_risk_deposit_profile';
        }

        return [
            'state' => $state,
            'reasons' => array_values(array_unique($reasons)),
        ];
    }

    protected function decodeIntMap(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_unique(array_map('intval', array_filter($value, 'is_numeric'))));
        }

        if (! is_string($value) || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        if (! is_array($decoded)) {
            return [];
        }

        return array_values(array_unique(array_map('intval', array_filter($decoded, 'is_numeric'))));
    }
}
