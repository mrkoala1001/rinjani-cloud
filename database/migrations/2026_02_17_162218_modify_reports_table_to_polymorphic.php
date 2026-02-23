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
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->unsignedBigInteger('sender_id')->after('ticket_id');
            $table->string('sender_type')->after('sender_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['sender_id', 'sender_type']);
            $table->foreignId('user_id')->after('ticket_id')->constrained()->onDelete('cascade');
        });
    }
};
