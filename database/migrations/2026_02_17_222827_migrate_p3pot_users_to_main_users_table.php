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
        $p3potUsers = \DB::table('user_p3pot')->get();

        foreach ($p3potUsers as $user) {
            // Check if user already exists in users table by username OR email
            $exists = \DB::table('users')
                ->where('username', $user->username)
                ->orWhere('email', $user->username . '@p3pot.local')
                ->exists();

            if (!$exists) {
                \DB::table('users')->insert([
                    'name' => $user->fullname,
                    'username' => $user->username,
                    'email' => $user->username . '@p3pot.local',
                    'password' => $user->password,
                    'role' => $user->role, // will be 'admin' or 'customer' for p3pot
                    'origin' => 'p3pot',
                    'is_active' => 1,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('users')->where('origin', 'p3pot')->delete();
    }
};
