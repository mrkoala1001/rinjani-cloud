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
            $after = Schema::hasColumn('billing_history', 'generated_at') ? 'generated_at' : 'created_at';
            
            if (!Schema::hasColumn('billing_history', 'first_login_at')) {
                $table->timestamp('first_login_at')->nullable()->after($after);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_history', function (Blueprint $table) {
            $table->dropColumn('first_login_at');
        });
    }
};
