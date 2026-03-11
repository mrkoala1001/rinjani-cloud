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
        Schema::create('plan_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan_code');
            $table->integer('amount');
            $table->string('payment_method')->nullable();
            $table->string('merchant_ref')->unique();
            $table->string('payment_url')->nullable();
            $table->enum('status', ['PENDING', 'PAID', 'EXPIRED', 'FAILED'])->default('PENDING');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_subscriptions');
    }
};
