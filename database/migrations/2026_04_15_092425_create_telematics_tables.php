<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add hardware tracking columns to the Cars table
        Schema::table('cr_cars', function (Blueprint $table) {
            $table->string('telematics_provider')->nullable()->after('status');
            $table->string('telematics_device_id')->nullable()->after('telematics_provider')->index();
            $table->float('geofence_radius_miles')->nullable()->after('telematics_device_id');
        });

        // 2. Create the Live Logging table for breadcrumbs and claims
        Schema::create('cr_vehicle_telematics_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('cr_cars')->cascadeOnDelete();
            $table->string('event_type')->default('periodic_ping'); // e.g., 'speeding', 'geofence_exit', 'hard_braking'
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->float('speed_mph')->nullable();
            $table->float('odometer_miles')->nullable();
            $table->float('fuel_percentage')->nullable();
            $table->json('raw_payload')->nullable(); // Store the exact JSON from the provider just in case
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cr_vehicle_telematics_logs');
        Schema::table('cr_cars', function (Blueprint $table) {
            $table->dropColumn(['telematics_provider', 'telematics_device_id', 'geofence_radius_miles']);
        });
    }
};