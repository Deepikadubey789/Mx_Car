<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table): void {
            if (! Schema::hasColumn('cr_bookings', 'fee_name')) {
                $table->string('fee_name', 120)->nullable()->after('price_snapshot');
            }

            if (! Schema::hasColumn('cr_bookings', 'fee_value')) {
                $table->decimal('fee_value', 15, 2)->default(0)->after('fee_name');
            }

            if (! Schema::hasColumn('cr_bookings', 'fee_amount')) {
                $table->decimal('fee_amount', 15, 2)->default(0)->after('fee_value');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table): void {
            $columns = [
                'fee_name',
                'fee_value',
                'fee_amount',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('cr_bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};