<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table): void {
            $table->json('after_photos')->nullable()->after('pickup_photos_uploaded_at');
            $table->timestamp('after_photos_uploaded_at')->nullable()->after('after_photos');
        });
    }

    public function down(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table): void {
            $table->dropColumn(['after_photos', 'after_photos_uploaded_at']);
        });
    }
};