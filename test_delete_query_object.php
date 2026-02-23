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

$name = 'test-' . time();
echo "Adding user $name...\n";
$q = new Query('/ip/hotspot/user/add');
$q->add('=name=' . $name);
$client->query($q)->read();

echo "Finding user...\n";
$users = $client->query('/ip/hotspot/user/print')->read();
$id = null;
foreach ($users as $u) {
    if ($u['name'] === $name) { $id = $u['.id']; break; }
}
echo "Found ID: $id\n";

echo "Attempting delete with Query object (=.id=ID)...\n";
try {
    $q = new Query('/ip/hotspot/user/remove');
    // For many RouterOS versions, the parameter for remove is 'numbers' or just .id
    // But in API it's usually =.id=...
    $q->add('=.id=' . $id);
    $res = $client->query($q)->read();
    echo "Response: "; print_r($res);
} catch (Exception $e) {
    echo "Error 1: " . $e->getMessage() . "\n";
}

$found = false;
$users = $client->query('/ip/hotspot/user/print')->read();
foreach ($users as $u) { if ($u['name'] === $name) { $found = true; break; } }
if (!$found) die("SUCCESS! Deleted using =.id=ID\n");

echo "Attempting delete with Query object (=numbers=ID)...\n";
try {
    $q = new Query('/ip/hotspot/user/remove');
    $q->add('=numbers=' . $id);
    $client->query($q)->read();
} catch (Exception $e) {
    echo "Error 2: " . $e->getMessage() . "\n";
}

$found = false;
$users = $client->query('/ip/hotspot/user/print')->read();
foreach ($users as $u) { if ($u['name'] === $name) { $found = true; break; } }
if (!$found) die("SUCCESS! Deleted using =numbers=ID\n");

echo "STILL EXISTS! All methods failed.\n";
