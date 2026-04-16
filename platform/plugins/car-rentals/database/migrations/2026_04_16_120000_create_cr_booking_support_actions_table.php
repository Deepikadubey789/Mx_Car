<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cr_booking_support_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('cr_bookings')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 120);
            $table->text('note')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cr_booking_support_actions');
    }
};
