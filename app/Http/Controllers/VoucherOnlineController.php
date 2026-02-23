<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MikrotikConfig;
use RouterOS\Client;
use RouterOS\Query;
use Exception;

class VoucherOnlineController extends Controller
{
    private function getClient()
    {
        $mkConfig = MikrotikConfig::where('user_id', auth()->id())->first();
        if (!$mkConfig) return null;

        try {
            return new Client([
                'host' => $mkConfig->host,
                'user' => $mkConfig->user,
                'pass' => $mkConfig->pass,
                'port' => (int)($mkConfig->port ?? 8728),
                'timeout' => 2,
            ]);
        } catch (Exception $e) {
            return null;
        }
    }

    private function utf8ize($d) {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = $this->utf8ize($v);
            }
        } else if (is_string($d)) {
            return mb_convert_encoding($d, 'UTF-8', 'UTF-8');
        }
        return $d;
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function index()
    {
        $client = $this->getClient();
        $routerStatus = $client ? 'Connected' : 'Disconnected';
        $activeUsers = [];

        if ($client) {
            try {
                // Fetch active users
                $activeUsersRaw = $client->query('/ip/hotspot/active/print')->read();
                
                // Format bytes
                foreach ($activeUsersRaw as &$user) {
                    $user['bytes-in_fmt'] = $this->formatBytes($user['bytes-in'] ?? 0);
                    $user['bytes-out_fmt'] = $this->formatBytes($user['bytes-out'] ?? 0);
                }
                
                // Handle UTF-8 issues
                $activeUsers = $this->utf8ize($activeUsersRaw);

            } catch (Exception $e) {
                $routerStatus = 'Error: ' . $e->getMessage();
            }
        }

        return view('voucher_online', compact('activeUsers', 'routerStatus'));
    }

    public function kick(Request $request)
    {
        $id = $request->query('id');
        if (!$id) return redirect()->back()->with('error', 'User ID not provided.');

        $client = $this->getClient();
        if ($client) {
            try {
                $query = (new Query('/ip/hotspot/active/remove'))->equal('.id', $id);
                $client->query($query)->read();
                return redirect()->back()->with('success', 'User disconnected successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Failed to disconnect user: ' . $e->getMessage());
            }
        }
        return redirect()->back()->with('error', 'Router not connected.');
    }
}
