<?php
// pages/resellers_logic.php

// Handle Add Reseller
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_reseller'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $balance = 0; // Default

    $stmt = $conn->prepare("INSERT INTO resellers (name, phone, balance) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $phone, $balance);
    
    if ($stmt->execute()) {
        set_flash_message('success', 'Reseller added successfully!');
    } else {
        set_flash_message('error', 'Error adding reseller: ' . $conn->error);
    }
    // Refresh to clear post
    header("Location: " . base_url('index.php?page=resellers'));
    exit;
}
?>
