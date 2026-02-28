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
        Schema::table('billing_history', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('reseller_id');
            $table->index('first_login_at');
            $table->index('profile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_history', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['reseller_id']);
            $table->dropIndex(['first_login_at']);
            $table->dropIndex(['profile']);
        });
    }
};
