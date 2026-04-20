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
        Schema::create('whatsapp_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number', 25);
            $table->string('sender_phone', 25);
            $table->string('message_id', 100)->unique();
            $table->string('status', 30)->default('received');
            $table->json('raw_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('linked_booking_id')->nullable();
            $table->unsignedBigInteger('linked_claim_id')->nullable();
            $table->unsignedBigInteger('linked_customer_id')->nullable();
            $table->json('classification_result')->nullable();
            $table->timestamps();

            $table->index(['phone_number', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['linked_booking_id', 'created_at']);
            $table->index(['linked_claim_id', 'created_at']);
            $table->foreign('linked_booking_id')->references('id')->on('cr_bookings')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_webhook_logs');
    }
};
