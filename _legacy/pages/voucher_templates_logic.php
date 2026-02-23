<?php
// pages/voucher_templates_logic.php

// Handle Add Template
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_template'])) {
    $name = $_POST['name'];
    $raw_content = $_POST['html_content']; // Now contains both HTML and CSS

    // Extract CSS from <style> tags
    $css = '';
    $html = $raw_content;
    
    if (preg_match('/<style[^>]*>(.*?)<\/style>/is', $raw_content, $matches)) {
        $css = $matches[1];
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $raw_content);
    }
    
    // Trim whitespace
    $css = trim($css);
    $html = trim($html);

    $stmt = $conn->prepare("INSERT INTO voucher_templates (name, html_content, css_content) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $html, $css);
    
    if ($stmt->execute()) {
        set_flash_message('success', 'Template saved.');
    } else {
        set_flash_message('error', 'Error: ' . $conn->error);
    }
    header("Location: index.php?page=voucher_templates");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM voucher_templates WHERE id = $id");
    set_flash_message('success', 'Template deleted.');
    header("Location: index.php?page=voucher_templates");
    exit;
}

$templates = $conn->query("SELECT * FROM voucher_templates ORDER BY id DESC");
?>
