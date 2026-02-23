<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MikrotikConfig;
use App\Models\BillingHistory;
use App\Models\Income;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ISPDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Dummy Owners
        $owners = [
            [
                'name' => 'CyberNet Official',
                'username' => 'cybernet',
                'email' => 'owner@cybernet.net',
                'location' => 'Jakarta Selatan',
                'whatsapp' => '081234567890',
            ],
            [
                'name' => 'Java Network',
                'username' => 'javanet',
                'email' => 'admin@javanet.id',
                'location' => 'Bandung, Jawa Barat',
                'whatsapp' => '081987654321',
            ],
            [
                'name' => 'Borneo Connect',
                'username' => 'borneo',
                'email' => 'suport@borneo.net',
                'location' => 'Balikpapan',
                'whatsapp' => '081299887766',
            ],
            [
                'name' => 'Sumatra Link',
                'username' => 'sumatralink',
                'email' => 'info@sumatra.id',
                'location' => 'Medan',
                'whatsapp' => '081311223344',
            ],
        ];

        foreach ($owners as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'password' => Hash::make('password'),
                    'role' => 'owner',
                    'location' => $data['location'],
                    'whatsapp' => $data['whatsapp'],
                    'is_active' => true,
                ]
            );

            // 2. Create Dummy Router for each owner
            if ($user->mikrotikConfigs()->count() == 0) {
                MikrotikConfig::create([
                    'user_id' => $user->id,
                    'host' => '192.168.1.' . rand(10, 255),
                    'user' => 'admin',
                    'pass' => 'password',
                    'port' => 8728,
                ]);
            }

            // 3. Create Dummy Transactions (Billing History - Voucher Sales)
            if ($user->billingHistories()->count() == 0) {
                for ($i = 0; $i < rand(5, 15); $i++) {
                    BillingHistory::create([
                        'user_id' => $user->id,
                        'username' => 'user' . rand(1000, 9999) . '_' . Str::random(5),
                        'password' => '123',
                        'profile' => ['1 Jam', '3 Jam', '1 Hari'][rand(0, 2)],
                        'price' => [2000, 5000, 10000][rand(0, 2)],
                        'created_at' => Carbon::now()->subDays(rand(0, 30)),
                    ]);
                }
            }

            // 4. Create Dummy Incomes (Member / PPPoE)
            if ($user->incomes()->count() == 0) {
                // Member Income
                Income::create([
                    'user_id' => $user->id,
                    'category' => 'Member',
                    'amount' => rand(500000, 1500000),
                    'description' => 'Bulanan Member',
                    'date' => Carbon::now(),
                ]);

                // PPPoE Income
                Income::create([
                    'user_id' => $user->id,
                    'category' => 'PPPoE',
                    'amount' => rand(1000000, 3000000),
                    'description' => 'Tagihan PPPoE',
                    'date' => Carbon::now(),
                ]);
            }
        }
    }
}
