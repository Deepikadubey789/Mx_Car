<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cr_booking_claims')) {
            return;
        }

        Schema::create('cr_booking_claims', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_id')->constrained('cr_bookings')->cascadeOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('open');
            $table->string('category', 60)->default('general');
            $table->decimal('claimed_amount', 12, 2)->nullable();
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->text('reason')->nullable();
            $table->text('resolution_note')->nullable();
            $table->json('evidence')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
            $table->index(['assignee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cr_booking_claims');
    }
};
