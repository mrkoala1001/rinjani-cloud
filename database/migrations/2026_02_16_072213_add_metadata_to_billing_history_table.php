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
            $table->string('hotspotname')->nullable()->after('profile');
            $table->string('timelimit')->nullable()->after('hotspotname');
            $table->string('datalimit')->nullable()->after('timelimit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_history', function (Blueprint $table) {
            $table->dropColumn(['hotspotname', 'timelimit', 'datalimit']);
        });
    }
};
