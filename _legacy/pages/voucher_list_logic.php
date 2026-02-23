<?php
// pages/voucher_list_logic.php

$config = get_mikrotik_config($conn);
$router_connected = false;
$users = [];

if ($config) {
    $API = new RouterOS\Client();
    if ($API->connect($config['host'], $config['user'], $config['pass'])) {
        $router_connected = true;

        // Handle Sync/Refresh
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sync_users'])) {
            // Already connected, just redirect to refresh
            set_flash_message('success', 'Daftar voucher berhasil diperbarui dari MikroTik.');
            header("Location: " . base_url('index.php?page=voucher_list'));
            exit;
        }

        // Handle Delete User
        if (isset($_GET['del'])) {
            $id = $_GET['del'];
            $API->comm('/ip/hotspot/user/remove', ['.id' => $id]);
            set_flash_message('success', 'Voucher berhasil dihapus.');
            header("Location: " . base_url('index.php?page=voucher_list'));
            exit;
        }

        // Fetch Hotspot Users
        $users = $API->comm('/ip/hotspot/user/print');
        
        $API->disconnect();
    }
}
?>
