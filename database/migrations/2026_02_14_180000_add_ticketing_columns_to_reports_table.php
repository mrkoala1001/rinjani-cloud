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
            $table->string('ticket_id')->unique()->nullable()->after('id');
            $table->text('admin_reply')->nullable()->after('message');
            $table->timestamp('reply_at')->nullable()->after('admin_reply');
            
            // Modify status column to include more states
            // Note: Since we are using enum, and DB support for modifying enum varies, 
            // we will drop constraints if necessary or just change the column definition.
            // For safety and compatibility, we will change it to string or redefine enum.
            // Let's redefine enum (MySQL allows this).
            $table->enum('status', ['unread', 'read', 'pending', 'processed', 'resolved', 'closed', 'archived'])
                  ->default('pending')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['ticket_id', 'admin_reply', 'reply_at']);
            // Revert status enum (tricky, but best effort)
            $table->enum('status', ['unread', 'read', 'archived'])->default('unread')->change();
        });
    }
};
