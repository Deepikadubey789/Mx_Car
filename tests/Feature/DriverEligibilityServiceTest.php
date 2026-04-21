<?php

namespace Tests\Feature;

use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarCategory;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Services\DriverEligibilityService;
use Botble\Setting\Facades\Setting;
use Tests\TestCase;

class DriverEligibilityServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Setting::set('car_rentals_eligibility_restricted_category_ids', '[]');
        Setting::save();

        parent::tearDown();
    }

    public function test_guest_is_routed_to_manual_review(): void
    {
        $service = app(DriverEligibilityService::class);
        $car = new Car();
        $car->setRelation('categories', collect());

        $result = $service->evaluate($car, null);

        $this->assertSame('manual_review', $result['state']);
        $this->assertContains('guest_requires_manual_review', $result['reasons']);
    }

    public function test_guest_is_blocked_for_restricted_category_cars(): void
    {
        Setting::set('car_rentals_eligibility_restricted_category_ids', json_encode([5]));
        Setting::save();

        $service = app(DriverEligibilityService::class);
        $car = new Car();
        $category = new CarCategory();
        $category->id = 5;
        $car->setRelation('categories', collect([$category]));

        $result = $service->evaluate($car, null);

        $this->assertSame('blocked', $result['state']);
        $this->assertContains('category_requires_driver_verified_kyc', $result['reasons']);
    }

    public function test_unverified_driver_is_blocked_for_restricted_categories(): void
    {
        Setting::set('car_rentals_eligibility_restricted_category_ids', json_encode([5]));
        Setting::save();

        $customer = new Customer();
        $customer->kyc_status = 'verified';
        $customer->kyc_level = 'basic';

        $service = app(DriverEligibilityService::class);
        $car = new Car();
        $category = new CarCategory();
        $category->id = 5;
        $car->setRelation('categories', collect([$category]));

        $result = $service->evaluate($car, $customer);

        $this->assertSame('blocked', $result['state']);
        $this->assertContains('category_requires_driver_verified_kyc', $result['reasons']);
    }
}
