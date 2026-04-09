<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('cr_bookings', function (Blueprint $table) {
            $table->json('pickup_photos')->nullable()->after('key_instructions_sent_at');
            $table->timestamp('pickup_photos_uploaded_at')->nullable()->after('pickup_photos');
        });
    }
    public function down(): void {
        Schema::table('cr_bookings', function (Blueprint $table) {
            $table->dropColumn(['pickup_photos', 'pickup_photos_uploaded_at']);
        });
    }
};
