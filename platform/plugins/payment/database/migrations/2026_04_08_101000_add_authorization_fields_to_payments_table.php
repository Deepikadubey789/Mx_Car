<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('payments')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table): void {
            if (! Schema::hasColumn('payments', 'is_authorized_hold')) {
                $table->boolean('is_authorized_hold')->default(false)->after('refund_note');
            }

            if (! Schema::hasColumn('payments', 'authorized_amount')) {
                $table->decimal('authorized_amount', 15)->nullable()->after('is_authorized_hold');
            }

            if (! Schema::hasColumn('payments', 'captured_amount')) {
                $table->decimal('captured_amount', 15)->nullable()->after('authorized_amount');
            }

            if (! Schema::hasColumn('payments', 'released_amount')) {
                $table->decimal('released_amount', 15)->nullable()->after('captured_amount');
            }

            if (! Schema::hasColumn('payments', 'authorized_at')) {
                $table->dateTime('authorized_at')->nullable()->after('released_amount');
            }

            if (! Schema::hasColumn('payments', 'captured_at')) {
                $table->dateTime('captured_at')->nullable()->after('authorized_at');
            }

            if (! Schema::hasColumn('payments', 'released_at')) {
                $table->dateTime('released_at')->nullable()->after('captured_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('payments')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table): void {
            $columns = [
                'is_authorized_hold',
                'authorized_amount',
                'captured_amount',
                'released_amount',
                'authorized_at',
                'captured_at',
                'released_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
