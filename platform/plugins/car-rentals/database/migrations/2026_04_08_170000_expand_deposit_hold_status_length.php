<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('cr_bookings') || ! Schema::hasColumn('cr_bookings', 'deposit_hold_status')) {
            return;
        }

        DB::statement('ALTER TABLE `cr_bookings` MODIFY `deposit_hold_status` VARCHAR(50) NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('cr_bookings') || ! Schema::hasColumn('cr_bookings', 'deposit_hold_status')) {
            return;
        }

        DB::statement('ALTER TABLE `cr_bookings` MODIFY `deposit_hold_status` VARCHAR(30) NULL');
    }
};
