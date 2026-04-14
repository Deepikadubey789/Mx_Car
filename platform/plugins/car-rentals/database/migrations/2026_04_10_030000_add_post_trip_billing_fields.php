<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table) {
            $table->string('checkin_fuel_level')->nullable()->after('completion_gas_level');
            $table->decimal('fuel_difference_charge', 10, 2)->default(0)->after('checkin_fuel_level');
            $table->timestamp('actual_return_datetime')->nullable()->after('fuel_difference_charge');
            $table->decimal('late_fee_charge', 10, 2)->default(0)->after('actual_return_datetime');
            $table->decimal('damage_amount', 10, 2)->default(0)->after('late_fee_charge');
            $table->string('damage_status')->nullable()->after('damage_amount');
            $table->text('damage_dispute_reason')->nullable()->after('damage_status');
            $table->timestamp('damage_settled_at')->nullable()->after('damage_dispute_reason');
        });

        Schema::table('cr_cars', function (Blueprint $table) {
            $table->decimal('fuel_rate_per_liter', 10, 2)->default(0)->after('mileage');
            $table->decimal('late_fee_per_hour', 10, 2)->default(0)->after('fuel_rate_per_liter');
        });
    }

    public function down(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table) {
            $table->dropColumn([
                'checkin_fuel_level',
                'fuel_difference_charge',
                'actual_return_datetime',
                'late_fee_charge',
                'damage_amount',
                'damage_status',
                'damage_dispute_reason',
                'damage_settled_at',
            ]);
        });

        Schema::table('cr_cars', function (Blueprint $table) {
            $table->dropColumn([
                'fuel_rate_per_liter',
                'late_fee_per_hour',
            ]);
        });
    }
};