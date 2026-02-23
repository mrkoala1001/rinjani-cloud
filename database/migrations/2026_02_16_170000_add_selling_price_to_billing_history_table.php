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
        if (Schema::hasTable('billing_history')) {
            Schema::table('billing_history', function (Blueprint $table) {
                if (!Schema::hasColumn('billing_history', 'selling_price')) {
                    $table->decimal('selling_price', 15, 2)->default(0)->after('price');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('billing_history')) {
            Schema::table('billing_history', function (Blueprint $table) {
                if (Schema::hasColumn('billing_history', 'selling_price')) {
                    $table->dropColumn('selling_price');
                }
            });
        }
    }
};
