<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_sent_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('whatsapp_sent_messages', 'provider_message_id')) {
                $table->string('provider_message_id', 191)->nullable()->after('status');
                $table->index('provider_message_id');
            }

            if (! Schema::hasColumn('whatsapp_sent_messages', 'status_updated_at')) {
                $table->dateTime('status_updated_at')->nullable()->after('sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_sent_messages', function (Blueprint $table) {
            if (Schema::hasColumn('whatsapp_sent_messages', 'provider_message_id')) {
                $table->dropIndex(['provider_message_id']);
                $table->dropColumn('provider_message_id');
            }

            if (Schema::hasColumn('whatsapp_sent_messages', 'status_updated_at')) {
                $table->dropColumn('status_updated_at');
            }
        });
    }
};
