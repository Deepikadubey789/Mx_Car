<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cr_vendor_quality_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->unique();
            $table->decimal('rating_score', 5, 2)->default(0);      // avg star rating normalized to 100
            $table->decimal('completion_rate', 5, 2)->default(0);   // % bookings completed
            $table->decimal('cancellation_score', 5, 2)->default(0);// 100 - cancellation%
            $table->decimal('response_score', 5, 2)->default(0);    // based on avg_response_hours
            $table->decimal('acceptance_rate', 5, 2)->default(0);   // future use
            $table->decimal('total_score', 5, 2)->default(0);       // weighted final score
            $table->string('badge_tier', 30)->nullable();            // all_star / top_host / rising_star / null
            $table->boolean('badge_override')->default(false);       // admin ne manually set kiya
            $table->string('override_badge', 30)->nullable();        // admin ka override value
            $table->unsignedInteger('total_bookings')->default(0);
            $table->unsignedInteger('completed_bookings')->default(0);
            $table->unsignedInteger('cancelled_bookings')->default(0);
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->decimal('avg_response_hours', 8, 2)->default(0);
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('cr_customers')->onDelete('cascade');
        });

        // cr_customers mein response tracking column add karo
        Schema::table('cr_customers', function (Blueprint $table) {
            $table->decimal('avg_response_hours', 8, 2)->default(24)->after('payout_payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cr_vendor_quality_scores');
        Schema::table('cr_customers', function (Blueprint $table) {
            $table->dropColumn('avg_response_hours');
        });
    }
};