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
            $table->unsignedBigInteger('template_id')->nullable()->after('reseller_id');
            $table->string('batch_id', 50)->nullable()->after('template_id');
            $table->timestamp('generated_at')->nullable()->after('batch_id');
            $table->timestamp('printed_at')->nullable()->after('generated_at');
            
            $table->index('batch_id');
            $table->index('generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_history', function (Blueprint $table) {
            $table->dropIndex(['batch_id']);
            $table->dropIndex(['generated_at']);
            $table->dropColumn(['template_id', 'batch_id', 'generated_at', 'printed_at']);
        });
    }
};
