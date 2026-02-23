<?php
// pages/billing_logic.php

// Sync Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sync_sales'])) {
    $config = get_mikrotik_config($conn);
    if ($config) {
        $API = new RouterOS\Client();
        if ($API->connect($config['host'], $config['user'], $config['pass'])) {
            
            // 1. Get Active Hotspot Users
            $active_users = $API->comm('/ip/hotspot/active/print');
            
            // 2. Get Profile Prices (Try Local DB first, then Script Fallback)
            $metadata_res = $conn->query("SELECT profile_name, price FROM hotspot_profile_metadata");
            $profile_prices = [];
            while($meta = $metadata_res->fetch_assoc()) {
                $profile_prices[$meta['profile_name']] = $meta['price'];
            }
            
            // Fetch Profiles from MikroTik for script fallback
            $profiles_mt = $API->comm('/ip/hotspot/user/profile/print');
            foreach ($profiles_mt as $p) {
                if (!isset($profile_prices[$p['name']])) {
                    $script = $p['on-login'] ?? '';
                    if (preg_match('/#\s*price=([0-9]+)/i', $script, $m)) {
                        $profile_prices[$p['name']] = intval($m[1]);
                    }
                    // Mikhmon :put format fallback
                    if (preg_match('/put\s*\(\",.*?,(.*?),(.*?),(.*?),/i', $script, $m)) {
                        $profile_prices[$p['name']] = intval($m[3]); // m[3] is selling_price in Mikhmon put string
                    }
                }
            }
            
            // Add default/fallback if still not set
            if (!isset($profile_prices['default'])) $profile_prices['default'] = 0;

            $synced_count = 0;
            
            foreach ($active_users as $user) {
                $username = $user['user'];
                // active print might not show profile directly in some versions, usually does.
                $profile_name = $user['login-by'] === 'mac' ? 'mac-login' : ($user['profile'] ?? 'default'); 
                
                $check_stmt = $conn->prepare("SELECT id FROM billing_history WHERE voucher_code = ? AND MONTH(date_sold) = MONTH(CURRENT_DATE()) AND YEAR(date_sold) = YEAR(CURRENT_DATE())");
                $check_stmt->bind_param("s", $username);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows == 0) {
                     $price = 0;
                     $server = $user['server'] ?? 'all';
                    
                     $user_details = $API->comm('/ip/hotspot/user/print', ['?name' => $username]);
                     if (isset($user_details[0])) {
                         $real_profile = $user_details[0]['profile'];
                         $server = $user_details[0]['server'] ?? $server;
                         $price = $profile_prices[$real_profile] ?? 0;
                         
                         $insert = $conn->prepare("INSERT INTO billing_history (voucher_code, price, profile, server) VALUES (?, ?, ?, ?)");
                         $insert->bind_param("sdss", $username, $price, $real_profile, $server);
                         if ($insert->execute()) {
                             $synced_count++;
                         }
                     }
                }
            }
            
            set_flash_message('success', "Synced $synced_count new sales from Active Users.");
            $API->disconnect();
            
            // Refresh to avoid resubmission
            header("Location: " . base_url('index.php?page=billing'));
            exit;
            
        } else {
             set_flash_message('error', 'Connection failed.');
        }
    }
}
?>
