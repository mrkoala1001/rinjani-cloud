<?php

namespace App\Services;

use App\Events\HotspotDataUpdated;
use App\Events\HotspotUserConnected;
use App\Events\HotspotUserDisconnected;
use App\Events\PppoeSessionConnected;
use App\Events\PppoeSessionDisconnected;
use App\Events\DashboardStatsUpdated; // Assuming you have this
use App\Models\MikrotikConfig;
use RouterOS\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

use App\Models\User;

class MikrotikSyncService
{
    public function __construct(public User $user) {}

    private function getClient()
    {
        $mkConfig = $this->user->mikrotikConfigs->first();
        if (!$mkConfig) return null;

        try {
            return new Client([
                'host' => $mkConfig->host,
                'user' => $mkConfig->user,
                'pass' => $mkConfig->pass,
                'port' => (int)($mkConfig->port ?? 8728),
                'timeout' => 5,
            ]);
        } catch (\Exception $e) {
            Log::error("Mikrotik Connection Failed (User {$this->user->id}): " . $e->getMessage());
            return null;
        }
    }

    public function syncHotspot()
    {
        $client = $this->getClient();
        if (!$client) return;

        try {
            $activeUsers = $client->query('/ip/hotspot/active/print')->read();
            
            // Format data
            $formattedUsers = collect($activeUsers)->map(function ($user) {
                return [
                    '.id' => $user['.id'] ?? null,
                    'name' => $user['user'] ?? 'Unknown',
                    'address' => $user['address'] ?? '',
                    'mac-address' => $user['mac-address'] ?? '',
                    'uptime' => $user['uptime'] ?? '0s',
                    'bytes-in' => $user['bytes-in'] ?? 0,
                    'bytes-out' => $user['bytes-out'] ?? 0,
                    'server' => $user['server'] ?? '',
                ];
            })->values()->toArray();

            // Broadcast full update
            broadcast(new HotspotDataUpdated($formattedUsers, $this->user->id));

            // Compare with previous state
            $cacheKey = "hotspot_active_users_{$this->user->id}";
            $previousUsers = Cache::get($cacheKey, []);
            $this->detectHotspotChanges($previousUsers, $formattedUsers);

            // Update cache
            Cache::put($cacheKey, $formattedUsers, 60);

        } catch (\Exception $e) {
            Log::error("Sync Hotspot Failed (User {$this->user->id}): " . $e->getMessage());
        }
    }

    private function detectHotspotChanges($previous, $current)
    {
        $prevMap = collect($previous)->keyBy('name');
        $currMap = collect($current)->keyBy('name');

        foreach ($current as $user) {
            if (!$prevMap->has($user['name'])) {
                broadcast(new HotspotUserConnected($user, $this->user->id));
            }
        }

        foreach ($previous as $user) {
            if (!$currMap->has($user['name'])) {
                broadcast(new HotspotUserDisconnected($user, $this->user->id));
            }
        }
    }

    public function syncPppoe()
    {
        $client = $this->getClient();
        if (!$client) return;

        try {
            $activeSessions = $client->query('/ppp/active/print')->read();

            $formattedSessions = collect($activeSessions)->map(function ($session) {
                return [
                    '.id' => $session['.id'] ?? null,
                    'name' => $session['name'] ?? 'Unknown',
                    'service' => $session['service'] ?? '',
                    'caller-id' => $session['caller-id'] ?? '',
                    'address' => $session['address'] ?? '',
                    'uptime' => $session['uptime'] ?? '0s',
                    'comment' => $session['comment'] ?? '',
                ];
            })->values()->toArray();

            // Broadcast
            // Use connect/disconnect logic
            
            $cacheKey = "pppoe_active_sessions_{$this->user->id}";
            $previousSessions = Cache::get($cacheKey, []);
            $this->detectPppoeChanges($previousSessions, $formattedSessions);
            
            Cache::put($cacheKey, $formattedSessions, 60);

        } catch (\Exception $e) {
            Log::error("Sync PPPoE Failed (User {$this->user->id}): " . $e->getMessage());
        }
    }

    private function detectPppoeChanges($previous, $current)
    {
        $prevMap = collect($previous)->keyBy('name');
        $currMap = collect($current)->keyBy('name');

        foreach ($current as $session) {
            if (!$prevMap->has($session['name'])) {
                broadcast(new PppoeSessionConnected($session, $this->user->id));
            }
        }

        foreach ($previous as $session) {
            if (!$currMap->has($session['name'])) {
                broadcast(new PppoeSessionDisconnected($session, $this->user->id));
            }
        }
    }
    
