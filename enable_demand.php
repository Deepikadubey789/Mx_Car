<?php
require __DIR__.'/bootstrap/app.php';

$cmd = "cd ".escapeshellarg(__DIR__)." && php artisan tinker --execute \"DB::table('cr_car_pricing_policies')->update(['demand_recommendations_enabled' => 1, 'demand_min_price' => 10, 'demand_max_price' => 500, 'demand_max_daily_change_percent' => 25]); echo 'Demand recommendations enabled!'; var_dump(DB::table('cr_car_pricing_policies')->where('demand_recommendations_enabled', 1)->count());\"";
echo shell_exec($cmd);
