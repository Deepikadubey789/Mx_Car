<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. DROP THE OLD FLAT-FEE INSURANCE STRUCTURE
        Schema::dropIfExists('cr_booking_insurances');
        Schema::dropIfExists('cr_insurances_translations');
        Schema::dropIfExists('cr_insurances');

        // 2. CREATE HOST PROTECTION PLANS (Only if it doesn't exist)
        if (!Schema::hasTable('cr_host_protection_plans')) {
            Schema::create('cr_host_protection_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name', 120); 
                $table->text('description')->nullable();
                $table->decimal('revenue_share_percentage', 5, 2); 
                $table->decimal('deductible_amount', 15, 2)->default(0); 
                $table->string('status', 60)->default('published');
                $table->timestamps();
            });
        }

        // 3. CREATE GUEST PROTECTION PLANS (Only if it doesn't exist)
        if (!Schema::hasTable('cr_guest_protection_plans')) {
            Schema::create('cr_guest_protection_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name', 120); 
                $table->text('description')->nullable();
                $table->decimal('daily_fee', 15, 2)->default(0); 
                $table->decimal('deductible_amount', 15, 2)->default(0); 
                $table->decimal('liability_limit', 15, 2)->nullable(); 
                $table->string('status', 60)->default('published');
                $table->timestamps();
            });
        }

        // 4. ATTACH HOST PLAN TO CARS (Safely guarded)
        Schema::table('cr_cars', function (Blueprint $table) {
            if (!Schema::hasColumn('cr_cars', 'host_protection_plan_id')) {
                $table->unsignedBigInteger('host_protection_plan_id')->nullable()->after('status');
                $table->foreign('host_protection_plan_id')
                      ->references('id')
                      ->on('cr_host_protection_plans')
                      ->onDelete('set null');
            }
        });

        // 5. SNAPSHOT PLANS ON THE BOOKING (Safely guarded)
        Schema::table('cr_bookings', function (Blueprint $table) {
            // Guest Side
            if (!Schema::hasColumn('cr_bookings', 'guest_protection_plan_id')) {
                $table->unsignedBigInteger('guest_protection_plan_id')->nullable()->after('amount');
                $table->foreign('guest_protection_plan_id')->references('id')->on('cr_guest_protection_plans')->onDelete('set null');
            }
            if (!Schema::hasColumn('cr_bookings', 'guest_protection_fee')) {
                $table->decimal('guest_protection_fee', 15, 2)->default(0)->after('guest_protection_plan_id');
            }
            if (!Schema::hasColumn('cr_bookings', 'guest_deductible_amount')) {
                $table->decimal('guest_deductible_amount', 15, 2)->default(0)->after('guest_protection_fee');
            }
            
            // Host Side
            if (!Schema::hasColumn('cr_bookings', 'host_protection_plan_id')) {
                $table->unsignedBigInteger('host_protection_plan_id')->nullable()->after('guest_deductible_amount');
                $table->foreign('host_protection_plan_id')->references('id')->on('cr_host_protection_plans')->onDelete('set null');
            }
            if (!Schema::hasColumn('cr_bookings', 'host_revenue_share_percentage')) {
                $table->decimal('host_revenue_share_percentage', 5, 2)->default(100)->after('host_protection_plan_id');
            }
            if (!Schema::hasColumn('cr_bookings', 'host_deductible_amount')) {
                $table->decimal('host_deductible_amount', 15, 2)->default(0)->after('host_revenue_share_percentage');
            }
            
            // Compliance Gatekeeper
            if (!Schema::hasColumn('cr_bookings', 'checkin_status')) {
                $table->string('checkin_status', 60)->default('pending')->after('status'); 
            }
        });
    }

    public function down(): void
    {
        Schema::table('cr_bookings', function (Blueprint $table) {
            $table->dropForeign(['guest_protection_plan_id']);
            $table->dropForeign(['host_protection_plan_id']);
            $table->dropColumn([
                'guest_protection_plan_id', 'guest_protection_fee', 'guest_deductible_amount',
                'host_protection_plan_id', 'host_revenue_share_percentage', 'host_deductible_amount',
                'checkin_status'
            ]);
        });

        Schema::table('cr_cars', function (Blueprint $table) {
            $table->dropForeign(['host_protection_plan_id']);
            $table->dropColumn('host_protection_plan_id');
        });

        Schema::dropIfExists('cr_guest_protection_plans');
        Schema::dropIfExists('cr_host_protection_plans');
    }
};