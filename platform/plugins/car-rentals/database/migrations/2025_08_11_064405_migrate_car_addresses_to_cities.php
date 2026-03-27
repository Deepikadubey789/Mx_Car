<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // First, add new columns to cr_booking_cars
        Schema::table('cr_booking_cars', function (Blueprint $table): void {
            if (! Schema::hasColumn('cr_booking_cars', 'pickup_city_id')) {
                $table->unsignedBigInteger('pickup_city_id')->nullable()->after('car_id');
            }
            if (! Schema::hasColumn('cr_booking_cars', 'return_city_id')) {
                $table->unsignedBigInteger('return_city_id')->nullable()->after('pickup_city_id');
            }
        });

        // Migrate existing data from car_addresses to cities
        if (Schema::hasTable('cr_car_addresses') && Schema::hasColumn('cr_booking_cars', 'pickup_address_id')) {
            DB::statement('
                UPDATE cr_booking_cars bc
                JOIN cr_car_addresses ca ON bc.pickup_address_id = ca.id
                SET bc.pickup_city_id = ca.city_id
                WHERE bc.pickup_address_id IS NOT NULL
            ');

            DB::statement('
                UPDATE cr_booking_cars bc
                JOIN cr_car_addresses ca ON bc.return_address_id = ca.id
                SET bc.return_city_id = ca.city_id
                WHERE bc.return_address_id IS NOT NULL
            ');
        }

        // Drop old columns from cr_booking_cars
        Schema::table('cr_booking_cars', function (Blueprint $table): void {
            if (Schema::hasColumn('cr_booking_cars', 'pickup_address_id')) {
                $table->dropColumn('pickup_address_id');
            }
            if (Schema::hasColumn('cr_booking_cars', 'return_address_id')) {
                $table->dropColumn('return_address_id');
            }
        });

        // Drop the cr_car_addresses table
        Schema::dropIfExists('cr_car_addresses');
    }

    public function down(): void
    {
        // Recreate cr_car_addresses table
        Schema::create('cr_car_addresses', function (Blueprint $table): void {
            $table->id();
            $table->string('detail_address', 300)->nullable(false)->default('');
            $table->string('status', 30)->default('published');
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->integer('order')->unsigned()->default(0);
            $table->timestamps();
        });

        // Add back old columns to cr_booking_cars
        Schema::table('cr_booking_cars', function (Blueprint $table): void {
            if (! Schema::hasColumn('cr_booking_cars', 'pickup_address_id')) {
                $table->unsignedBigInteger('pickup_address_id')->nullable()->after('car_id');
            }
            if (! Schema::hasColumn('cr_booking_cars', 'return_address_id')) {
                $table->unsignedBigInteger('return_address_id')->nullable()->after('pickup_address_id');
            }
        });

        // Drop new columns from cr_booking_cars
        Schema::table('cr_booking_cars', function (Blueprint $table): void {
            if (Schema::hasColumn('cr_booking_cars', 'pickup_city_id')) {
                $table->dropColumn('pickup_city_id');
            }
            if (Schema::hasColumn('cr_booking_cars', 'return_city_id')) {
                $table->dropColumn('return_city_id');
            }
        });
    }
};
