<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MikrotikConfig;
use RouterOS\Client;
use RouterOS\Query;
use Exception;

class WanStaticController extends Controller
{
    private function getClient($userId = null)
    {
        $userId = $userId ?? auth()->id();
        $config = MikrotikConfig::where('user_id', $userId)->first();

        if (!$config) {
            throw new Exception('Mikrotik configuration not found.');
        }

        return new Client([
            'host' => $config->host,
            'user' => $config->user,
            'pass' => $config->pass,
            'port' => (int)($config->port ?? 8728),
            'timeout' => 10,
        ]);
    }

    public function index(Request $request)
    {
        try {
            $client = $this->getClient();
            $query = new Query('/queue/simple/print');
            $queues = $client->query($query)->read();

            // Filter/Process queues if they are "Static IP" related
            // Usually, we can identify them by name or comment, but here we just list simple queues
            // as requested.

            return view('wan_static.index', [
                'queues' => $queues,
                'routerStatus' => 'Connected'
            ]);
        } catch (Exception $e) {
            return view('wan_static.index', [
                'queues' => [],
                'routerStatus' => 'Disconnected',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'target' => 'required',
            'max_limit_up' => 'required',
            'max_limit_down' => 'required',
        ]);

        try {
            $client = $this->getClient();
            
            // Format max-limit: upload/download
            // MikroTik usually expects bits per second if no suffix is provided.
            // But we should allow M, k etc.
            $maxLimit = $request->max_limit_up . '/' . $request->max_limit_down;

            $query = (new Query('/queue/simple/add'))
                ->equal('name', $request->name)
                ->equal('target', $request->target)
                ->equal('max-limit', $maxLimit);

            if ($request->comment) {
                $query->equal('comment', $request->comment);
            }

            $response = $client->query($query)->read();

            if (isset($response['after']['message'])) {
                return redirect()->back()->with('error', 'MikroTik Error: ' . $response['after']['message']);
            }

            return redirect()->back()->with('success', 'WAN-IP STATIC berhasil ditambahkan.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'target' => 'required',
            'max_limit_up' => 'required',
            'max_limit_down' => 'required',
        ]);

        try {
            $client = $this->getClient();
            $maxLimit = $request->max_limit_up . '/' . $request->max_limit_down;

            $query = (new Query('/queue/simple/set'))
                ->equal('.id', $request->id)
                ->equal('name', $request->name)
                ->equal('target', $request->target)
                ->equal('max-limit', $maxLimit);

            if ($request->comment) {
                $query->equal('comment', $request->comment);
            }

            $client->query($query)->read();

            return redirect()->back()->with('success', 'WAN-IP STATIC berhasil diperbarui.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $client = $this->getClient();
            $query = (new Query('/queue/simple/remove'))
                ->equal('.id', $id);

            $client->query($query)->read();

            return redirect()->back()->with('success', 'WAN-IP STATIC berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}
