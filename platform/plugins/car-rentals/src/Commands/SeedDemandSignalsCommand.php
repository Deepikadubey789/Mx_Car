<?php

namespace Botble\CarRentals\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedDemandSignalsCommand extends Command
{
    protected $signature = 'car-rentals:seed-demand-signals {car_id=101}';
    protected $description = 'Seed test view and booking data for demand signal testing';

    public function handle(): int
    {
        $carId = (int) $this->argument('car_id');
        
        $this->info("Seeding demand signals for car $carId...");
        
        // Get car name
        $car = DB::table('cr_cars')->find($carId);
        if (!$car) {
            $this->error("Car $carId not found!");
            return self::FAILURE;
        }
        
        // Update 30 days of view data with random values (or create if missing)
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays(30 - $i);
            DB::table('cr_car_views')
                ->where('car_id', $carId)
                ->where('date', $date->toDateString())
                ->updateOrInsert(
                    ['car_id' => $carId, 'date' => $date->toDateString()],
                    ['views' => rand(100, 300)]
                );
        }
        $this->info("✓ Updated view data for 30 days");
        
        // Add 8 bookings
        for ($i = 0; $i < 8; $i++) {
            $startDate = Carbon::now()->addDays(rand(1, 20));
            $endDate = $startDate->copy()->addDays(rand(1, 5));
            $amount = rand(300, 800);
            
            $bookingId = DB::table('cr_bookings')->insertGetId([
                'customer_id' => 1,
                'status' => 'confirmed',
                'amount' => $amount,
                'sub_total' => $amount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Use raw SQL to bypass strict column requirements
            DB::statement("INSERT INTO cr_booking_cars 
                (booking_id, car_id, car_name, price, rental_start_date, rental_end_date, currency_id, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 1, ?)", [
                $bookingId, $carId, $car->name, $amount, 
                $startDate->toDateString(), $endDate->toDateString(), now()
            ]);
        }
        $this->info("✓ Created 8 test bookings");
        
        // Verify
        $views = DB::table('cr_car_views')->where('car_id', $carId)->sum('views');
        $bookings = DB::table('cr_booking_cars')->where('car_id', $carId)->count();
        
        $this->info("✓ Total views for car $carId: $views");
        $this->info("✓ Total bookings for car $carId: $bookings");
        $this->info("\nNow run: php artisan car-rentals:generate-demand-pricing-recommendations --days=30 --sync");
        
        return self::SUCCESS;
    }
}
