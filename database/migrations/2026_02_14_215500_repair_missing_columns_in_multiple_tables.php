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
        // hotspot_profile_metadata
        if (Schema::hasTable('hotspot_profile_metadata')) {
            Schema::table('hotspot_profile_metadata', function (Blueprint $table) {
                if (!Schema::hasColumn('hotspot_profile_metadata', 'profile_name')) {
                    $table->string('profile_name')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('hotspot_profile_metadata', 'price')) {
                    $table->decimal('price', 15, 2)->default(0)->after('profile_name');
                }
                if (!Schema::hasColumn('hotspot_profile_metadata', 'selling_price')) {
                    $table->decimal('selling_price', 15, 2)->default(0)->after('price');
                }
                if (!Schema::hasColumn('hotspot_profile_metadata', 'validity')) {
                    $table->string('validity')->nullable()->after('selling_price');
                }
                if (!Schema::hasColumn('hotspot_profile_metadata', 'timelimit')) {
                    $table->string('timelimit')->nullable()->after('validity');
                }
                if (!Schema::hasColumn('hotspot_profile_metadata', 'user_mode')) {
                    $table->string('user_mode')->nullable()->after('timelimit');
                }
                if (!Schema::hasColumn('hotspot_profile_metadata', 'lock_user')) {
                    $table->boolean('lock_user')->default(false)->after('user_mode');
                }
            });
        }

        // customer_members
        if (Schema::hasTable('customer_members')) {
            Schema::table('customer_members', function (Blueprint $table) {
                if (!Schema::hasColumn('customer_members', 'type')) {
                    $table->string('type')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('customer_members', 'name')) {
                    $table->string('name')->nullable()->after('type');
                }
                if (!Schema::hasColumn('customer_members', 'location')) {
                    $table->string('location')->nullable()->after('name');
                }
                if (!Schema::hasColumn('customer_members', 'coordinates')) {
                    $table->string('coordinates')->nullable()->after('location');
                }
                if (!Schema::hasColumn('customer_members', 'bill_amount')) {
                    $table->decimal('bill_amount', 15, 2)->default(0)->after('coordinates');
                }
                if (!Schema::hasColumn('customer_members', 'paid_amount')) {
                    $table->decimal('paid_amount', 15, 2)->default(0)->after('bill_amount');
                }
                if (!Schema::hasColumn('customer_members', 'payment_date')) {
                    $table->date('payment_date')->nullable()->after('paid_amount');
                }
                if (!Schema::hasColumn('customer_members', 'notes')) {
                    $table->text('notes')->nullable()->after('payment_date');
                }
                if (!Schema::hasColumn('customer_members', 'password')) {
                    $table->string('password')->nullable()->after('notes');
                }
                if (!Schema::hasColumn('customer_members', 'device_name')) {
                    $table->string('device_name')->nullable()->after('password');
                }
                if (!Schema::hasColumn('customer_members', 'device_ip')) {
                    $table->string('device_ip')->nullable()->after('device_name');
                }
                if (!Schema::hasColumn('customer_members', 'wan_ip')) {
                    $table->string('wan_ip')->nullable()->after('device_ip');
                }
                if (!Schema::hasColumn('customer_members', 'device_username')) {
                    $table->string('device_username')->nullable()->after('wan_ip');
                }
                if (!Schema::hasColumn('customer_members', 'device_password')) {
                    $table->string('device_password')->nullable()->after('device_username');
                }
            });
        }

        // mikrotik_config
        if (Schema::hasTable('mikrotik_config')) {
            Schema::table('mikrotik_config', function (Blueprint $table) {
                if (!Schema::hasColumn('mikrotik_config', 'host')) {
                    $table->string('host')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('mikrotik_config', 'user')) {
                    $table->string('user')->nullable()->after('host');
                }
                if (!Schema::hasColumn('mikrotik_config', 'pass')) {
                    $table->string('pass')->nullable()->after('user');
                }
                if (!Schema::hasColumn('mikrotik_config', 'port')) {
                    $table->integer('port')->default(8728)->after('pass');
                }
            });
        }

        // voucher_templates
        if (Schema::hasTable('voucher_templates')) {
            Schema::table('voucher_templates', function (Blueprint $table) {
                if (!Schema::hasColumn('voucher_templates', 'name')) {
                    $table->string('name')->nullable()->after('is_system');
                }
                if (!Schema::hasColumn('voucher_templates', 'html_content')) {
                    $table->longText('html_content')->nullable()->after('name');
                }
                if (!Schema::hasColumn('voucher_templates', 'css_content')) {
                    $table->longText('css_content')->nullable()->after('html_content');
                }
            });
        }

        // resellers
        if (Schema::hasTable('resellers')) {
            Schema::table('resellers', function (Blueprint $table) {
                if (!Schema::hasColumn('resellers', 'name')) {
                    $table->string('name')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('resellers', 'balance')) {
                    $table->decimal('balance', 15, 2)->default(0)->after('name');
                }
                if (!Schema::hasColumn('resellers', 'phone')) {
                    $table->string('phone')->nullable()->after('balance');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't necessarily want to drop these columns in down as they represent the repair state.
    }
};
