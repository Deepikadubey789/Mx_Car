<?php
require __DIR__.'/bootstrap/app.php';

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "Adding test booking and view signals for car 101...\n";

// Add view data for car 101 for past 30 days
for ($i = 0; $i < 30; $i++) {
    $date = Carbon::now()->subDays(30 - $i);
    DB::table('cr_car_views')->updateOrCreate(
        ['car_id' => 101, 'date' => $date->toDateString()],
        ['views' => rand(100, 300), 'created_at' => now()]
    );
}
echo "✓ Created 30 days of view data (100-300 views per day)\n";

// Add some bookings for car 101
for ($i = 0; $i < 8; $i++) {
    $startDate = Carbon::now()->addDays(rand(1, 20));
    
    $booking = DB::table('cr_bookings')->insertGetId([
        'user_id' => 1,
        'status' => 'confirmed',
        'total' => rand(300, 800),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    DB::table('cr_booking_cars')->insert([
        'booking_id' => $booking,
        'car_id' => 101,
        'rental_start_date' => $startDate->toDateString(),
        'rental_end_date' => $startDate->copy()->addDays(rand(1, 5))->toDateString(),
        'created_at' => now(),
    ]);
}
echo "✓ Created 8 test bookings with varying dates\n";

// Verify
$views = DB::table('cr_car_views')->where('car_id', 101)->sum('views');
$bookings = DB::table('cr_booking_cars')->where('car_id', 101)->count();
echo "✓ Total views: $views\n";
echo "✓ Total bookings: $bookings\n";
echo "\nNow run: php artisan car-rentals:generate-demand-pricing-recommendations --days=30 --sync\n";
echo "You should see VARYING recommended prices based on demand signals!\n";
