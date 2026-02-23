<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/routeros_api.class.php';
require_once 'includes/functions.php';

// Debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Simple Router
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
// Allow resellers page and new voucher modules
$allowed_pages = [
    'dashboard', 'vouchers', 'pppoe_secrets', 'billing', 'settings', 'resellers',
    'voucher_profiles', 'voucher_online', 'voucher_sold', 'voucher_recap', 'voucher_templates',
    'customer_members', 'pppoe_active', 'pppoe_profiles', 'pppoe_data', 'voucher_list',
    'customer_list', 'customer_form'
];

if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

// Check for logic file (pre-header)
$logic_file = "pages/{$page}_logic.php";
if (file_exists($logic_file)) {
    include $logic_file;
}

include 'includes/header.php';

$file_path = "pages/{$page}.php";
if (file_exists($file_path)) {
    include $file_path;
} else {
    echo '<div class="text-center text-gray-500 mt-10">Page not found or under construction.</div>';
}

include 'includes/footer.php';
?>
