<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Correct way to update ENUM in MySQL/PostgreSQL
        DB::statement("ALTER TABLE users MODIFY COLUMN plan ENUM('basic', 'medium', 'pro', 'isp') DEFAULT 'basic'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN plan ENUM('basic', 'medium', 'pro', 'ultra') DEFAULT 'basic'");
    }
};
