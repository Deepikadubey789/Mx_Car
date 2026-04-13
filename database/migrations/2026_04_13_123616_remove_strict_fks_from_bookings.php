<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Temporarily turn off checks so we can safely remove the constraints
        Schema::disableForeignKeyConstraints();

        // Safely try to drop the explicit named constraints we made earlier
        try {
            Schema::table('cr_bookings', function (Blueprint $table) {
                $table->dropForeign('cr_bookings_guest_plan_fk');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('cr_bookings', function (Blueprint $table) {
                $table->dropForeign('cr_bookings_host_plan_fk');
            });
        } catch (\Exception $e) {}

        // Safely try to drop the default Laravel named constraints just in case
        try {
            Schema::table('cr_bookings', function (Blueprint $table) {
                $table->dropForeign(['guest_protection_plan_id']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('cr_bookings', function (Blueprint $table) {
                $table->dropForeign(['host_protection_plan_id']);
            });
        } catch (\Exception $e) {}

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Forward-only patch
    }
};