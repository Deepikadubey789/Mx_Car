<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('cr_cars')) {
            return;
        }

        $constraintName = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'cr_cars')
            ->where('COLUMN_NAME', 'host_protection_plan_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        if (! $constraintName) {
            return;
        }

        Schema::table('cr_cars', function (Blueprint $table) use ($constraintName): void {
            // Drop the strict foreign key constraint that is blocking updates.
            $table->dropForeign($constraintName);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('cr_cars') || ! Schema::hasTable('cr_host_protection_plans')) {
            return;
        }

        $constraintExists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'cr_cars')
            ->where('COLUMN_NAME', 'host_protection_plan_id')
            ->where('CONSTRAINT_NAME', 'cr_cars_host_plan_fk')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();

        if ($constraintExists) {
            return;
        }

        Schema::table('cr_cars', function (Blueprint $table): void {
            // Re-apply the constraint if the migration is rolled back.
            $table->foreign('host_protection_plan_id', 'cr_cars_host_plan_fk')
                ->references('id')
                ->on('cr_host_protection_plans')
                ->nullOnDelete();
        });
    }
};