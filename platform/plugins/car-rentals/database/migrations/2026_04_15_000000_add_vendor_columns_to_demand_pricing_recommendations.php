<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cr_demand_pricing_recommendations', function (Blueprint $table) {
            // Track if vendor adjusted the recommendation (store the delta)
            $table->float('adjustment_applied', 10, 2)->nullable()->after('expires_at')->comment('Price adjustment applied by vendor (+/- from recommended)');

            // Vendor notes on why they adjusted
            $table->text('vendor_adjustment_notes')->nullable()->after('adjustment_applied')->comment('Vendor notes explaining their adjustment');

            // Vendor notes on why they accepted/rejected
            $table->text('vendor_notes')->nullable()->after('vendor_adjustment_notes')->comment('Vendor notes on acceptance/rejection');

            // Track rejection reason for algorithm learning
            $table->enum('rejected_reason', ['too_high', 'too_low', 'inventory_issue', 'not_applicable', 'other'])->nullable()->after('vendor_notes')->comment('Why vendor rejected (for feedback loop)');

            // Capture baseline price at recommendation time (7-day rolling average)
            $table->float('local_baseline_price', 10, 2)->nullable()->after('rejected_reason')->comment('7-day average price at recommendation time (for comparison)');

            // Estimated revenue impact if recommendation was applied
            $table->float('estimated_revenue_impact', 12, 2)->nullable()->after('local_baseline_price')->comment('Est. revenue uplift if applied (calculated at generation)');

            // Index for vendor lookups
            $table->index('car_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('cr_demand_pricing_recommendations', function (Blueprint $table) {
            $table->dropIndex(['car_id']);
            $table->dropIndex(['status']);
            $table->dropColumn([
                'adjustment_applied',
                'vendor_adjustment_notes',
                'vendor_notes',
                'rejected_reason',
                'local_baseline_price',
                'estimated_revenue_impact',
            ]);
        });
    }
};
