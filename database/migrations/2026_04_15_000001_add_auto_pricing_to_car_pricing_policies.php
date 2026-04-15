<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cr_car_pricing_policies', function (Blueprint $table) {
            // Auto-apply configuration
            $table->boolean('demand_auto_apply_enabled')->default(false)->after('demand_max_daily_change_percent');
            $table->decimal('demand_auto_apply_min_confidence', 4, 2)->default(0.70)->after('demand_auto_apply_enabled');
            $table->decimal('demand_auto_apply_max_daily_change_percent', 8, 2)->nullable()->after('demand_auto_apply_min_confidence');
            
            // Pause mechanism and tracking
            $table->timestamp('demand_auto_apply_paused_until')->nullable()->after('demand_auto_apply_max_daily_change_percent');
            $table->timestamp('demand_auto_apply_last_applied_at')->nullable()->after('demand_auto_apply_paused_until');
            $table->integer('demand_auto_apply_count')->default(0)->after('demand_auto_apply_last_applied_at');
            
            // Add index for auto-apply queries
            $table->index(['demand_auto_apply_enabled', 'car_id']);
            $table->index(['demand_auto_apply_paused_until']);
        });
    }

    public function down(): void
    {
        Schema::table('cr_car_pricing_policies', function (Blueprint $table) {
            $table->dropIndex(['demand_auto_apply_enabled', 'car_id']);
            $table->dropIndex(['demand_auto_apply_paused_until']);
            
            $table->dropColumn([
                'demand_auto_apply_enabled',
                'demand_auto_apply_min_confidence',
                'demand_auto_apply_max_daily_change_percent',
                'demand_auto_apply_paused_until',
                'demand_auto_apply_last_applied_at',
                'demand_auto_apply_count',
            ]);
        });
    }
};
