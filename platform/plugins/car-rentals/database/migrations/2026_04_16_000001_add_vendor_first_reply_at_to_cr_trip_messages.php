<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cr_trip_messages', function (Blueprint $table) {
            $table->timestamp('vendor_first_reply_at')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('cr_trip_messages', function (Blueprint $table) {
            $table->dropColumn('vendor_first_reply_at');
        });
    }
};
