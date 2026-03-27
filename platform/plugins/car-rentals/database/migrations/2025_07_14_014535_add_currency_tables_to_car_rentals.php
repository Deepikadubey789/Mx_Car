<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('cr_currencies')) {
            Schema::create('cr_currencies', function (Blueprint $table): void {
                $table->id();
                $table->string('title');
                $table->string('symbol', 10);
                $table->tinyInteger('is_prefix_symbol')->unsigned()->default(0);
                $table->tinyInteger('decimals')->unsigned()->default(0)->nullable();
                $table->integer('order')->default(0)->unsigned()->nullable();
                $table->tinyInteger('is_default')->default(0);
                $table->double('exchange_rate')->default(1);
                $table->timestamps();
            });
        }

        // Update currency field in bookings if it doesn't exist
        if (! Schema::hasColumn('cr_bookings', 'currency')) {
            Schema::table('cr_bookings', function (Blueprint $table): void {
                $table->string('currency', 10)->nullable()->after('tax_amount');
            });
        }

        // Add currency to cars
        if (! Schema::hasColumn('cr_cars', 'currency')) {
            Schema::table('cr_cars', function (Blueprint $table): void {
                $table->string('currency', 10)->nullable()->after('rental_rate');
            });
        }
    }

    public function down(): void
    {
        Schema::table('cr_cars', function (Blueprint $table): void {
            $table->dropColumn('currency');
        });

        Schema::table('cr_bookings', function (Blueprint $table): void {
            $table->dropColumn('currency');
        });

        Schema::dropIfExists('cr_currencies');
    }
};
