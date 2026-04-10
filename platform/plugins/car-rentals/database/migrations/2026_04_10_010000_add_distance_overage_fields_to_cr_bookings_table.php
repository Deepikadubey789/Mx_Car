<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table): void {
            $table->string('distance_unit', 20)->nullable()->after('completion_miles');
            $table->unsignedInteger('start_mileage')->nullable()->after('distance_unit');
            $table->unsignedInteger('included_distance_limit')->nullable()->after('start_mileage');
            $table->string('distance_overage_billing_mode', 30)->nullable()->after('included_distance_limit');
            $table->decimal('extra_distance_unit_price', 15, 4)->default(0)->after('distance_overage_billing_mode');
            $table->unsignedInteger('distance_travelled')->nullable()->after('extra_distance_unit_price');
            $table->unsignedInteger('distance_overage_units')->nullable()->after('distance_travelled');
            $table->decimal('distance_overage_amount', 15, 2)->default(0)->after('distance_overage_units');
        });
    }

    public function down(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table): void {
            $table->dropColumn([
                'distance_unit',
                'start_mileage',
                'included_distance_limit',
                'distance_overage_billing_mode',
                'extra_distance_unit_price',
                'distance_travelled',
                'distance_overage_units',
                'distance_overage_amount',
            ]);
        });
    }
};