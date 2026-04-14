<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Enums\ServicePriceTypeEnum;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\Insurance;
use Botble\CarRentals\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PricingQuoteService
{
    public function buildQuote(
        Car $car,
        Carbon $startDate,
        Carbon $endDate,
        array $serviceIds = [],
        array $insuranceIds = [],
        ?string $couponCode = null,
        mixed $customer = null
    ): array {
        $rentalDays = max(1, $startDate->diffInDays($endDate));

        $baseRentalAmount = (float) $car->getCarRentalPrice($startDate->toDateString(), $endDate->toDateString());

        $policyDiscount = $this->resolvePolicyDiscount($car, $baseRentalAmount, $rentalDays);
        $rentalAmount = round(max(0, $baseRentalAmount - $policyDiscount['amount']), 2);
        $policy = $car->pricingPolicy;
        $includedDistanceLimit = $this->resolveIncludedDistanceLimit($policy, $rentalDays);
        $distanceUnit = (string) ($policy?->distance_unit ?: 'km');
        $distanceOverageBillingMode = (string) ($policy?->distance_overage_billing_mode ?: 'end_of_trip');
        $extraDistanceUnitPrice = round((float) ($policy?->extra_distance_unit_price ?: 0), 4);

        $services = Service::query()->whereIn('id', $serviceIds)->get();
        $serviceAmount = $this->calculateServiceAmount($services, $rentalDays);

        $insurances = Insurance::query()->whereIn('id', $insuranceIds)->get();
        $insuranceAmount = round((float) $insurances->sum('price'), 2);

        $subtotal = round($rentalAmount + $serviceAmount + $insuranceAmount, 2);

        $couponAmount = 0.0;
        if ($couponCode) {
            $couponService = app(CouponService::class);
            $coupon = $couponService->getCouponByCode($couponCode);
            if ($coupon !== null) {
                $couponAmount = (float) $couponService->getDiscountAmount(
                    $coupon->type->getValue(),
                    (float) $coupon->value,
                    $subtotal
                );
            }
        }

        $couponAmount = round(min($couponAmount, $subtotal), 2);

        $taxAmount = round((float) $car->calculateTaxAmount($subtotal), 2);
        $taxTitle = $car->getTaxInfo($taxAmount);

        $priceLockService = app(PriceLockService::class);
        $feeName = $priceLockService->getFeeName();
        $feeValue = $priceLockService->getFeeValue();
        $feeAmount = $priceLockService->calculateFeeAmount($subtotal);

        $depositType = $priceLockService->getDepositType();
        $depositRate = $priceLockService->getDepositRate();
        $depositFixedAmount = $priceLockService->getDepositFixedAmount();
        $depositBaseAmount = $priceLockService->calculateDepositAmount($subtotal);

        $depositRisk = app(RiskBasedDepositService::class)->assess(
            $depositBaseAmount,
            $car,
            $customer
        );

        $depositAmount = round((float) $depositRisk['final_amount'], 2);

        $totalAmount = round(($subtotal + $taxAmount) - $couponAmount + $feeAmount, 2);
        $finalPayableAmount = round($totalAmount + $depositAmount, 2);

        return [
            'rental_days' => $rentalDays,
            'base_rental_amount' => round($baseRentalAmount, 2),
            'policy_discount_amount' => $policyDiscount['amount'],
            'policy_discount_pre_cap_amount' => $policyDiscount['pre_cap_amount'],
            'policy_discount_capped' => $policyDiscount['capped'],
            'policy_discount_cap_percent' => $policyDiscount['cap_percent'],
            'policy_discount_type' => $policyDiscount['type'],
            'policy_discount_source' => $policyDiscount['source'],
            'distance_unit' => $distanceUnit,
            'included_distance_limit' => $includedDistanceLimit,
            'distance_overage_billing_mode' => $distanceOverageBillingMode,
            'extra_distance_unit_price' => $extraDistanceUnitPrice,
            'rental_amount' => $rentalAmount,
            'service_amount' => $serviceAmount,
            'insurance_amount' => $insuranceAmount,
            'subtotal' => $subtotal,
            'coupon_code' => $couponCode,
            'coupon_amount' => $couponAmount,
            'tax_amount' => $taxAmount,
            'tax_title' => $taxTitle,
            'fee_name' => $feeName,
            'fee_value' => $feeValue,
            'fee_amount' => $feeAmount,
            'deposit_type' => $depositType,
            'deposit_rate' => $depositRate,
            'deposit_fixed_amount' => $depositFixedAmount,
            'deposit_base_amount' => $depositBaseAmount,
            'deposit_amount' => $depositAmount,
            'deposit_risk' => $depositRisk,
            'eligibility_state' => (string) ($depositRisk['eligibility_state'] ?? 'eligible'),
            'eligibility_reasons' => $depositRisk['eligibility_reasons'] ?? [],
            'total_amount' => $totalAmount,
            'final_payable_amount' => $finalPayableAmount,
            'services' => $services,
            'insurances' => $insurances,
        ];
    }

    protected function resolveIncludedDistanceLimit(mixed $policy, int $rentalDays): ?int
    {
        if (! $policy || ! $policy->active) {
            return null;
        }

        if ($policy->included_distance_per_trip !== null) {
            return max(0, (int) $policy->included_distance_per_trip);
        }

        if ($policy->included_distance_per_day !== null) {
            return max(0, (int) $policy->included_distance_per_day) * max(1, $rentalDays);
        }

        return null;
    }

    protected function calculateServiceAmount(Collection $services, int $rentalDays): float
    {
        $serviceAmount = 0.0;

        foreach ($services as $service) {
            $price = (float) $service->price;
            if ($service->price_type == ServicePriceTypeEnum::PER_DAY) {
                $serviceAmount += $price * $rentalDays;
            } else {
                $serviceAmount += $price;
            }
        }

        return round($serviceAmount, 2);
    }

    protected function resolvePolicyDiscount(Car $car, float $baseRentalAmount, int $rentalDays): array
    {
        $policy = $car->pricingPolicy;

        if (! $policy || ! $policy->active) {
            return [
                'amount' => 0.0,
                'pre_cap_amount' => 0.0,
                'capped' => false,
                'cap_percent' => null,
                'type' => null,
                'source' => null,
            ];
        }

        $candidates = [];

        $weeklyCandidate = $this->discountCandidate(
            $rentalDays >= 7,
            (string) $policy->weekly_discount_type,
            (float) $policy->weekly_discount_value,
            $baseRentalAmount,
            'weekly'
        );

        if ($weeklyCandidate) {
            $candidates[] = $weeklyCandidate;
        }

        $monthlyCandidate = $this->discountCandidate(
            $rentalDays >= 30,
            (string) $policy->monthly_discount_type,
            (float) $policy->monthly_discount_value,
            $baseRentalAmount,
            'monthly'
        );

        if ($monthlyCandidate) {
            $candidates[] = $monthlyCandidate;
        }

        $tripDiscounts = $policy->tripDiscounts()
            ->where('active', true)
            ->where('min_days', '<=', $rentalDays)
            ->where(function ($query) use ($rentalDays): void {
                $query->whereNull('max_days')
                    ->orWhere('max_days', '>=', $rentalDays);
            })
            ->orderByDesc('priority')
            ->get();

        foreach ($tripDiscounts as $tripDiscount) {
            $candidate = $this->discountCandidate(
                true,
                (string) $tripDiscount->discount_type,
                (float) $tripDiscount->discount_value,
                $baseRentalAmount,
                'trip-rule:' . $tripDiscount->id
            );

            if ($candidate) {
                $candidates[] = $candidate;
            }
        }

        if (empty($candidates)) {
            return [
                'amount' => 0.0,
                'pre_cap_amount' => 0.0,
                'capped' => false,
                'cap_percent' => null,
                'type' => null,
                'source' => null,
            ];
        }

        $allowBestDiscountOnly = $policy->allow_best_discount_only ?? true;
        $rawAmount = 0.0;
        $discountType = null;
        $discountSource = null;

        if ($allowBestDiscountOnly) {
            usort($candidates, fn ($a, $b) => $b['amount'] <=> $a['amount']);
            $best = $candidates[0];
            $rawAmount = (float) $best['amount'];
            $discountType = $best['type'];
            $discountSource = $best['source'];
        } else {
            $rawAmount = (float) collect($candidates)->sum('amount');
            $discountType = 'combined';
            $discountSource = collect($candidates)
                ->pluck('source')
                ->unique()
                ->implode(',');
        }

        $preCapAmount = round(min($rawAmount, $baseRentalAmount), 2);
        $capPercent = $policy->max_discount_cap_percent;
        $finalAmount = $preCapAmount;
        $isCapped = false;

        if ($capPercent !== null) {
            $capPercentValue = max(0.0, (float) $capPercent);
            $capAmount = round(($baseRentalAmount * $capPercentValue) / 100, 2);
            $finalAmount = min($preCapAmount, $capAmount);
            $isCapped = $finalAmount < $preCapAmount;
            $capPercent = $capPercentValue;
        }

        return [
            'amount' => round(min($finalAmount, $baseRentalAmount), 2),
            'pre_cap_amount' => $preCapAmount,
            'capped' => $isCapped,
            'cap_percent' => $capPercent,
            'type' => $discountType,
            'source' => $discountSource,
        ];
    }

    protected function discountCandidate(
        bool $eligible,
        string $discountType,
        float $discountValue,
        float $baseRentalAmount,
        string $source
    ): ?array {
        if (! $eligible || $discountValue <= 0 || $discountType === 'none') {
            return null;
        }

        $amount = match ($discountType) {
            'percentage' => ($baseRentalAmount * $discountValue) / 100,
            'fixed' => $discountValue,
            default => 0,
        };

        if ($amount <= 0) {
            return null;
        }

        return [
            'amount' => round(min($amount, $baseRentalAmount), 2),
            'type' => $discountType,
            'source' => $source,
        ];
    }
}