<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('cr_bookings')) {
            return;
        }

        Schema::table('cr_bookings', function (Blueprint $table): void {
            if (! Schema::hasColumn('cr_bookings', 'eligibility_state')) {
                $table->string('eligibility_state', 30)->nullable()->after('deposit_risk_reasons');
            }

            if (! Schema::hasColumn('cr_bookings', 'eligibility_reasons')) {
                $table->text('eligibility_reasons')->nullable()->after('eligibility_state');
            }

            if (! Schema::hasColumn('cr_bookings', 'kyc_verification_id')) {
                $table->unsignedBigInteger('kyc_verification_id')->nullable()->after('customer_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cr_bookings')) {
            return;
        }

        Schema::table('cr_bookings', function (Blueprint $table): void {
            foreach (['eligibility_state', 'eligibility_reasons', 'kyc_verification_id'] as $column) {
                if (Schema::hasColumn('cr_bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
