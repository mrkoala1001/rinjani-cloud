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
                if (!Schema::hasColumn('billing_history', 'reseller_id')) {
                    $table->unsignedBigInteger('reseller_id')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('billing_history', 'template_id')) {
                    $table->unsignedBigInteger('template_id')->nullable()->after('reseller_id');
                }
                if (!Schema::hasColumn('billing_history', 'batch_id')) {
                    $table->string('batch_id', 50)->nullable()->after('template_id');
                    $table->index('batch_id');
                }
                if (!Schema::hasColumn('billing_history', 'generated_at')) {
                    $table->timestamp('generated_at')->nullable()->after('batch_id');
                    $table->index('generated_at');
                }
                if (!Schema::hasColumn('billing_history', 'printed_at')) {
                    $table->timestamp('printed_at')->nullable()->after('generated_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration for repair
    }
};
