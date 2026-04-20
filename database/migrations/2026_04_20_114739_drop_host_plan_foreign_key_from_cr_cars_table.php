<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cr_cars', function (Blueprint $table) {
            // Drop the strict foreign key constraint that is blocking updates
            $table->dropForeign('cr_cars_host_plan_fk');
            
            // Note: We are leaving the actual 'host_protection_plan_id' column intact. 
            // We are only removing the strict database-level rule.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cr_cars', function (Blueprint $table) {
            // Re-apply the constraint if the migration is rolled back
            $table->foreign('host_protection_plan_id', 'cr_cars_host_plan_fk')
                  ->references('id')
                  ->on('cr_host_protection_plans')
                  ->onDelete('set null');
        });
    }
};