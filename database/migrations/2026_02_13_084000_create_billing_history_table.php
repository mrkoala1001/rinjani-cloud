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
        if (!Schema::hasTable('billing_history')) {
            Schema::create('billing_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('voucher_code')->nullable();
                $table->string('profile')->nullable();
                $table->decimal('price', 15, 2)->default(0);
                $table->dateTime('date_sold')->nullable();
                $table->string('server')->nullable();
                $table->string('comment')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_history');
    }
};
