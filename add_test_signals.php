<?php
require __DIR__.'/bootstrap/app.php';

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Add test view data for past 30 days
$startDate = Carbon::now()->subDays(30);
for ($i = 0; $i < 30; $i++) {
    $date = $startDate->copy()->addDays($i);
    
    DB::table('cr_car_views')->updateOrCreate(
        [
            'car_id' => 1,
            'date' => $date->toDateString(),
        ],
        [
            'views' => rand(50, 200),
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );
}

echo "Created test view data for car 1 (past 30 days)\n";

// Show what was created
$views = DB::table('cr_car_views')->where('car_id', 1)->sum('views');
echo "Total views: $views\n";
