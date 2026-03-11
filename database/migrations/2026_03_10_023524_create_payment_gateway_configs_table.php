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
        Schema::create('payment_gateway_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ISP / Owner ID
            $table->string('provider')->default('tripay');
            $table->string('merchant_code')->nullable();
            $table->string('api_key')->nullable();
            $table->string('private_key')->nullable();
            $table->enum('mode', ['sandbox', 'production'])->default('sandbox');
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_configs');
    }
};
