<?php
require 'vendor/autoload.php';
use RouterOS\Client;

$client = new Client([
    'host' => '103.154.25.94',
    'user' => 'marwadi',
    'pass' => 'Semangat45',
    'port' => 527,
    'timeout' => 5,
]);

$users = $client->query('/ip/hotspot/user/print')->read();
echo "Total Users in MikroTik: " . count($users) . "\n";
foreach ($users as $i => $u) {
    echo ($i+1) . ". Name: " . ($u['name'] ?? 'N/A') . " | ID: " . ($u['.id'] ?? 'N/A') . "\n";
    if ($i >= 10) break;
}
