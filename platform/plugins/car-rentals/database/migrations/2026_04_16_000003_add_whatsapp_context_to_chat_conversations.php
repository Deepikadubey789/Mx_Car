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
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->string('context_type', 30)->nullable()->after('user_id');
            $table->unsignedBigInteger('context_id')->nullable()->after('context_type');
            $table->string('source', 30)->default('platform')->after('context_id');
            $table->json('metadata')->nullable()->after('source');

            $table->index('context_type');
            $table->index(['context_type', 'context_id']);
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropIndex(['context_type', 'context_id']);
            $table->dropIndex(['context_type']);
            $table->dropColumn(['metadata', 'source', 'context_id', 'context_type']);
        });
    }
};
