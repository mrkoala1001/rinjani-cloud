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
        // 1. Create Views for Authentication and Authorization
        // radcheck: Username and Password
        DB::statement("CREATE OR REPLACE VIEW radcheck AS 
            SELECT id, username, 'Cleartext-Password' AS attribute, ':=' AS op, password AS value 
            FROM billing_history");

        // radreply: Profile mapping to Mikrotik-Group
        DB::statement("CREATE OR REPLACE VIEW radreply AS 
            SELECT id, username, 'Mikrotik-Group' AS attribute, ':=' AS op, profile AS value 
            FROM billing_history");

        // 2. Create actual tables for Accounting and Post-Auth (RADIUS needs to write here)
        if (!Schema::hasTable('radacct')) {
            Schema::create('radacct', function (Blueprint $table) {
                $table->bigIncrements('radacctid');
                $table->string('acctsessionid', 64)->default('')->index();
                $table->string('acctuniqueid', 32)->default('')->index();
                $table->string('username', 64)->default('')->index();
                $table->string('groupname', 64)->default('');
                $table->string('realm', 64)->default('');
                $table->string('nasipaddress', 15)->default('')->index();
                $table->string('nasportid', 15)->nullable();
                $table->string('nasporttype', 32)->nullable();
                $table->dateTime('acctstarttime')->nullable()->index();
                $table->dateTime('acctupdatetime')->nullable();
                $table->dateTime('acctstoptime')->nullable()->index();
                $table->integer('acctinterval')->nullable();
                $table->bigInteger('acctsessiontime')->nullable();
                $table->string('acctauthentic', 32)->nullable();
                $table->string('connectinfo_start', 50)->nullable();
                $table->string('connectinfo_stop', 50)->nullable();
                $table->bigInteger('acctinputoctets')->nullable();
                $table->bigInteger('acctoutputoctets')->nullable();
                $table->string('calledstationid', 50)->default('');
                $table->string('callingstationid', 50)->default('');
                $table->string('acctterminatecause', 32)->default('');
                $table->string('servicetype', 32)->nullable();
                $table->string('framedprotocol', 32)->nullable();
                $table->string('framedipaddress', 15)->default('');
            });
        }

        if (!Schema::hasTable('radpostauth')) {
            Schema::create('radpostauth', function (Blueprint $table) {
                $table->id();
                $table->string('username', 64)->default('')->index();
                $table->string('pass', 64)->default('');
                $table->string('reply', 32)->default('');
                $table->timestamp('authdate')->useCurrent();
            });
        }

        if (!Schema::hasTable('nas')) {
            Schema::create('nas', function (Blueprint $table) {
                $table->id();
                $table->string('nasname', 128)->index();
                $table->string('shortname', 32)->nullable();
                $table->string('type', 30)->default('other');
                $table->integer('ports')->nullable();
                $table->string('secret', 60)->default('secret');
                $table->string('server', 64)->nullable();
                $table->string('community', 50)->nullable();
                $table->string('description', 200)->default('RADIUS Client');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nas');
        Schema::dropIfExists('radpostauth');
        Schema::dropIfExists('radacct');
        DB::statement("DROP VIEW IF EXISTS radreply");
        DB::statement("DROP VIEW IF EXISTS radcheck");
    }
};
