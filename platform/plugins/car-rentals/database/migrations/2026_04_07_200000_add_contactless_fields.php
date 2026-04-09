<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('cr_cars', function (Blueprint $table) {
            $table->text('default_pickup_instructions')->nullable()->after('insurance_info');
        });
        Schema::table('cr_bookings', function (Blueprint $table) {
            $table->text('key_instructions')->nullable()->after('completion_notes');
            $table->timestamp('key_instructions_sent_at')->nullable()->after('key_instructions');
        });
    }
    public function down(): void {
        Schema::table('cr_cars', function (Blueprint $table) {
            $table->dropColumn('default_pickup_instructions');
        });
        Schema::table('cr_bookings', function (Blueprint $table) {
            $table->dropColumn(['key_instructions', 'key_instructions_sent_at']);
        });
    }
};
