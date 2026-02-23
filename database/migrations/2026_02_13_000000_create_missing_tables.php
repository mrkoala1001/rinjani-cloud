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
        if (!Schema::hasTable('customer_members')) {
            Schema::create('customer_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('type')->nullable(); // MEMBER, PERUMAHAN, RESELLER
                $table->string('name')->nullable();
                $table->string('location')->nullable();
                $table->string('coordinates')->nullable();
                $table->decimal('bill_amount', 15, 2)->default(0);
                $table->decimal('paid_amount', 15, 2)->default(0);
                $table->date('payment_date')->nullable();
                $table->text('notes')->nullable();
                $table->string('password')->nullable();
                $table->string('device_name')->nullable();
                $table->string('device_ip')->nullable();
                $table->string('wan_ip')->nullable();
                $table->string('device_username')->nullable();
                $table->string('device_password')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mikrotik_config')) {
            Schema::create('mikrotik_config', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('host')->nullable();
                $table->string('user')->nullable();
                $table->string('pass')->nullable();
                $table->integer('port')->default(8728);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hotspot_profile_metadata')) {
            Schema::create('hotspot_profile_metadata', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('profile_name')->nullable();
                $table->decimal('price', 15, 2)->default(0);
                $table->decimal('selling_price', 15, 2)->default(0);
                $table->string('validity')->nullable();
                $table->string('timelimit')->nullable();
                $table->string('user_mode')->nullable();
                $table->boolean('lock_user')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('voucher_templates')) {
            Schema::create('voucher_templates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->boolean('is_system')->default(false);
                $table->string('name')->nullable();
                $table->longText('html_content')->nullable();
                $table->longText('css_content')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('resellers')) {
            Schema::create('resellers', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->decimal('balance', 15, 2)->default(0);
                $table->string('phone')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resellers');
        Schema::dropIfExists('voucher_templates');
        Schema::dropIfExists('hotspot_profile_metadata');
        Schema::dropIfExists('mikrotik_config');
        Schema::dropIfExists('customer_members');
    }
};
