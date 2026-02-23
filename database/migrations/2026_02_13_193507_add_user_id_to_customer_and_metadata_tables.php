<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('customer_members') && !Schema::hasColumn('customer_members', 'user_id')) {
            Schema::table('customer_members', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            });
        }

        if (Schema::hasTable('hotspot_profile_metadata') && !Schema::hasColumn('hotspot_profile_metadata', 'user_id')) {
            Schema::table('hotspot_profile_metadata', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            });
        }

        // Patch existing data (Assign to ID 1 / default admin)
        DB::table('customer_members')->update(['user_id' => 1]);
        DB::table('hotspot_profile_metadata')->update(['user_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_members', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('hotspot_profile_metadata', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
