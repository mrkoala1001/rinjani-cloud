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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('category', 50);
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50)->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('proof_image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('date');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
