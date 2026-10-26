<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->after('id')
                ->constrained('businesses')->nullOnDelete();
        });

        // Give every existing user who already has customers a business, and link the customers.
        $hasShopname = Schema::hasColumn('users', 'shopname');
        $hasPhone = Schema::hasColumn('users', 'phone');

        $users = DB::table('users')
            ->whereIn('id', DB::table('customers')->select('user_id')->distinct())
            ->get();

        foreach ($users as $u) {
            $businessId = DB::table('businesses')->insertGetId([
                'owner_id' => $u->id,
                'name' => ($hasShopname && $u->shopname) ? $u->shopname : $u->name . "'s Shop",
                'business_type' => 'other',
                'phone' => $hasPhone ? $u->phone : null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('customers')->where('user_id', $u->id)->update(['business_id' => $businessId]);
        }
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('business_id');
        });
    }
};