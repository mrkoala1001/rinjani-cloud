<?php
// pages/voucher_profiles_logic.php

$config = get_mikrotik_config($conn);
$router_connected = false;
$profiles = [];

if ($config) {
    $API = new RouterOS\Client();
    if ($API->connect($config['host'], $config['user'], $config['pass'])) {
        $router_connected = true;

        // Handle Add Profile
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
            $name = trim($_POST['name']);
            $rate_limit = trim($_POST['rate_limit'] ?? '');
            $shared_users = (string)($_POST['shared_users'] ?: 1);
            $validity = trim($_POST['validity'] ?? '');
            $price = trim($_POST['price'] ?? '0');
            $sell_price = trim($_POST['sell_price'] ?? '0');
            
            $add_data = [
                'name' => $name,
                'shared-users' => $shared_users
            ];

            if (!empty($rate_limit)) {
                $add_data['rate-limit'] = $rate_limit;
            }
            
            // Create Profile in MikroTik (NO COMMENT)
            $response = $API->comm('/ip/hotspot/user/profile/add', $add_data);

            if (isset($response['!trap'])) {
                $errorMsg = $response['!trap'][0]['message'] ?? 'Unknown MikroTik error';
                set_flash_message('error', "Gagal membuat profil di MikroTik: $errorMsg");
            } else {
                // Save metadata locally
                $stmt = $conn->prepare("INSERT INTO hotspot_profile_metadata (profile_name, price, selling_price, validity) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE price=?, selling_price=?, validity=?");
                $stmt->bind_param("sdddsdd", $name, $price, $sell_price, $validity, $price, $sell_price, $validity);
                $stmt->execute();
                
                set_flash_message('success', "Profile $name berhasil dibuat (Metadata disimpan lokal)!");
            }

            header("Location: " . base_url('index.php?page=voucher_profiles'));
            exit;
        }

        // Handle Delete Profile
        if (isset($_GET['del'])) {
            $id = $_GET['del'];
            $name = $_GET['name'] ?? '';
            $API->comm('/ip/hotspot/user/profile/remove', ['.id' => $id]);
            
            // Optionally delete local metadata
            if ($name) {
                $stmt = $conn->prepare("DELETE FROM hotspot_profile_metadata WHERE profile_name = ?");
                $stmt->bind_param("s", $name);
                $stmt->execute();
            }

            set_flash_message('success', "Profile removed.");
            header("Location: " . base_url('index.php?page=voucher_profiles'));
            exit;
        }

        // Handle Sync Profiles (Import from Script)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sync_profiles'])) {
            $profiles_mt = $API->comm('/ip/hotspot/user/profile/print');
            $synced = 0;
            foreach ($profiles_mt as $p) {
                $script = $p['on-login'] ?? '';
                $price = 0; $sell_price = 0; $validity = '';
                
                if (preg_match('/#\s*price=([0-9]+)/i', $script, $m)) $price = $m[1];
                if (preg_match('/#\s*selling_price=([0-9]+)/i', $script, $m)) $sell_price = $m[1];
                if (preg_match('/#\s*validity=([^\s\r\n]+)/i', $script, $m)) $validity = $m[1];

                // Mikhmon :put format
                if (preg_match('/put\s*\(\",.*?,(.*?),(.*?),(.*?),/i', $script, $m)) {
                    $price = $m[1];
                    $validity = $m[2];
                    $sell_price = $m[3];
                }

                if ($price > 0 || !empty($validity)) {
                    $stmt = $conn->prepare("INSERT INTO hotspot_profile_metadata (profile_name, price, selling_price, validity) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE price=?, selling_price=?, validity=?");
                    $stmt->bind_param("sdddsdd", $p['name'], $price, $sell_price, $validity, $price, $sell_price, $validity);
                    if ($stmt->execute()) $synced++;
                }
            }
            set_flash_message('success', "Berhasil menyinkronkan $synced profil dari MikroTik ke database lokal.");
            header("Location: " . base_url('index.php?page=voucher_profiles'));
            exit;
        }

        // Fetch Profiles from MikroTik
        $profiles_mt = $API->comm('/ip/hotspot/user/profile/print');
        
        // Fetch Metadata from DB
        $metadata_res = $conn->query("SELECT * FROM hotspot_profile_metadata");
        $metadata = [];
        while($m = $metadata_res->fetch_assoc()) {
            $metadata[$m['profile_name']] = $m;
        }

        // Merge Metadata with MikroTik Profiles
        $profiles = [];
        foreach ($profiles_mt as $p) {
            $p_name = $p['name'];
            $meta = $metadata[$p_name] ?? null;

            // Fallback: Parse from MikroTik script (Mikhmon style)
            if (!$meta) {
                $script = $p['on-login'] ?? '';
                $price = 0; $sell_price = 0; $validity = '-';
                
                if (preg_match('/#\s*price=([0-9]+)/i', $script, $m)) $price = $m[1];
                if (preg_match('/#\s*selling_price=([0-9]+)/i', $script, $m)) $sell_price = $m[1];
                if (preg_match('/#\s*validity=([^\s\r\n]+)/i', $script, $m)) $validity = $m[1];

                // Mikhmon :put format
                if (preg_match('/put\s*\(\",.*?,(.*?),(.*?),(.*?),/i', $script, $m)) {
                    $price = $m[1];
                    $validity = $m[2];
                    $sell_price = $m[3];
                }

                if ($price > 0 || $validity !== '-') {
                    $meta = [
                        'price' => $price,
                        'selling_price' => $sell_price,
                        'validity' => $validity
                    ];
                }
            }

            $p['local_metadata'] = $meta;
            $profiles[] = $p;
        }
        $API->disconnect();
    }
}
?>
