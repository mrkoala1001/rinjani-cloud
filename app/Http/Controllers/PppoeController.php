<?php

namespace App\Http\Controllers;

use App\Models\MikrotikConfig;
use App\Models\PppoeProfileMetadata;
use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Query;
use Exception;
use Illuminate\Support\Str;

class PppoeController extends Controller
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
                'timeout' => 10,
            ]);
        } catch (Exception $e) {
            return null;
        }
    }

    // --- PPPoE Active ---
    public function active() {
        $active = [];
        $routerStatus = 'Disconnected';
        $client = $this->getClient();

        if ($client) {
            try {
                $active = $client->query('/ppp/active/print')->read();
                $secrets = $client->query('/ppp/secret/print')->read();
                
                // Map secrets by name for quick lookup
                $secretMap = [];
                foreach ($secrets as $secret) {
                    if (isset($secret['name'])) {
                        $secretMap[$secret['name']] = $secret['profile'] ?? '-';
                    }
                }

                // Add profile to active users
                foreach ($active as &$session) {
                    if (isset($session['name']) && isset($secretMap[$session['name']])) {
                        $session['profile'] = $secretMap[$session['name']];
                    } else {
                        $session['profile'] = '-';
                    }
                }

                $routerStatus = 'Connected';
            } catch (Exception $e) {
                $routerStatus = 'Error: ' . $e->getMessage();
            }
        }
        
        return view('pppoe.active', compact('active', 'routerStatus'));
    }

    public function kickActive($id) {
        $client = $this->getClient();
        if ($client) {
            try {
                $query = (new Query('/ppp/active/remove'))->equal('.id', $id);
                $client->query($query)->read();
                return redirect()->back()->with('success', 'Session removed successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Failed to remove session: ' . $e->getMessage());
            }
        }
        return redirect()->back()->with('error', 'Router not connected.');
    }

    // --- PPPoE Secrets ---
    public function secrets() {
        $secrets = [];
        $profiles = [];
        $routerStatus = 'Disconnected';
        $client = $this->getClient();

        if ($client) {
            try {
                $secrets = $client->query('/ppp/secret/print')->read();
                $profiles = $client->query('/ppp/profile/print')->read();
                $routerStatus = 'Connected';

                // Get correct user context for metadata
                $user = auth()->user();
                $contextUserId = ($user->role === 'mitra-reseller' || $user->role === 'mitra') ? $user->created_by : $user->id;

                // Filter profiles if mitra-reseller
                if ($user->role === 'mitra-reseller' && !session()->has('impersonated_by')) {
                    $managedProfileNames = PppoeProfileMetadata::withoutGlobalScopes()
                        ->where('user_id', $contextUserId)
                        ->pluck('profile_name')
                        ->toArray();
                        
                    $profiles = array_values(array_filter($profiles, function($p) use ($managedProfileNames) {
                        return in_array($p['name'], $managedProfileNames);
                    }));
                    
                    // Also filter secrets to show only those with managed profiles
                    $secrets = array_values(array_filter($secrets, function($s) use ($managedProfileNames) {
                        return in_array($s['profile'], $managedProfileNames);
                    }));
                }
            } catch (Exception $e) {
                $routerStatus = 'Error: ' . $e->getMessage();
            }
        }

        return view('pppoe.secrets', compact('secrets', 'profiles', 'routerStatus'));
    }

    public function storeSecret(Request $request) {
        $client = $this->getClient();
        if (!$client) return redirect()->back()->with('error', 'Router not connected.');

        try {
            // Get correct user context for metadata
            $user = auth()->user();
            $contextUserId = ($user->role === 'mitra-reseller' || $user->role === 'mitra') ? $user->created_by : $user->id;

            // Validate profile if mitra-reseller
            if ($user->role === 'mitra-reseller' && !session()->has('impersonated_by')) {
                $managedProfiles = PppoeProfileMetadata::withoutGlobalScopes()
                    ->where('user_id', $contextUserId)
                    ->pluck('profile_name')
                    ->toArray();
                if (!in_array($request->profile, $managedProfiles)) {
                    return redirect()->back()->with('error', 'Profile not allowed.');
                }
            }

            $data = [
                'name' => $request->name,
                'password' => $request->password,
                'profile' => $request->profile,
                'service' => 'pppoe', // Default to PPPoE
            ];
            
            if ($request->filled('local_address')) $data['local-address'] = $request->local_address;
            if ($request->filled('remote_address')) $data['remote-address'] = $request->remote_address;
            if ($request->filled('comment')) $data['comment'] = $request->comment;

            $query = new Query('/ppp/secret/add');
            foreach ($data as $k => $v) {
                $query->equal($k, $v);
            }
            
            $client->query($query)->read();
            return redirect()->back()->with('success', 'Secret added successfully.');
        } catch (Exception $e) {
             return redirect()->back()->with('error', 'Failed to add secret: ' . $e->getMessage());
        }
    }

    public function deleteSecret($id) {
        $client = $this->getClient();
        if ($client) {
            try {
                $query = (new Query('/ppp/secret/remove'))->equal('.id', $id);
                $client->query($query)->read();
                return redirect()->back()->with('success', 'Secret deleted successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Failed to delete secret: ' . $e->getMessage());
            }
        }
        return redirect()->back()->with('error', 'Router not connected.');
    }

    // --- PPPoE Profiles ---
    public function profiles() {
        $profiles = [];
        $routerStatus = 'Disconnected';
        $client = $this->getClient();

        if ($client) {
            try {
                $profiles = $client->query('/ppp/profile/print')->read();
                
                // Get correct user context for metadata
                $user = auth()->user();
                $contextUserId = ($user->role === 'mitra-reseller' || $user->role === 'mitra') ? $user->created_by : $user->id;

                // Merge with metadata
                foreach ($profiles as &$prof) {
                    $prof['local_metadata'] = PppoeProfileMetadata::withoutGlobalScopes()
                        ->where('user_id', $contextUserId)
                        ->where('profile_name', $prof['name'])
                        ->first();
                }

                if ($user->role === 'mitra-reseller' && !session()->has('impersonated_by')) {
                    $profiles = array_values(array_filter($profiles, function($p) {
                        return isset($p['local_metadata']) && $p['local_metadata'] !== null;
                    }));
                }

                $routerStatus = 'Connected';
            } catch (Exception $e) {
                 $routerStatus = 'Error: ' . $e->getMessage();
            }
        }
        return view('pppoe.profiles', compact('profiles', 'routerStatus'));
    }
    
    public function storeProfile(Request $request) {
        $client = $this->getClient();
        if (!$client) return redirect()->back()->with('error', 'Router not connected.');

        try {
            $data = [];
            if ($request->filled('name')) $data['name'] = $request->name;
            if ($request->filled('local_address')) $data['local-address'] = $request->local_address;
            if ($request->filled('remote_address')) $data['remote-address'] = $request->remote_address;
            if ($request->filled('rate_limit')) $data['rate-limit'] = $request->rate_limit;
            if ($request->filled('dns_server')) $data['dns-server'] = $request->dns_server;

            $query = new Query('/ppp/profile/add');
            foreach ($data as $k => $v) {
                $query->equal($k, $v);
            }
            
            $result = $client->query($query)->read();
            
            // Periksa apakah ada error dari MikroTik
            if (isset($result['after']['message'])) {
                return redirect()->back()->with('error', 'Gagal membuat profile: ' . $result['after']['message']);
            }

            // Save Metadata
            PppoeProfileMetadata::updateOrCreate(
                ['profile_name' => $request->name],
                [
                    'price' => $request->price ?? 0,
                    'selling_price' => $request->selling_price ?? 0
                ]
            );

            return redirect()->back()->with('success', 'Profile added successfully.');

        } catch (Exception $e) {
             return redirect()->back()->with('error', 'Failed to add profile: ' . $e->getMessage());
        }
    }
    
    public function updateProfile(Request $request) {
        $client = $this->getClient();
        if (!$client) return redirect()->back()->with('error', 'Router not connected.');

        try {
            $data = [];
            if ($request->filled('name')) $data['name'] = $request->name;
            if ($request->filled('local_address')) $data['local-address'] = $request->local_address;
            if ($request->filled('remote_address')) $data['remote-address'] = $request->remote_address;
            if ($request->filled('rate_limit')) $data['rate-limit'] = $request->rate_limit;
            if ($request->filled('dns_server')) $data['dns-server'] = $request->dns_server;
            if ($request->filled('comment')) $data['comment'] = $request->comment;

            $query = new Query('/ppp/profile/set');
            $query->equal('.id', $request->id);
            foreach ($data as $k => $v) {
                $query->equal($k, $v);
            }
            
            $result = $client->query($query)->read();

            // Periksa apakah ada error dari MikroTik
            if (isset($result['after']['message'])) {
                return redirect()->back()->with('error', 'Gagal update profile: ' . $result['after']['message']);
            }

            // Update Metadata
            PppoeProfileMetadata::updateOrCreate(
                ['profile_name' => $request->name],
                [
                    'price' => $request->price ?? 0,
                    'selling_price' => $request->selling_price ?? 0
                ]
            );

            return redirect()->back()->with('success', 'Profile updated successfully.');

        } catch (Exception $e) {
             return redirect()->back()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }
    
    public function deleteProfile($id) {
        $client = $this->getClient();
         if ($client) {
            try {
                // Find profile name first if possible
                $profiles = $client->query('/ppp/profile/print')->read();
                $profName = null;
                foreach ($profiles as $p) {
                    if ($p['.id'] == $id) {
                        $profName = $p['name'];
                        break;
                    }
                }

                $query = (new Query('/ppp/profile/remove'))->equal('.id', $id);
                $client->query($query)->read();

                if ($profName) {
                    PppoeProfileMetadata::where('profile_name', $profName)->delete();
                }

                return redirect()->back()->with('success', 'Profile deleted successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Failed to delete profile: ' . $e->getMessage());
            }
        }
        return redirect()->back()->with('error', 'Router not connected.');
    }
    
    public function updateSecret(Request $request) {
        $client = $this->getClient();
        if (!$client) return redirect()->back()->with('error', 'Router not connected.');

        try {
            // Get correct user context for metadata
            $user = auth()->user();
            $contextUserId = ($user->role === 'mitra-reseller' || $user->role === 'mitra') ? $user->created_by : $user->id;

            // Validate profile if mitra-reseller
            if ($request->filled('profile') && $user->role === 'mitra-reseller' && !session()->has('impersonated_by')) {
                $managedProfiles = PppoeProfileMetadata::withoutGlobalScopes()
                    ->where('user_id', $contextUserId)
                    ->pluck('profile_name')
                    ->toArray();
                if (!in_array($request->profile, $managedProfiles)) {
                    return redirect()->back()->with('error', 'Profile not allowed.');
                }
            }

            $data = [];
            if ($request->filled('name')) $data['name'] = $request->name;
            if ($request->filled('password')) $data['password'] = $request->password;
            if ($request->filled('profile')) $data['profile'] = $request->profile;
            if ($request->filled('service')) $data['service'] = $request->service;
            if ($request->filled('local_address')) $data['local-address'] = $request->local_address;
            if ($request->filled('remote_address')) $data['remote-address'] = $request->remote_address;
            if ($request->filled('comment')) $data['comment'] = $request->comment;

            $query = new Query('/ppp/secret/set');
            $query->equal('.id', $request->id);
            foreach ($data as $k => $v) {
                $query->equal($k, $v);
            }
            
            $client->query($query)->read();
            return redirect()->back()->with('success', 'Secret updated successfully.');

        } catch (Exception $e) {
             return redirect()->back()->with('error', 'Failed to update secret: ' . $e->getMessage());
        }
    }


}
