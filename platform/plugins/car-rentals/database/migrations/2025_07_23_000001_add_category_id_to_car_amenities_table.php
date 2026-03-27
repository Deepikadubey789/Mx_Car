<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('cr_car_amenities', function (Blueprint $table): void {
            if (! Schema::hasColumn('cr_car_amenities', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cr_car_amenities', function (Blueprint $table): void {
            $table->dropColumn('category_id');
        });
    }
};
