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
            if (! Schema::hasColumn('cr_bookings', 'deposit_base_amount')) {
                $table->decimal('deposit_base_amount', 15)->nullable()->after('fee_amount');
            }

            if (! Schema::hasColumn('cr_bookings', 'deposit_risk_multiplier')) {
                $table->decimal('deposit_risk_multiplier', 8, 4)->nullable()->after('deposit_rate');
            }

            if (! Schema::hasColumn('cr_bookings', 'deposit_risk_level')) {
                $table->string('deposit_risk_level', 20)->nullable()->after('deposit_risk_multiplier');
            }

            if (! Schema::hasColumn('cr_bookings', 'deposit_risk_reasons')) {
                $table->text('deposit_risk_reasons')->nullable()->after('deposit_risk_level');
            }

            if (! Schema::hasColumn('cr_bookings', 'deposit_hold_status')) {
                $table->string('deposit_hold_status', 50)->nullable()->after('deposit_risk_reasons');
            }

            if (! Schema::hasColumn('cr_bookings', 'deposit_hold_amount')) {
                $table->decimal('deposit_hold_amount', 15)->nullable()->after('deposit_hold_status');
            }

            if (! Schema::hasColumn('cr_bookings', 'deposit_authorized_at')) {
                $table->dateTime('deposit_authorized_at')->nullable()->after('deposit_hold_amount');
            }

            if (! Schema::hasColumn('cr_bookings', 'deposit_settled_at')) {
                $table->dateTime('deposit_settled_at')->nullable()->after('deposit_authorized_at');
            }

            if (! Schema::hasColumn('cr_bookings', 'deposit_captured_amount')) {
                $table->decimal('deposit_captured_amount', 15)->nullable()->after('deposit_settled_at');
            }

            if (! Schema::hasColumn('cr_bookings', 'deposit_released_amount')) {
                $table->decimal('deposit_released_amount', 15)->nullable()->after('deposit_captured_amount');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cr_bookings')) {
            return;
        }

        Schema::table('cr_bookings', function (Blueprint $table): void {
            $columns = [
                'deposit_base_amount',
                'deposit_risk_multiplier',
                'deposit_risk_level',
                'deposit_risk_reasons',
                'deposit_hold_status',
                'deposit_hold_amount',
                'deposit_authorized_at',
                'deposit_settled_at',
                'deposit_captured_amount',
                'deposit_released_amount',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('cr_bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
