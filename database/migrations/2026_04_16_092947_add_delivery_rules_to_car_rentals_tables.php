<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create the Delivery Locations table (Bulletproof check)
        if (!Schema::hasTable('cr_delivery_locations')) {
            Schema::create('cr_delivery_locations', function (Blueprint $table) {
                $table->id();
                // Safe Application-Level Index instead of strict DB constraint
                $table->unsignedBigInteger('vendor_id')->nullable()->index(); 
                $table->string('name');
                $table->string('type')->default('airport'); 
                $table->decimal('fee_amount', 15, 2)->default(0); 
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->string('status', 60)->default('published');
                $table->timestamps();
            });
        }

        // 2. Add Delivery Settings to the Cars table
        if (Schema::hasTable('cr_cars')) {
            Schema::table('cr_cars', function (Blueprint $table) {
                if (!Schema::hasColumn('cr_cars', 'is_delivery_enabled')) {
                    $table->boolean('is_delivery_enabled')->default(false)->after('status');
                }
                if (!Schema::hasColumn('cr_cars', 'free_delivery_days_threshold')) {
                    $table->integer('free_delivery_days_threshold')->nullable()->after('is_delivery_enabled');
                }
                if (!Schema::hasColumn('cr_cars', 'max_delivery_distance_miles')) {
                    $table->integer('max_delivery_distance_miles')->nullable()->after('free_delivery_days_threshold');
                }
            });
        }

        // 3. Add Delivery Fees to the Bookings table
        if (Schema::hasTable('cr_bookings')) {
            Schema::table('cr_bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('cr_bookings', 'delivery_location_id')) {
                    // Safe Application-Level Index
                    $table->unsignedBigInteger('delivery_location_id')->nullable()->index()->after('amount');
                }
                if (!Schema::hasColumn('cr_bookings', 'custom_delivery_address')) {
                    $table->string('custom_delivery_address')->nullable()->after('delivery_location_id');
                }
                if (!Schema::hasColumn('cr_bookings', 'delivery_fee')) {
                    $table->decimal('delivery_fee', 15, 2)->default(0)->after('custom_delivery_address'); 
                }
            });
        }

        // 4. Create pivot table for Car <-> Delivery Locations
        if (!Schema::hasTable('cr_car_delivery_locations')) {
            Schema::create('cr_car_delivery_locations', function (Blueprint $table) {
                // Safe indices without strict DB constraints
                $table->unsignedBigInteger('car_id')->index();
                $table->unsignedBigInteger('location_id')->index();
                $table->primary(['car_id', 'location_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cr_car_delivery_locations');
        
        if (Schema::hasTable('cr_bookings')) {
            Schema::table('cr_bookings', function (Blueprint $table) {
                if (Schema::hasColumn('cr_bookings', 'delivery_location_id')) {
                    $table->dropColumn('delivery_location_id');
                }
                if (Schema::hasColumn('cr_bookings', 'custom_delivery_address')) {
                    $table->dropColumn('custom_delivery_address');
                }
                if (Schema::hasColumn('cr_bookings', 'delivery_fee')) {
                    $table->dropColumn('delivery_fee');
                }
            });
        }

        if (Schema::hasTable('cr_cars')) {
            Schema::table('cr_cars', function (Blueprint $table) {
                if (Schema::hasColumn('cr_cars', 'is_delivery_enabled')) {
                    $table->dropColumn('is_delivery_enabled');
                }
                if (Schema::hasColumn('cr_cars', 'free_delivery_days_threshold')) {
                    $table->dropColumn('free_delivery_days_threshold');
                }
                if (Schema::hasColumn('cr_cars', 'max_delivery_distance_miles')) {
                    $table->dropColumn('max_delivery_distance_miles');
                }
            });
        }

        Schema::dropIfExists('cr_delivery_locations');
    }
};