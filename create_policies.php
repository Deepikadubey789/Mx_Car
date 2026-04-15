<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Get all cars
$cars = DB::table('cr_cars')->pluck('id');

foreach ($cars as $carId) {
    DB::table('cr_car_pricing_policies')->insert([
        'car_id' => $carId,
        'weekly_discount_type' => 'none',
        'monthly_discount_type' => 'none',
        'distance_unit' => 'km',
        'distance_overage_billing_mode' => 'end_of_trip',
        'allow_best_discount_only' => false,
        'active' => true,
        'demand_recommendations_enabled' => true,
        'demand_min_price' => 10,
        'demand_max_price' => 500,
        'demand_max_daily_change_percent' => 25,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

echo "Created " . count($cars) . " pricing policies with demand recommendations enabled!\n";
