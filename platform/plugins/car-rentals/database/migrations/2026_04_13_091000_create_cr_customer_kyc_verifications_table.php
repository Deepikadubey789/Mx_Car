<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('cr_customer_kyc_verifications')) {
            return;
        }

        Schema::create('cr_customer_kyc_verifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('status', 30)->default('draft');
            $table->string('provider', 60)->default('mock');
            $table->string('provider_reference')->nullable();
            $table->decimal('ocr_confidence_score', 8, 4)->nullable();
            $table->decimal('face_match_score', 8, 4)->nullable();
            $table->decimal('risk_score', 8, 4)->nullable();
            $table->boolean('license_valid')->default(false);
            $table->date('license_expiry_date')->nullable();
            $table->string('license_number', 120)->nullable();
            $table->json('ocr_payload')->nullable();
            $table->json('provider_payload')->nullable();
            $table->json('decision_reasons')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cr_customer_kyc_verifications');
    }
};
