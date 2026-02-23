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
            $after = Schema::hasColumn('billing_history', 'printed_at') ? 'printed_at' : 'created_at'; // Fallback
            
            if (!Schema::hasColumn('billing_history', 'payment_status')) {
                 $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->after($after);
            }
            
            if (!Schema::hasColumn('billing_history', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_history', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'paid_at']);
        });
    }
};
