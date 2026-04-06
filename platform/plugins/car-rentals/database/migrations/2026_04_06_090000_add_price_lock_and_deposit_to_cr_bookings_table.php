<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table): void {
            if (! Schema::hasColumn('cr_bookings', 'price_lock_expires_at')) {
                $table->dateTime('price_lock_expires_at')->nullable()->after('tax_amount');
            }

            if (! Schema::hasColumn('cr_bookings', 'price_snapshot')) {
                $table->longText('price_snapshot')->nullable()->after('price_lock_expires_at');
            }

            if (! Schema::hasColumn('cr_bookings', 'deposit_amount')) {
                $table->decimal('deposit_amount', 15)->default(0)->after('price_snapshot');
            }

            if (! Schema::hasColumn('cr_bookings', 'deposit_type')) {
                $table->string('deposit_type', 20)->nullable()->after('deposit_amount');
            }

            if (! Schema::hasColumn('cr_bookings', 'deposit_rate')) {
                $table->decimal('deposit_rate', 8, 2)->default(0)->after('deposit_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table): void {
            $columns = [
                'price_lock_expires_at',
                'price_snapshot',
                'deposit_amount',
                'deposit_type',
                'deposit_rate',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('cr_bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
