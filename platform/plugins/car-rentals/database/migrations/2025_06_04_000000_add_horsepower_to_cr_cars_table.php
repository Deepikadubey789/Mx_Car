<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('cr_cars', 'horsepower')) {
            return;
        }

        Schema::table('cr_cars', function (Blueprint $table): void {
            $table->decimal('horsepower', 8, 2)->nullable()->after('mileage');
        });
    }

    public function down(): void
    {
        Schema::table('cr_cars', function (Blueprint $table): void {
            $table->dropColumn('horsepower');
        });
    }
};
