<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table) {
            $table->timestamp('original_end_date')->nullable()->after('damage_settled_at');
            $table->string('modification_type')->nullable()->after('original_end_date');
            $table->string('modification_status')->nullable()->after('modification_type');
            $table->text('modification_reason')->nullable()->after('modification_status');
            $table->timestamp('modified_at')->nullable()->after('modification_reason');
            $table->string('cancellation_policy')->nullable()->after('modified_at');
            $table->decimal('refund_amount', 10, 2)->nullable()->after('cancellation_policy');
            $table->text('cancellation_reason')->nullable()->after('refund_amount');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
        });
    }

    public function down(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table) {
            $table->dropColumn([
                'original_end_date',
                'modification_type',
                'modification_status',
                'modification_reason',
                'modified_at',
                'cancellation_policy',
                'refund_amount',
                'cancellation_reason',
                'cancelled_at',
            ]);
        });
    }
};