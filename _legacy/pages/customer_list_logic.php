<?php
// pages/customer_list_logic.php

// Handle CSV Import
if (isset($_POST['import']) && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    
    if (is_uploaded_file($file)) {
        $handle = fopen($file, "r");
        $headers = fgetcsv($handle, 1000, ","); // Skip header
        
        $count = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Map CSV columns to Database
            // CSV: JENIS PELANGGAN, NAMA, LOKASI, TANGGAL PEMBAYARAN, JUMLAH TAGIHAN, JUMLAH PEMBAYARAN, PASSWORD, NAMA ALAT, IP ALAT, IP WAN, USERNAME ALAT, PASSWORD ALAT, KOORDINAT, KETERANGAN NOMOR HP
            
            $type = $data[0] ?? 'HOTSPOT';
            $name = $data[1] ?? '';
            $location = $data[2] ?? '';
            $payment_date = !empty($data[3]) ? date('Y-m-d', strtotime($data[3])) : NULL;
            $bill_amount = $data[4] ?? 0;
            $paid_amount = $data[5] ?? 0;
            $password = $data[6] ?? '';
            $device_name = $data[7] ?? '';
            $device_ip = $data[8] ?? '';
            $wan_ip = $data[9] ?? '';
            $device_username = $data[10] ?? '';
            $device_password = $data[11] ?? '';
            $coordinates = $data[12] ?? '';
            $notes = $data[13] ?? '';

            // Simple validation
            if (empty($name)) continue;

            $stmt = $conn->prepare("INSERT INTO customer_members (type, name, location, payment_date, bill_amount, paid_amount, password, device_name, device_ip, wan_ip, device_username, device_password, coordinates, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssddssssssss", $type, $name, $location, $payment_date, $bill_amount, $paid_amount, $password, $device_name, $device_ip, $wan_ip, $device_username, $device_password, $coordinates, $notes);
            
            if ($stmt->execute()) {
                $count++;
            }
        }
        fclose($handle);
        set_flash_message('success', "Imported $count customers successfully.");
    } else {
        set_flash_message('error', "Failed to upload file.");
    }
    
    // Redirect to avoid resubmission
    header("Location: index.php?page=customer_list&type=" . ($_GET['type'] ?? 'all'));
    exit;
}

// Handle Delete
if (isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $conn->query("DELETE FROM customer_members WHERE id = $id");
    set_flash_message('success', "Customer deleted successfully.");
    header("Location: index.php?page=customer_list&type=" . ($_GET['type'] ?? 'all'));
    exit;
}

// Fetch Customers
$type_filter = $_GET['type'] ?? 'all';
$search_query = $_GET['search'] ?? '';

$sql = "SELECT * FROM customer_members WHERE 1=1";

if ($type_filter !== 'all') {
    $type_safe = $conn->real_escape_string($type_filter);
    $sql .= " AND type = '$type_safe'";
}

if (!empty($search_query)) {
    $search_safe = $conn->real_escape_string($search_query);
    $sql .= " AND (name LIKE '%$search_safe%' OR location LIKE '%$search_safe%' OR device_ip LIKE '%$search_safe%' OR notes LIKE '%$search_safe%')";
}

$sql .= " ORDER BY name ASC";
$result = $conn->query($sql);
$customers = [];
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

?>
