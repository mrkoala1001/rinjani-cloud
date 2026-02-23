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

// 1. Add a test user
$name = 'test-' . time();
echo "Adding user $name...\n";
$q = new Query('/ip/hotspot/user/add');
$q->add('=name=' . $name);
$client->query($q)->read();

// 2. Find it and get ID
echo "Finding user...\n";
$users = $client->query('/ip/hotspot/user/print')->read();
$id = null;
foreach ($users as $u) {
    if ($u['name'] === $name) {
        $id = $u['.id'];
        break;
    }
}

if (!$id) {
    die("User not found after add!\n");
}
echo "Found ID: $id\n";

// 3. Try to delete using different formats
echo "Attempting to delete with .id=$id...\n";
try {
    // Try passing .id directly as array param
    $res = $client->query('/ip/hotspot/user/remove', ['.id' => $id])->read();
    echo "Response: "; print_r($res);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// 4. Verify
echo "Verifying if $name still exists...\n";
$users = $client->query('/ip/hotspot/user/print')->read();
$found = false;
foreach ($users as $u) {
    if ($u['name'] === $name) {
        $found = true;
        break;
    }
}

if ($found) {
    echo "STILL EXISTS! Deletion failed using .id\n";
    
    echo "Attempting to delete with name instead of ID...\n";
    try {
        // Some systems allow 'numbers' as name
        $client->query('/ip/hotspot/user/remove', ['numbers' => $name])->read();
    } catch (Exception $e) {
        echo "Error (Name): " . $e->getMessage() . "\n";
    }
    
    // Verify again
    $users = $client->query('/ip/hotspot/user/print')->read();
    $found = false;
    foreach ($users as $u) {
        if ($u['name'] === $name) {
            $found = true;
            break;
        }
    }
    
    if ($found) {
        echo "STILL EXISTS! Deletion failed using name\n";
        
        echo "Attempting to delete with =numbers=$id (Query object)...\n";
        try {
            $q = new Query('/ip/hotspot/user/remove');
            $q->add('=numbers=' . $id);
            $client->query($q)->read();
        } catch (Exception $e) {
             echo "Error (Query Object): " . $e->getMessage() . "\n";
        }
        
        // Verify again
        $users = $client->query('/ip/hotspot/user/print')->read();
        $found = false;
        foreach ($users as $u) {
            if ($u['name'] === $name) {
                $found = true;
                break;
            }
        }
        
        if ($found) {
            echo "STILL EXISTS! All methods failed.\n";
        } else {
            echo "SUCCESS! Deleted using Query Object with =numbers=ID\n";
        }
    } else {
        echo "SUCCESS! Deleted using numbers => name\n";
    }
} else {
    echo "SUCCESS! Deleted using .id\n";
}
