<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table) {
            $table->string('cancelled_by', 20)->nullable()->after('cancelled_at');
            // 'customer', 'host', 'admin'
        });
    }

    public function down(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table) {
            $table->dropColumn('cancelled_by');
        });
    }
};
