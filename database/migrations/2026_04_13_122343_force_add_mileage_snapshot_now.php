<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table) {
            // Safely force the column into the table
            if (!Schema::hasColumn('cr_bookings', 'start_mileage_snapshot')) {
                $table->unsignedInteger('start_mileage_snapshot')->nullable()->after('start_mileage');
            }
            
            // Let's also ensure these two are here just in case they are missing too!
            if (!Schema::hasColumn('cr_bookings', 'distance_overage_billing_mode')) {
                $table->string('distance_overage_billing_mode', 50)->nullable()->after('start_mileage_snapshot');
            }
            if (!Schema::hasColumn('cr_bookings', 'extra_distance_unit_price')) {
                $table->decimal('extra_distance_unit_price', 15, 2)->default(0)->after('distance_overage_billing_mode');
            }
        });
    }

    public function down(): void
    {
        // Keep empty
    }
};