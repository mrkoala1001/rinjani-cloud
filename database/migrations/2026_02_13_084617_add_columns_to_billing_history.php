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
            $table->unsignedBigInteger('customer_id')->nullable()->after('server');
            $table->string('customer_name')->nullable()->after('customer_id');
            $table->string('category')->default('Voucher')->comment('Voucher, Member, Reseller')->after('customer_name');
            $table->string('payment_method')->nullable()->after('category');
            $table->decimal('bill_amount', 15, 2)->default(0)->after('payment_method');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('bill_amount');
            $table->string('proof_image')->nullable()->after('paid_amount');
            $table->text('notes')->nullable()->after('proof_image');
        });
    }

    public function down(): void
    {
        Schema::table('billing_history', function (Blueprint $table) {
            $table->dropColumn(['customer_id', 'customer_name', 'category', 'payment_method', 'bill_amount', 'paid_amount', 'proof_image', 'notes']);
        });
    }
};
