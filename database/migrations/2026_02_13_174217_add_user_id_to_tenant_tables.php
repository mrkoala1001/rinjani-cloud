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
        $tables = [
            'billing_history',
            'expenses',
            'incomes',
            'voucher_templates',
            'debts',
            'resellers',
            'hotspot_profile_metadata',
            'customer_members' // Assuming this is the table name for CustomerMember model
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'user_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('user_id')->nullable()->index()->constrained('users')->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'billing_history',
            'expenses',
            'incomes',
            'voucher_templates',
            'debts',
            'resellers',
            'hotspot_profile_metadata',
            'customer_members'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'user_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                     $table->dropForeign(['user_id']);
                     $table->dropColumn('user_id');
                });
            }
        }
    }
};
