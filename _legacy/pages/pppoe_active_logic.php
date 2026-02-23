<?php
// pages/pppoe_active_logic.php

$config = get_mikrotik_config($conn);
$active_ppp = [];
$router_connected = false;

if ($config) {
    $API = new RouterOS\Client();
    if ($API->connect($config['host'], $config['user'], $config['pass'])) {
        $router_connected = true;
        
        // Handle Disconnect
        if (isset($_GET['remove'])) {
            $id = $_GET['remove'];
            $API->comm('/ppp/active/remove', ['.id' => $id]);
            set_flash_message('success', "Session disconnected.");
            header("Location: index.php?page=pppoe_active");
            exit;
        }

        $active_ppp = $API->comm('/ppp/active/print');
        $API->disconnect();
    }
}
?>
