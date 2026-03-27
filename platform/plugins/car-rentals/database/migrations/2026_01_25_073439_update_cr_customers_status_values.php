<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('cr_customers')
            ->where('status', 'published')
            ->update(['status' => 'activated']);

        DB::table('cr_customers')
            ->whereIn('status', ['draft', 'pending'])
            ->update(['status' => 'locked']);
    }

    public function down(): void
    {
        DB::table('cr_customers')
            ->where('status', 'activated')
            ->update(['status' => 'published']);

        DB::table('cr_customers')
            ->where('status', 'locked')
            ->update(['status' => 'draft']);
    }
};
