<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cr_trip_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('cr_bookings')->onDelete('cascade');
            $table->nullableMorphs('sender');
            $table->text('message')->nullable();
            $table->string('type', 60)->default('user_message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cr_trip_messages');
    }
};
