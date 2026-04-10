<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('cr_car_pricing_policies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('car_id')->unique()->constrained('cr_cars')->cascadeOnDelete();
            $table->string('weekly_discount_type', 20)->default('none');
            $table->decimal('weekly_discount_value', 15, 2)->default(0);
            $table->string('monthly_discount_type', 20)->default('none');
            $table->decimal('monthly_discount_value', 15, 2)->default(0);
            $table->unsignedInteger('included_distance_per_day')->nullable();
            $table->unsignedInteger('included_distance_per_trip')->nullable();
            $table->decimal('extra_distance_unit_price', 15, 4)->default(0);
            $table->string('distance_unit', 20)->default('km');
            $table->string('distance_overage_billing_mode', 30)->default('end_of_trip');
            $table->boolean('allow_best_discount_only')->default(true);
            $table->decimal('max_discount_cap_percent', 8, 2)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['car_id', 'active']);
        });

        Schema::create('cr_car_trip_discounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('car_id')->constrained('cr_cars')->cascadeOnDelete();
            $table->foreignId('car_pricing_policy_id')->constrained('cr_car_pricing_policies')->cascadeOnDelete();
            $table->unsignedInteger('min_days')->default(1);
            $table->unsignedInteger('max_days')->nullable();
            $table->string('discount_type', 20);
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['car_id', 'active']);
            $table->index(['min_days', 'max_days']);
            $table->index(['car_pricing_policy_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cr_car_trip_discounts');
        Schema::dropIfExists('cr_car_pricing_policies');
    }
};