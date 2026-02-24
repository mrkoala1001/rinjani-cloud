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
        Schema::create('topups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Owner
            $table->unsignedBigInteger('customer_id'); // Reseller
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('PENDING'); // PENDING, SUCCESS, FAILED, EXPIRED
            $table->text('payment_url')->nullable();
            $table->string('reference_id')->nullable(); // DOKU reference
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topups');
    }
};
