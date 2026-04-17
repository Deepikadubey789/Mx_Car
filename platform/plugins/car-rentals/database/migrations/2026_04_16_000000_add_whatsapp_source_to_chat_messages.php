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
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->string('source', 30)->default('platform')->after('content');
            $table->string('provider_message_id')->nullable()->after('source');
            $table->string('phone_number', 25)->nullable()->after('provider_message_id');
            $table->bigInteger('timestamp_ms')->nullable()->after('phone_number');
            $table->json('whatsapp_metadata')->nullable()->after('timestamp_ms');

            $table->index('source');
            $table->index(['conversation_id', 'source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex(['conversation_id', 'source']);
            $table->dropIndex(['source']);
            $table->dropColumn(['whatsapp_metadata', 'timestamp_ms', 'phone_number', 'provider_message_id', 'source']);
        });
    }
};
