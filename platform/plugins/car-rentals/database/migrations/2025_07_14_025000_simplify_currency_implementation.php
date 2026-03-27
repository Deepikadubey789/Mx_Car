<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // Since the system already has currency_id columns in most tables,
        // we just need to ensure they are properly set up

        // 1. First populate default currency for existing records
        $defaultCurrency = DB::table('cr_currencies')->where('is_default', 1)->first();

        if ($defaultCurrency) {
            // Update bookings with null currency_id
            DB::table('cr_bookings')
                ->whereNull('currency_id')
                ->update(['currency_id' => $defaultCurrency->id]);

            // Update booking_cars with null currency_id
            DB::table('cr_booking_cars')
                ->whereNull('currency_id')
                ->update(['currency_id' => $defaultCurrency->id]);

            // Update invoices with null currency_id
            DB::table('cr_invoices')
                ->whereNull('currency_id')
                ->update(['currency_id' => $defaultCurrency->id]);

            // Update car maintenance histories with null currency_id
            DB::table('cr_car_maintenance_histories')
                ->whereNull('currency_id')
                ->update(['currency_id' => $defaultCurrency->id]);
        }

        // 2. Add currency_id to cr_cars if it doesn't exist
        if (! Schema::hasColumn('cr_cars', 'currency_id')) {
            Schema::table('cr_cars', function (Blueprint $table): void {
                $table->foreignId('currency_id')->nullable()->after('rental_rate');
            });

            // Set default currency for existing cars
            if ($defaultCurrency) {
                DB::table('cr_cars')->update(['currency_id' => $defaultCurrency->id]);
            }
        }

        // 3. Clean up any duplicate currency columns
        if (Schema::hasColumn('cr_bookings', 'currency')) {
            Schema::table('cr_bookings', function (Blueprint $table): void {
                $table->dropColumn('currency');
            });
        }

        if (Schema::hasColumn('cr_bookings', 'new_currency_id')) {
            Schema::table('cr_bookings', function (Blueprint $table): void {
                $table->dropColumn('new_currency_id');
            });
        }
    }

    public function down(): void
    {
        // Remove currency_id from cr_cars
        if (Schema::hasColumn('cr_cars', 'currency_id')) {
            Schema::table('cr_cars', function (Blueprint $table): void {
                $table->dropColumn('currency_id');
            });
        }
    }
};
