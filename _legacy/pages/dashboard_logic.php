<?php
// pages/dashboard_logic.php

// 1. Get Income Stats (All Time) - Synchronized with Voucher Sold & Recap
$total_income_query = "SELECT SUM(COALESCE(pm.price, bh.price)) as total_income 
                       FROM billing_history bh
                       LEFT JOIN hotspot_profile_metadata pm ON bh.profile = pm.profile_name";
$total_income_res = $conn->query($total_income_query);
$total_income = $total_income_res->fetch_assoc()['total_income'] ?? 0;

// Monthly for reference - Synchronized
$monthly_income_query = "SELECT SUM(COALESCE(pm.price, bh.price)) as total_income 
                         FROM billing_history bh
                         LEFT JOIN hotspot_profile_metadata pm ON bh.profile = pm.profile_name
                         WHERE MONTH(bh.date_sold) = MONTH(CURRENT_DATE()) AND YEAR(bh.date_sold) = YEAR(CURRENT_DATE())";
$monthly_income = $conn->query($monthly_income_query)->fetch_assoc()['total_income'] ?? 0;

// 2. Get Resellers Count
$total_resellers = $conn->query("SELECT COUNT(*) as total FROM resellers")->fetch_assoc()['total'] ?? 0;

// 3. Get MikroTik Stats
$hotspot_active_count = 0;
$total_voucher_count = 0;
$pppoe_users_count = 0;
$router_status = 'Disconnected';

$config = get_mikrotik_config($conn);

if ($config) {
    // Check if we already have the data (from voucher_online_logic.php for example)
    // but dashboard logic runs before page logic if included in index.php? No, index.php includes logic then header then page.
    // If we are on dashboard page, this logic runs.
    
    $API = new RouterOS\Client();
    $API->timeout = 2; 
    
    if ($API->connect($config['host'], $config['user'], $config['pass'])) {
        $router_status = 'Connected';
        
        // Active Hotspot
        // Changed to fetch data for menu display (table)
        $hotspot_active_raw = $API->comm('/ip/hotspot/active/print', ['.proplist' => 'user,uptime']);
        $hotspot_active_data = [];
        foreach ($hotspot_active_raw as $u) {
            if (isset($u['user'])) {
                $hotspot_active_data[] = [
                    'user' => $u['user'],
                    'uptime' => $u['uptime'] ?? '-'
                ];
            }
        }
        $hotspot_active_count = count($hotspot_active_data);

        // Total Hotspot Users (Vouchers)
        $total_voucher_count = count($API->comm('/ip/hotspot/user/print'));

        // PPPoE
        $pppoe_users_count = count($API->comm('/ppp/secret/print'));
        
        $API->disconnect();
    }
}
?>
