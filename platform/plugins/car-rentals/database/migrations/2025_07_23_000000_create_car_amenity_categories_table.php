<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('cr_car_amenity_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->string('status')->index()->default('published');
            $table->timestamps();
        });

        Schema::create('cr_car_amenity_categories_translations', function (Blueprint $table): void {
            $table->string('lang_code');
            $table->foreignId('cr_car_amenity_categories_id');
            $table->string('name')->nullable();
            $table->primary(['lang_code', 'cr_car_amenity_categories_id'], 'amenity_categories_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cr_car_amenity_categories_translations');
        Schema::dropIfExists('cr_car_amenity_categories');
    }
};
