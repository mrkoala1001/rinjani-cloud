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
        if (Schema::hasTable('users_blog')) {
            $blogUsers = \DB::table('users_blog')->get();

            foreach ($blogUsers as $user) {
                // Check if user already exists in users table by email
                $exists = \DB::table('users')->where('email', $user->email)->exists();

                if (!$exists) {
                    \DB::table('users')->insert([
                        'name' => $user->name,
                        'email' => $user->email,
                        'username' => $user->username ?: explode('@', $user->email)[0],
                        'password' => $user->password,
                        'role' => $user->role,
                        'origin' => 'depootcom',
                        'is_active' => $user->is_active ?? 1,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                        'location' => $user->location ?? null,
                        'notes' => $user->notes ?? null,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('users')->where('origin', 'depootcom')->delete();
    }
};
