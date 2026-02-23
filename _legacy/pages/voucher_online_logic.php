<?php
// pages/voucher_online_logic.php

$config = get_mikrotik_config($conn);
$active_users = [];
$router_connected = false;

if ($config) {
    $API = new RouterOS\Client();
    if ($API->connect($config['host'], $config['user'], $config['pass'])) {
        $router_connected = true;
        
        // Handle Kick User
        if (isset($_GET['kick'])) {
            $user_id = $_GET['kick'];
            $API->comm('/ip/hotspot/active/remove', ['.id' => $user_id]);
            set_flash_message('success', "User disconnected.");
            header("Location: index.php?page=voucher_online");
            exit;
        }

        $active_users = $API->comm('/ip/hotspot/active/print');
        
        // Pre-format bytes for the front-end
        foreach ($active_users as &$user) {
            $user['bytes-in_fmt'] = format_bytes($user['bytes-in'] ?? 0);
            $user['bytes-out_fmt'] = format_bytes($user['bytes-out'] ?? 0);
        }
        
        $API->disconnect();
    }
}
?>
