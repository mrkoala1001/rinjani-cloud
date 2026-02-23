<?php
// pages/pppoe_logic.php

$config = get_mikrotik_config($conn);
$router_connected = false;
$secrets = []; // Not used here but good for consistency or if we move fetch logic
$profiles = [];

if ($config && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_secret'])) {
    $API = new RouterOS\Client();
    if ($API->connect($config['host'], $config['user'], $config['pass'])) {
        
        $name = $_POST['name'];
        $password = $_POST['password'];
        $service = $_POST['service'] ?? 'pppoe';
        $profile = $_POST['profile'];
        $local_address = $_POST['local_address'];
        $remote_address = $_POST['remote_address'];
        $comment = $_POST['comment'];

        $add_data = [
            'name' => $name,
            'password' => $password,
            'service' => $service,
            'profile' => $profile,
            'comment' => $comment
        ];

        if (!empty($local_address)) $add_data['local-address'] = $local_address;
        if (!empty($remote_address)) $add_data['remote-address'] = $remote_address;

        $API->comm('/ppp/secret/add', $add_data);
        set_flash_message('success', "PPPoE Secret added successfully!");
        
        $API->disconnect();
        
        header("Location: " . base_url('index.php?page=pppoe_secrets'));
        exit;
    } else {
        set_flash_message('error', 'Could not connect to MikroTik to add secret.');
    }
}
?>
