<?php
// includes/db.php

$host = 'localhost';
$user = 'admin'; 
$pass = '@Deepleo1001'; 
$db_name = 'hotpot_db';

// Enable exception reporting for mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass);
    
    // Create DB if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS $db_name";
    $conn->query($sql);
    $conn->select_db($db_name);

} catch (mysqli_sql_exception $e) {
    die("Database Connection Error: " . $e->getMessage() . "<br>Please ensure the database user exists and has permissions.");
}

// Check tables
// We rely on database.sql import, but for simplicity in this script we can run it here or manually.
// For now, let's assume the user runs the SQL or we run it via command.
