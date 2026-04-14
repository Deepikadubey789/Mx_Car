<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('cr_customer_kyc_documents')) {
            return;
        }

        Schema::create('cr_customer_kyc_documents', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('verification_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('document_type', 40);
            $table->string('file_path');
            $table->string('checksum', 80)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['verification_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cr_customer_kyc_documents');
    }
};
