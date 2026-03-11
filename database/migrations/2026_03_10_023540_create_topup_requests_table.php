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
        Schema::create('topup_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Reseller ID
            $table->unsignedBigInteger('isp_id'); // ISP ID (owner of gateway)
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->string('reference')->unique(); // Gateway Reference
            $table->string('merchant_ref')->unique(); // Internal Reference
            $table->string('payment_method')->nullable();
            $table->enum('status', ['UNPAID', 'PAID', 'EXPIRED', 'FAILED'])->default('UNPAID');
            $table->text('payment_detail')->nullable(); // JSON response from gateway
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('isp_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topup_requests');
    }
};
