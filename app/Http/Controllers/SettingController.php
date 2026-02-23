<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MikrotikConfig;
use RouterOS\Client;
use Exception;

class SettingController extends Controller
{
    public function index() {
        $config = MikrotikConfig::where('user_id', auth()->id())->first();
        return view('settings', compact('config'));
    }
    
    public function update(Request $request) {
        $validated = $request->validate([
            'host' => 'required',
            'user' => 'required',
            'pass' => 'required',
            'port' => 'nullable|integer',
        ]);
        
        $validated['user_id'] = auth()->id();
        
        MikrotikConfig::updateOrCreate(
            ['user_id' => auth()->id()],
            $validated
        );
        
        return redirect()->route('settings')->with('success', 'Konfigurasi MikroTik berhasil disimpan!');
    }
    
    public function testConnection(Request $request) {
        $validated = $request->validate([
            'host' => 'required',
            'user' => 'required',
            'pass' => 'required',
            'port' => 'nullable|integer',
        ]);
        
        try {
            $client = new Client([
                'host' => $validated['host'],
                'user' => $validated['user'],
                'pass' => $validated['pass'],
                'port' => (int)($validated['port'] ?? 8728),
                'timeout' => 5,
            ]);
            
            // Test with simple query
            $identity = $client->query('/system/identity/print')->read();
            $resource = $client->query('/system/resource/print')->read();
            
            $routerName = $identity[0]['name'] ?? 'Unknown';
            $boardName = $resource[0]['board-name'] ?? 'Unknown';
            $version = $resource[0]['version'] ?? 'Unknown';
            
            return redirect()->route('settings')->with('success', "✅ Koneksi Berhasil!\n\nRouter: {$routerName}\nBoard: {$boardName}\nVersion: {$version}");
            
        } catch (Exception $e) {
            return redirect()->route('settings')->with('error', "❌ Koneksi Gagal!\n\nError: " . $e->getMessage() . "\n\nPastikan:\n- Router online\n- IP/Host benar\n- Username/Password benar\n- API Port (8728) terbuka");
        }
    }

    public function disconnect() {
        MikrotikConfig::where('user_id', auth()->id())->delete();
        return redirect()->route('settings')->with('success', '✅ Jaringan MikroTik berhasil diputuskan!');
    }

    public function wipeData() {
        $userId = auth()->id();
        $isIsp = auth()->user()->role === 'isp';
        
        $models = [
            \App\Models\Income::class,
            \App\Models\BillingHistory::class,
            \App\Models\Expense::class,
            \App\Models\Debt::class,
            \App\Models\CustomerMember::class,
            \App\Models\Reseller::class,
            \App\Models\MikrotikConfig::class,
            \App\Models\VoucherTemplate::class,
            \App\Models\Report::class,
        ];

        foreach ($models as $modelClass) {
            if ($modelClass === \App\Models\Report::class) {
                $query = $modelClass::withoutGlobalScopes()
                    ->where('sender_id', $userId)
                    ->where('sender_type', \App\Models\User::class);
            } else {
                $query = $modelClass::withoutGlobalScopes()->where('user_id', $userId);
            }
            
            if ($isIsp) {
                if ($modelClass !== \App\Models\Report::class) {
                    $query->orWhereNull('user_id');
                }
            }
            $query->delete();
        }
        
        return redirect()->route('settings')->with('success', '🔥 Seluruh data billing dan konfigurasi telah dihapus secara permanen!');
    }
}
