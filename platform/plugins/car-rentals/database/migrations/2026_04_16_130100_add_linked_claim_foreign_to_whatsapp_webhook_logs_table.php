<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('whatsapp_webhook_logs') || ! Schema::hasTable('cr_booking_claims')) {
            return;
        }

        Schema::table('whatsapp_webhook_logs', function (Blueprint $table): void {
            if (! Schema::hasColumn('whatsapp_webhook_logs', 'linked_claim_id')) {
                return;
            }

            $table->foreign('linked_claim_id')
                ->references('id')
                ->on('cr_booking_claims')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('whatsapp_webhook_logs')) {
            return;
        }

        Schema::table('whatsapp_webhook_logs', function (Blueprint $table): void {
            $table->dropForeign(['linked_claim_id']);
        });
    }
};
