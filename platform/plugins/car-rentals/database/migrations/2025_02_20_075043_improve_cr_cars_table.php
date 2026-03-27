<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('cr_cars', function (Blueprint $table): void {
            $table->unsignedInteger('number_of_seats')->nullable()->change();
            $table->unsignedInteger('number_of_doors')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('cr_cars', function (Blueprint $table): void {
            $table->unsignedTinyInteger('number_of_seats')->nullable()->change();
            $table->unsignedTinyInteger('number_of_doors')->nullable()->change();
        });
    }
};
