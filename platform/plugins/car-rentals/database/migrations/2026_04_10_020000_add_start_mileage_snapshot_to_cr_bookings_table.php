<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table): void {
            $table->unsignedInteger('start_mileage_snapshot')->nullable()->after('start_mileage');
        });
    }

    public function down(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table): void {
            $table->dropColumn('start_mileage_snapshot');
        });
    }
};