<?php
// ajax/get_active_count.php
session_start();
require_once '../includes/db.php';
require_once '../includes/routeros_api.class.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$config = get_mikrotik_config($conn);
$response = ['success' => false, 'count' => 0, 'message' => 'Not connected'];

if ($config) {
    // Only connect if we have config
    $API = new RouterOS\Client();
    $API->timeout = 2; // Short timeout for AJAX
    
    if ($API->connect($config['host'], $config['user'], $config['pass'])) {
        // Active Hotspot
        $active_users = $API->comm('/ip/hotspot/active/print', ['count-only' => 'true']);
        $count = (int)$active_users; // Count of items
        
        // Actually 'count-only' returns just a number or array of count? 
        // Let's use standard print and count array for safety based on previous code usage
        // But count-only is faster. Let's stick to previous method for consistency:
        // $hotspot_active_count = count($API->comm('/ip/hotspot/active/print'));
        
        $all_active = $API->comm('/ip/hotspot/active/print');
        $count = count($all_active);
        
        $API->disconnect();
        
        // Update Cache
        $_SESSION['cache_active_users'] = $count;
        $_SESSION['cache_active_users_time'] = time();
        
        $response = ['success' => true, 'count' => $count];
    } else {
        $response['message'] = 'Connection failed';
    }
} else {
    $response['message'] = 'No config found';
}

echo json_encode($response);
?>
