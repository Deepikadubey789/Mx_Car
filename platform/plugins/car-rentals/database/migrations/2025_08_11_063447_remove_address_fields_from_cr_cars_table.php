<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('cr_cars', function (Blueprint $table): void {
            // Drop indexes first if they exist
            $indexes = ['cr_pick_address_index', 'cr_return_address_index'];

            foreach ($indexes as $index) {
                if (Schema::hasIndex('cr_cars', $index)) {
                    $table->dropIndex($index);
                }
            }

            // Drop columns if they exist
            $columns = ['pick_address_id', 'return_address_id'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('cr_cars', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('cr_cars', function (Blueprint $table): void {
            // Re-add columns if rolling back
            if (! Schema::hasColumn('cr_cars', 'pick_address_id')) {
                $table
                    ->unsignedBigInteger('pick_address_id')
                    ->comment('Id table cr_car_addresses')
                    ->nullable()
                    ->index('cr_pick_address_index')
                    ->after('city_id');
            }

            if (! Schema::hasColumn('cr_cars', 'return_address_id')) {
                $table
                    ->unsignedBigInteger('return_address_id')
                    ->comment('Id table cr_car_addresses')
                    ->nullable()
                    ->index('cr_return_address_index')
                    ->after('pick_address_id');
            }
        });
    }
};
