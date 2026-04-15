<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Botble\CarRentals\Models\CarPricingPolicy;

echo "=== Car 101 Pricing Policy ===\n";
$policy = CarPricingPolicy::where('car_id', 101)->first();
if ($policy) {
    echo "Base Price: " . $policy->base_price . "\n";
    echo "Demand Min Price: " . ($policy->demand_min_price ?? 'NULL') . "\n";
    echo "Demand Max Price: " . ($policy->demand_max_price ?? 'NULL') . "\n";
    echo "Max Daily Change: " . ($policy->demand_max_daily_change_percent ?? 'NULL') . "%\n";
    echo "Last Generated: " . ($policy->demand_last_generated_at ?? 'Never') . "\n";
}

echo "\n=== Recent Recommendations (Last 5) ===\n";
$recs = DB::table('cr_demand_pricing_recommendations')
    ->where('car_id', 101)
    ->orderBy('recommendation_date', 'desc')
    ->limit(5)
    ->get();

foreach ($recs as $rec) {
    echo $rec->recommendation_date . " | Price: $" . $rec->recommended_value . " | Demand Score: " . $rec->demand_score . " | Reasons: " . ($rec->reason_codes ? implode(', ', json_decode($rec->reason_codes, true)) : 'N/A') . "\n";
}

echo "\n=== View & Booking Data ===\n";
$views = DB::table('cr_car_views')->where('car_id', 101)->sum('views');
$bookings = DB::table('cr_booking_cars')
    ->join('cr_bookings', 'cr_booking_cars.booking_id', '=', 'cr_bookings.id')
    ->where('cr_booking_cars.car_id', 101)
    ->whereNotIn('cr_bookings.status', ['cancelled', 'failed'])
    ->count();

echo "Total Views (30 days): " . $views . "\n";
echo "Total Bookings: " . $bookings . "\n";
