<?php
// includes/functions.php

function base_url($path = '') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'];
    return $protocol . $domainName . '/' . ltrim($path, '/');
}

function redirect($path) {
    header("Location: " . base_url($path));
    exit;
}

function format_currency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function get_mikrotik_config($conn) {
    $stmt = $conn->prepare("SELECT * FROM mikrotik_config LIMIT 1");
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Generate a random voucher code with different character sets
function generate_code($length = 6, $prefix = '', $type = 'mixed') {
    $chars = [
        'numbers' => '23456789',
        'lowercase' => 'abcdefghijkmnpqrstuvwxyz23456789',
        'uppercase' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789',
        'mixed' => 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'
    ];
    
    $characters = $chars[$type] ?? $chars['mixed'];
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
    return $prefix . $randomString;
}

// Session Helper to store flash messages
function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // 'success', 'error', 'info'
        'text' => $message
    ];
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}

function format_flash_message($msg) {
    if (!$msg) return '';
    $colors = [
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error' => 'bg-red-100 border-red-400 text-red-700',
        'info' => 'bg-blue-100 border-blue-400 text-blue-700'
    ];
    $colorClass = $colors[$msg['type']] ?? $colors['info'];
    return "<div class='border-l-4 $colorClass p-4 mb-4 rounded shadow-sm' role='alert'>{$msg['text']}</div>";
}

function format_bytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function get_active_hotspot_data($conn) {
    $config = get_mikrotik_config($conn);
    if ($config) {
        $API = new RouterOS\Client();
        $API->timeout = 2; // Short timeout
        if ($API->connect($config['host'], $config['user'], $config['pass'])) {
            // Fetch specific fields for the table
            $active = $API->comm('/ip/hotspot/active/print', ['.proplist' => 'user,uptime']);
            $API->disconnect();
            
            // Return the data directly, filtering empty users if any
            $users_data = [];
            foreach ($active as $a) {
                if (isset($a['user'])) {
                    $users_data[] = [
                        'user' => $a['user'],
                        'uptime' => $a['uptime'] ?? '-'
                    ];
                }
            }
            return $users_data;
        }
    }
    return [];
}

