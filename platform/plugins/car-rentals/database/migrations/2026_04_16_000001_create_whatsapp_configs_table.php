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
        Schema::create('whatsapp_configs', function (Blueprint $table) {
            $table->id();
            $table->string('api_version', 30)->default('v18.0');
            $table->string('phone_number_id', 50)->unique();
            $table->string('webhook_verify_token', 255);
            $table->text('api_access_token')->nullable();
            $table->string('business_account_id', 50)->nullable();
            $table->boolean('enabled')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('phone_number_id');
            $table->index('enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_configs');
    }
};
