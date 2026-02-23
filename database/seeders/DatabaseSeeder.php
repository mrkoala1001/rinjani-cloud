<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create Superduper Admin (ISP)
        User::create([
            'name' => 'Superduper Admin',
            'email' => 'admin@isp.com',
            'password' => bcrypt('12345678'),
            'role' => 'isp',
            'is_active' => true,
        ]);

        // Create Demo Owner (Mitra)
        User::create([
            'name' => 'Owner Demo',
            'email' => 'owner@demo.com',
            'password' => bcrypt('12345678'),
            'role' => 'owner',
            'is_active' => true,
        ]);
    }
}
