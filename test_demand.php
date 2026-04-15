<?php
require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';

use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarPricingPolicy;

$car = Car::first();
if ($car) {
    echo "Car ID: " . $car->id . "\n";
    $policy = $car->pricingPolicy;
    if ($policy) {
        echo "Policy ID: " . $policy->id . "\n";
        echo "Demand recommendations enabled: " . ($policy->demand_recommendations_enabled ? 'Yes' : 'No') . "\n";
        
        // Enable recommendations
        $policy->update([
            'demand_recommendations_enabled' => true,
            'demand_min_price' => 10,
            'demand_max_price' => 500,
            'demand_max_daily_change_percent' => 25
        ]);
        
        echo "Updated successfully!\n";
        $policy->refresh();
        echo "Demand recommendations enabled: " . ($policy->demand_recommendations_enabled ? 'Yes' : 'No') . "\n";
    } else {
        echo "No pricing policy for this car\n";
    }
} else {
    echo "No cars found\n";
}
