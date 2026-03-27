<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table): void {
            $table->unsignedInteger('completion_miles')->nullable()->after('note');
            $table->string('completion_gas_level', 30)->nullable()->after('completion_miles');
            $table->text('completion_damage_images')->nullable()->after('completion_gas_level');
            $table->text('completion_notes')->nullable()->after('completion_damage_images');
            $table->timestamp('completed_at')->nullable()->after('completion_notes');
        });
    }

    public function down(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table): void {
            $table->dropColumn([
                'completion_miles',
                'completion_gas_level',
                'completion_damage_images',
                'completion_notes',
                'completed_at',
            ]);
        });
    }
};
