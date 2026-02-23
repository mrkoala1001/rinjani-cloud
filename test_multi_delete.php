<?php
require 'vendor/autoload.php';
use RouterOS\Client;
use RouterOS\Query;

$client = new Client([
    'host' => '103.154.25.94',
    'user' => 'marwadi',
    'pass' => 'Semangat45',
    'port' => 527,
    'timeout' => 5,
]);

$ids = [];
for($i=0; $i<3; $i++) {
    $name = "multi-$i-".time();
    $q = new Query('/ip/hotspot/user/add');
    $q->add('=name=' . $name);
    $client->query($q)->read();
    
    $users = $client->query('/ip/hotspot/user/print')->read();
    foreach ($users as $u) {
        if ($u['name'] === $name) { $ids[] = $u['.id']; break; }
    }
}

echo "Attempts to delete multiple IDs: " . implode(',', $ids) . "\n";
try {
    $q = new Query('/ip/hotspot/user/remove');
    $q->add('=.id=' . implode(',', $ids));
    $res = $client->query($q)->read();
    echo "Response: "; print_r($res);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$users = $client->query('/ip/hotspot/user/print')->read();
foreach($ids as $id) {
    foreach($users as $u) {
        if ($u['.id'] === $id) {
            echo "FAILED: $id still exists!\n";
        }
    }
}
echo "Check finished.\n";
