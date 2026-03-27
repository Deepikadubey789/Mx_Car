<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('cr_car_dates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('car_id')->constrained('cr_cars')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('value', 15, 2)->default(0);
            $table->string('value_type', 20)->default('fixed');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();

            $table->index(['car_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cr_car_dates');
    }
};
