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
        Schema::create('balance_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ISP/Owner ID
            $table->unsignedBigInteger('customer_id'); // Reseller ID (from customer_members)
            $table->enum('type', ['IN', 'OUT']); // IN for Topup, OUT for Generate Voucher
            $table->decimal('amount', 15, 2);
            $table->decimal('before_balance', 15, 2);
            $table->decimal('after_balance', 15, 2);
            $table->string('description')->nullable();
            $table->string('reference_id')->nullable(); // Batch ID for OUT, or Transaction ID for IN
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_histories');
    }
};
