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
        Schema::create('whatsapp_sent_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('claim_id')->nullable();
            $table->string('phone_number', 25);
            $table->string('event_type', 50);
            $table->string('template_name', 100)->nullable();
            $table->text('message_content');
            $table->string('status', 30)->default('pending');
            $table->json('meta_response')->nullable();
            $table->text('error_message')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('cr_customers')->cascadeOnDelete();
            $table->foreign('booking_id')->references('id')->on('cr_bookings')->nullOnDelete();

            $table->index(['customer_id', 'created_at']);
            $table->index(['booking_id', 'event_type']);
            $table->index(['claim_id', 'event_type']);
            $table->index(['status', 'created_at']);
            $table->index(['event_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_sent_messages');
    }
};
