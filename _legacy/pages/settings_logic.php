<?php
// pages/settings_logic.php

$message = '';
$message_type = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_config'])) {
    $host = $_POST['host'];
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $port = $_POST['port'] ?: 8728;

    // Check if config exists
    $check = $conn->query("SELECT id FROM mikrotik_config LIMIT 1");
    if ($check->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE mikrotik_config SET host=?, user=?, pass=?, port=? WHERE id=1"); 
        $stmt->bind_param("sssi", $host, $user, $pass, $port);
    } else {
        $stmt = $conn->prepare("INSERT INTO mikrotik_config (host, user, pass, port) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $host, $user, $pass, $port);
    }

    if ($stmt->execute()) {
        set_flash_message('success', 'Configuration saved successfully!');
        // Redirect logic handled here before HTML output
        header("Location: " . base_url('index.php?page=settings'));
        exit;
    } else {
        $message = "Error saving config: " . $conn->error;
        $message_type = 'error';
    }
}

// Handle Test Connection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_connection'])) {
    $host = $_POST['host'];
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $port = $_POST['port'] ?: 8728;

    $API = new RouterOS\Client();
    $API->timeout = 3;
    if ($API->connect($host, $user, $pass, $port)) {
        $API->disconnect();
        $message = "Connection successful!";
        $message_type = 'success';
    } else {
        $message = "Connection failed!";
        $message_type = 'error';
    }
}
?>
