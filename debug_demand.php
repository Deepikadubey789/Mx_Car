<?php
require __DIR__.'/platform/plugins/car-rentals/src/Services/DemandPricingRecommendationService.php';
require __DIR__.'/platform/plugins/car-rentals/src/Models/DemandPricingRecommendation.php';
require __DIR__.'/platform/plugins/car-rentals/src/Models/Car.php';

// Load Illuminate
$autoload = require __DIR__.'/vendor/autoload.php';

// Set up laravel
\Dotenv\Dotenv::createImmutable(__DIR__)->load();

$app = require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarPricingPolicy;

// Check policies
$enabledCount = DB::table('cr_car_pricing_policies')->where('demand_recommendations_enabled', 1)->count();
echo "Policies with recommendations enabled: $enabledCount\n";

// Check cars
$carCount = Car::count();
echo "Total cars: $carCount\n";

// Check if a car has pricing policy
$carsWithPolicy = Car::whereNotNull('pricing_policy_id')->count();
echo "Cars with pricing policy:  $carsWithPolicy\n";

$carsWithEnabledPolicy = Car::whereHas('pricingPolicy', function ($q) {
    $q->where('demand_recommendations_enabled', 1);
})->count();
echo "Cars with enabled policies: $carsWithEnabledPolicy\n";

// Try to understand the relationship
if ($carsWithEnabledPolicy > 0) {
    $car = Car::whereHas('pricingPolicy', function ($q) {
        $q->where('demand_recommendations_enabled', 1);
    })->first();
    echo "\nCar ID: " . $car->id . "\n";
    echo "Policy: " . ($car->pricingPolicy ? "ID " . $car->pricingPolicy->id : "None") . "\n";
    echo "Base rate: " . $car->getCarRentalPrice() . "\n";
}
