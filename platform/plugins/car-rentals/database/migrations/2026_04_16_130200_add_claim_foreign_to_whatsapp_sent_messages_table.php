<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('whatsapp_sent_messages') || ! Schema::hasTable('cr_booking_claims')) {
            return;
        }

        Schema::table('whatsapp_sent_messages', function (Blueprint $table): void {
            if (! Schema::hasColumn('whatsapp_sent_messages', 'claim_id')) {
                return;
            }

            $table->foreign('claim_id')
                ->references('id')
                ->on('cr_booking_claims')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('whatsapp_sent_messages')) {
            return;
        }

        Schema::table('whatsapp_sent_messages', function (Blueprint $table): void {
            $table->dropForeign(['claim_id']);
        });
    }
};
