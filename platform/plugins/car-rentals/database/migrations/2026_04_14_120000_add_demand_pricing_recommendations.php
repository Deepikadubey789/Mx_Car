<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('cr_car_pricing_policies', function (Blueprint $table): void {
            $table->boolean('demand_recommendations_enabled')->default(false)->after('active');
            $table->decimal('demand_min_price', 15, 2)->nullable()->after('demand_recommendations_enabled');
            $table->decimal('demand_max_price', 15, 2)->nullable()->after('demand_min_price');
            $table->decimal('demand_max_daily_change_percent', 8, 2)->nullable()->after('demand_max_price');
            $table->timestamp('demand_last_generated_at')->nullable()->after('demand_max_daily_change_percent');

            $table->index(['demand_recommendations_enabled'], 'cr_cpp_demand_reco_enabled_idx');
        });

        Schema::create('cr_demand_pricing_recommendations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('car_id')->constrained('cr_cars')->cascadeOnDelete();
            $table->date('recommendation_date');
            $table->decimal('recommended_value', 15, 2);
            $table->string('value_type', 20)->default('fixed');
            $table->decimal('demand_score', 8, 4)->default(0);
            $table->decimal('confidence_score', 8, 4)->default(0);
            $table->json('reason_codes')->nullable();
            $table->string('status', 20)->default('pending');
            $table->unsignedBigInteger('applied_by')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();

            $table->unique(['car_id', 'recommendation_date'], 'cr_dpr_car_date_unique');
            $table->index(['car_id', 'status', 'recommendation_date'], 'cr_dpr_car_status_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cr_demand_pricing_recommendations');

        Schema::table('cr_car_pricing_policies', function (Blueprint $table): void {
            $table->dropIndex('cr_cpp_demand_reco_enabled_idx');
            $table->dropColumn([
                'demand_recommendations_enabled',
                'demand_min_price',
                'demand_max_price',
                'demand_max_daily_change_percent',
                'demand_last_generated_at',
            ]);
        });
    }
};
