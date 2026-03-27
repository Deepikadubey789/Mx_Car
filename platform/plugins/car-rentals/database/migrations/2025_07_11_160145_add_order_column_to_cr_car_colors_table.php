<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('cr_car_colors', 'order')) {
            Schema::table('cr_car_colors', function (Blueprint $table): void {
                $table->tinyInteger('order')->default(0)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cr_car_colors', 'order')) {
            Schema::table('cr_car_colors', function (Blueprint $table): void {
                $table->dropColumn('order');
            });
        }
    }
};
