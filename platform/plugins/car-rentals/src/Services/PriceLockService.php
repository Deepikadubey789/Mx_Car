<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Facades\CarRentalsHelper;
use Carbon\Carbon;

class PriceLockService
{
    public function getLockDurationMinutes(): int
    {
        $minutes = (int) CarRentalsHelper::getSetting('price_lock_duration_minutes', 1);

        return min(120, max(1, $minutes));
    }

    public function getDepositRate(): float
    {
        $rate = (float) CarRentalsHelper::getSetting('deposit_percentage', 20);

        return min(100, max(0, $rate));
    }

    public function getDepositType(): string
    {
        $type = (string) CarRentalsHelper::getSetting('deposit_type', 'percentage');

        return in_array($type, ['percentage', 'fixed'], true) ? $type : 'percentage';
    }

    public function getDepositFixedAmount(): float
    {
        $amount = (float) CarRentalsHelper::getSetting('deposit_fixed_amount', 0);

        return max(0, $amount);
    }

    public function getFeeName(): string
    {
        return (string) CarRentalsHelper::getSetting('fee_name', 'Service fee');
    }

    public function getFeeValue(): float
    {
        $value = (float) CarRentalsHelper::getSetting('fee_value', 0);

        return max(0, $value);
    }

    public function getExpiredMessage(): string
    {
        return (string) CarRentalsHelper::getSetting(
            'price_lock_expired_message',
            'Price lock expired or quote changed. We refreshed your total. Please review and try again.'
        );
    }

    public function calculateDepositAmount(float $subtotal): float
    {
        if ($this->getDepositType() === 'fixed') {
            return round($this->getDepositFixedAmount(), 2);
        }

        return round(($subtotal * $this->getDepositRate()) / 100, 2);
    }

    public function calculateFeeAmount(float $subtotal): float
    {
        return round($this->getFeeValue(), 2);
    }

    public function createLock(array $quote): array
    {
        $expiresAt = Carbon::now()->addMinutes($this->getLockDurationMinutes());

        $snapshot = [
            'rental_amount' => (float) ($quote['rental_amount'] ?? 0),
            'service_amount' => (float) ($quote['service_amount'] ?? 0),
            'subtotal' => (float) ($quote['subtotal'] ?? 0),
            'tax_amount' => (float) ($quote['tax_amount'] ?? 0),
            'coupon_code' => $quote['coupon_code'] ?? null,
            'coupon_amount' => (float) ($quote['coupon_amount'] ?? 0),
            'fee_name' => $quote['fee_name'] ?? $this->getFeeName(),
            'fee_value' => (float) ($quote['fee_value'] ?? $this->getFeeValue()),
            'fee_amount' => (float) ($quote['fee_amount'] ?? 0),
            'deposit_amount' => (float) ($quote['deposit_amount'] ?? 0),
            'deposit_type' => $quote['deposit_type'] ?? $this->getDepositType(),
            'deposit_rate' => (float) ($quote['deposit_rate'] ?? $this->getDepositRate()),
            'deposit_fixed_amount' => (float) ($quote['deposit_fixed_amount'] ?? $this->getDepositFixedAmount()),
            'total_amount' => (float) ($quote['total_amount'] ?? 0),
            'currency_id' => $quote['currency_id'] ?? null,
            'tax_title' => $quote['tax_title'] ?? null,
            'services' => $quote['services'] ?? [],
        ];

        return [
            'expires_at' => $expiresAt->toIso8601String(),
            'snapshot' => $snapshot,
            'snapshot_hash' => $this->snapshotHash($snapshot),
        ];
    }

    public function isExpired(?array $lock): bool
    {
        if (! $lock || empty($lock['expires_at'])) {
            return true;
        }

        return Carbon::parse($lock['expires_at'])->isPast();
    }

    public function matchesSnapshot(?array $lock, array $quote): bool
    {
        if (! $lock || empty($lock['snapshot_hash'])) {
            return false;
        }

        $snapshot = [
            'rental_amount' => (float) ($quote['rental_amount'] ?? 0),
            'service_amount' => (float) ($quote['service_amount'] ?? 0),
            'subtotal' => (float) ($quote['subtotal'] ?? 0),
            'tax_amount' => (float) ($quote['tax_amount'] ?? 0),
            'coupon_code' => $quote['coupon_code'] ?? null,
            'coupon_amount' => (float) ($quote['coupon_amount'] ?? 0),
            'fee_name' => $quote['fee_name'] ?? $this->getFeeName(),
            'fee_value' => (float) ($quote['fee_value'] ?? $this->getFeeValue()),
            'fee_amount' => (float) ($quote['fee_amount'] ?? 0),
            'deposit_amount' => (float) ($quote['deposit_amount'] ?? 0),
            'deposit_type' => $quote['deposit_type'] ?? $this->getDepositType(),
            'deposit_rate' => (float) ($quote['deposit_rate'] ?? $this->getDepositRate()),
            'deposit_fixed_amount' => (float) ($quote['deposit_fixed_amount'] ?? $this->getDepositFixedAmount()),
            'total_amount' => (float) ($quote['total_amount'] ?? 0),
            'currency_id' => $quote['currency_id'] ?? null,
            'tax_title' => $quote['tax_title'] ?? null,
            'services' => $quote['services'] ?? [],
        ];

        return hash_equals($lock['snapshot_hash'], $this->snapshotHash($snapshot));
    }

    protected function snapshotHash(array $snapshot): string
    {
        return hash('sha256', json_encode($snapshot));
    }
}
