<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('cr_customers')) {
            return;
        }

        Schema::table('cr_customers', function (Blueprint $table): void {
            if (! Schema::hasColumn('cr_customers', 'kyc_status')) {
                $table->string('kyc_status', 30)->default('not_submitted')->after('is_verified');
            }

            if (! Schema::hasColumn('cr_customers', 'kyc_level')) {
                $table->string('kyc_level', 30)->nullable()->after('kyc_status');
            }

            if (! Schema::hasColumn('cr_customers', 'kyc_last_verified_at')) {
                $table->dateTime('kyc_last_verified_at')->nullable()->after('kyc_level');
            }

            if (! Schema::hasColumn('cr_customers', 'kyc_current_verification_id')) {
                $table->unsignedBigInteger('kyc_current_verification_id')->nullable()->after('kyc_last_verified_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cr_customers')) {
            return;
        }

        Schema::table('cr_customers', function (Blueprint $table): void {
            foreach (['kyc_status', 'kyc_level', 'kyc_last_verified_at', 'kyc_current_verification_id'] as $column) {
                if (Schema::hasColumn('cr_customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