    public function syncDashboard()
    {
        $client = $this->getClient();
        if (!$client) return;

        try {
            // Check if TenantScope is applied? 
            // Since we are running in Job, auth() is likely null. 
            // We need to query DB manually with user_id or use withoutGlobalScopes() if we want ALL (which is not what we want here).
            // We strictly want THIS user's data.
            // But Model::sum() uses global scope. 
            // If auth() is null, TenantScope does nothing?
            // Yes, "if (auth()->check())".
            // So queries here will see ALL data if we use Model::...
            // BUT we are using DB::table(...). TenantScope DOES NOT apply to DB::table.
            // So we MUST manually add where('user_id', $this->user->id).
            
            $userId = $this->user->id;

            // Income Stats
            $voucherRealtime = \Illuminate\Support\Facades\DB::table('billing_history as bh')
                ->where('bh.user_id', $userId)
                ->whereNull('bh.first_login_at')
                ->leftJoin('hotspot_profile_metadata as pm', 'bh.profile', '=', 'pm.profile_name')
                ->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(pm.price, bh.price)'));

            // Anchor Date for Stats
            $anchorDate = null;
            if (!empty($timeData)) {
                try {
                    $anchorDate = \Carbon\Carbon::createFromFormat('M/d/Y H:i:s', $timeData['date'] . ' ' . $timeData['time']);
                } catch (\Exception $e) {}
            }
            $anchorDate = $anchorDate ?: now();

            $totalVoucherSold = \Illuminate\Support\Facades\DB::table('billing_history as bh')
                ->where('bh.user_id', $userId)
                ->whereNotNull('bh.first_login_at')
                ->whereMonth('bh.first_login_at', $anchorDate->month)
                ->whereYear('bh.first_login_at', $anchorDate->year)
                ->leftJoin('hotspot_profile_metadata as pm', 'bh.profile', '=', 'pm.profile_name')
                ->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(pm.price, bh.price)'));

            $totalIncome = $totalVoucherSold + \Illuminate\Support\Facades\DB::table('incomes')
                ->where('user_id', $userId)
                ->sum('amount');

            $monthlyIncome = \Illuminate\Support\Facades\DB::table('billing_history as bh')
                 ->where('bh.user_id', $userId)
                 ->whereNotNull('bh.first_login_at')
                ->leftJoin('hotspot_profile_metadata as pm', 'bh.profile', '=', 'pm.profile_name')
                ->whereMonth('bh.first_login_at', now()->month)
                ->whereYear('bh.first_login_at', now()->year)
                ->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(pm.price, bh.price)'));

            // Mikrotik Stats
            $activeUsers = $client->query('/ip/hotspot/active/print')->read();
            $hotspotCount = count($activeUsers);
            
            $allUsers = $client->query('/ip/hotspot/user/print')->read();
            $totalVoucherCount = count($allUsers);
            
            $pppoeSecrets = $client->query('/ppp/secret/print')->read();
            $pppoeCount = count($pppoeSecrets);
            
            
            $resource = $client->query('/system/resource/print')->read();
            $cpu = 0;
            $uptime = '0s';
            $boardName = '-';
            $freeMemory = 0;
            $version = '-';
            
            if (!empty($resource)) {
                $cpu = $resource[0]['cpu-load'] ?? 0;
                $uptime = $resource[0]['uptime'] ?? '0s';
                $boardName = $resource[0]['board-name'] ?? '-';
                $freeMemory = $resource[0]['free-memory'] ?? 0;
                $version = $resource[0]['version'] ?? '-';
            }
            
            $clock = $client->query('/system/clock/print')->read();
            $timeData = !empty($clock) ? $clock[0] : [];

            $stats = [
                'totalIncome' => $totalIncome,
                'monthlyIncome' => $monthlyIncome,
                'voucherRealtime' => $voucherRealtime,
                'totalVoucherSold' => $totalVoucherSold,
                'hotspot_count' => $hotspotCount,
                'totalVoucher' => $totalVoucherCount,
                'pppoe_count' => $pppoeCount,
                'cpu_load' => $cpu,
                'uptime' => $uptime,
                'board_name' => $boardName,
                'free_memory' => $freeMemory,
                'version' => $version,
                'time' => $timeData
            ];

            broadcast(new DashboardStatsUpdated($stats, $userId));

        } catch (\Exception $e) {
             Log::error("Sync Dashboard Failed (User {$this->user->id}): " . $e->getMessage());
        }
    }
}
