<?php
// pages/pppoe_profiles_logic.php

$config = get_mikrotik_config($conn);
$ppp_profiles = [];
$router_connected = false;

if ($config) {
    $API = new RouterOS\Client();
    if ($API->connect($config['host'], $config['user'], $config['pass'])) {
        $router_connected = true;
        
        // Fetch Profiles
        $ppp_profiles = $API->comm('/ppp/profile/print');
        $API->disconnect();
    }
}
?>
