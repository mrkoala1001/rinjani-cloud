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
        Schema::create('pppoe_profile_metadata', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('profile_name');
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->string('validity')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'profile_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pppoe_profile_metadata');
    }
};
