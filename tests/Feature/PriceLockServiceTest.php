<?php

namespace Tests\Feature;

use Botble\CarRentals\Services\PriceLockService;
use Botble\Setting\Facades\Setting;
use Carbon\Carbon;
use Tests\TestCase;

class PriceLockServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Setting::set('car_rentals_deposit_type', 'percentage');
        Setting::set('car_rentals_deposit_fixed_amount', 0);
        Setting::set('car_rentals_fee_name', 'Service fee');
        Setting::set('car_rentals_fee_value', 0);
        Setting::save();

        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_creates_a_lock_with_expiry_and_snapshot(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-06 10:00:00'));

        $service = app(PriceLockService::class);
        $lock = $service->createLock([
            'rental_amount' => 100,
            'service_amount' => 25,
            'subtotal' => 125,
            'tax_amount' => 12.5,
            'coupon_code' => 'PROMO10',
            'coupon_amount' => 10,
            'fee_name' => 'Service fee',
            'fee_type' => 'fixed',
            'fee_value' => 15,
            'fee_amount' => 15,
            'deposit_amount' => 25,
            'deposit_base_amount' => 20,
            'deposit_type' => 'percentage',
            'deposit_rate' => 20,
            'deposit_fixed_amount' => 0,
            'deposit_risk_multiplier' => 1.25,
            'deposit_risk_level' => 'medium',
            'total_amount' => 152.5,
            'currency_id' => 1,
            'tax_title' => 'VAT',
            'services' => [
                ['id' => 1, 'name' => 'GPS', 'price' => 25],
            ],
        ]);

        $this->assertArrayHasKey('expires_at', $lock);
        $this->assertArrayHasKey('snapshot', $lock);
        $this->assertArrayHasKey('snapshot_hash', $lock);
        $this->assertSame('Service fee', $lock['snapshot']['fee_name']);
        $this->assertSame(15.0, $lock['snapshot']['fee_amount']);
        $this->assertSame('percentage', $lock['snapshot']['deposit_type']);
        $this->assertSame(20.0, $lock['snapshot']['deposit_rate']);
        $this->assertSame(20.0, $lock['snapshot']['deposit_base_amount']);
        $this->assertSame(25.0, $lock['snapshot']['deposit_amount']);
        $this->assertSame(1.25, $lock['snapshot']['deposit_risk_multiplier']);
        $this->assertSame('medium', $lock['snapshot']['deposit_risk_level']);
        $this->assertSame(152.5, $lock['snapshot']['total_amount']);
    }

    public function test_it_detects_expired_locks(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-06 10:00:00'));

        $service = app(PriceLockService::class);
        $lock = $service->createLock([
            'rental_amount' => 100,
            'service_amount' => 0,
            'subtotal' => 100,
            'tax_amount' => 10,
            'coupon_amount' => 0,
            'fee_amount' => 0,
            'deposit_amount' => 20,
            'deposit_base_amount' => 20,
            'deposit_type' => 'percentage',
            'deposit_rate' => 20,
            'deposit_fixed_amount' => 0,
            'deposit_risk_multiplier' => 1,
            'deposit_risk_level' => 'low',
            'total_amount' => 130,
            'currency_id' => 1,
            'services' => [],
        ]);

        $this->assertFalse($service->isExpired($lock));

        Carbon::setTestNow(Carbon::parse('2026-04-06 10:16:00'));

        $this->assertTrue($service->isExpired($lock));
    }

    public function test_it_compares_snapshots(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-06 10:00:00'));

        $service = app(PriceLockService::class);
        $quote = [
            'rental_amount' => 100,
            'service_amount' => 25,
            'subtotal' => 125,
            'tax_amount' => 12.5,
            'coupon_code' => 'PROMO10',
            'coupon_amount' => 10,
            'fee_name' => 'Service fee',
            'fee_type' => 'fixed',
            'fee_value' => 15,
            'fee_amount' => 15,
            'deposit_amount' => 25,
            'deposit_base_amount' => 20,
            'deposit_type' => 'percentage',
            'deposit_rate' => 20,
            'deposit_fixed_amount' => 0,
            'deposit_risk_multiplier' => 1.25,
            'deposit_risk_level' => 'medium',
            'total_amount' => 152.5,
            'currency_id' => 1,
            'tax_title' => 'VAT',
            'services' => [
                ['id' => 1, 'name' => 'GPS', 'price' => 25],
            ],
        ];

        $lock = $service->createLock($quote);

        $this->assertTrue($service->matchesSnapshot($lock, $quote));

        $quote['total_amount'] = 160;
        $this->assertFalse($service->matchesSnapshot($lock, $quote));
    }

    public function test_it_calculates_fixed_deposit_amount_when_deposit_type_is_fixed(): void
    {
        Setting::set('car_rentals_deposit_type', 'fixed');
        Setting::set('car_rentals_deposit_fixed_amount', 55);
        Setting::save();

        $service = app(PriceLockService::class);

        $this->assertSame(55.0, $service->calculateDepositAmount(300));
    }

    public function test_it_calculates_fixed_fee_amount(): void
    {
        Setting::set('car_rentals_fee_value', 15);
        Setting::save();

        $service = app(PriceLockService::class);

        $this->assertSame(15.0, $service->calculateFeeAmount(250));
    }
}
