<?php
// pages/customer_form.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$customer = [];
if ($id > 0) {
    $result = $conn->query("SELECT * FROM customer_members WHERE id = $id");
    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
    }
}

// Handle Form Submission
if (isset($_POST['save_customer'])) {
    $type = $_POST['type'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $coordinates = $_POST['coordinates'];
    $bill_amount = $_POST['bill_amount'];
    $notes = $_POST['notes'];
    
    // ... add other fields as needed ...

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE customer_members SET type=?, name=?, location=?, coordinates=?, bill_amount=?, notes=? WHERE id=?");
        $stmt->bind_param("ssssdsi", $type, $name, $location, $coordinates, $bill_amount, $notes, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO customer_members (type, name, location, coordinates, bill_amount, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssds", $type, $name, $location, $coordinates, $bill_amount, $notes);
    }

    if ($stmt->execute()) {
        set_flash_message('success', "Customer saved successfully.");
        header("Location: index.php?page=customer_list&type=" . $type);
        exit;
    } else {
        $error = "Error saving customer: " . $conn->error;
    }
}
?>

<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4"><?php echo $id > 0 ? 'Edit Pelanggan' : 'Tambah Pelanggan Baru'; ?></h2>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-700">Jenis Pelanggan</label>
                <select name="type" class="mt-1 block w-full border rounded p-2">
                    <option value="MEMBER" <?php echo ($customer['type'] ?? '') == 'MEMBER' ? 'selected' : ''; ?>>Member (Hotspot)</option>
                    <option value="PERUMAHAN" <?php echo ($customer['type'] ?? '') == 'PERUMAHAN' ? 'selected' : ''; ?>>Perumahan (PPPoE)</option>
                    <option value="RESELLER" <?php echo ($customer['type'] ?? '') == 'RESELLER' ? 'selected' : ''; ?>>Reseller</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700">Nama</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($customer['name'] ?? ''); ?>" required class="mt-1 block w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Lokasi / Alamat</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($customer['location'] ?? ''); ?>" class="mt-1 block w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Koordinat</label>
                <div class="flex gap-2">
                    <input type="text" name="coordinates" id="coordinates" value="<?php echo htmlspecialchars($customer['coordinates'] ?? ''); ?>" class="mt-1 block w-full border rounded p-2 bg-gray-50" readonly>
                    <button type="button" onclick="getLocation()" class="mt-1 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        <i class="fas fa-map-marker-alt mr-1"></i> Ambil Lokasi
                    </button>
                </div>
                <p id="geo-status" class="text-xs text-gray-500 mt-1"></p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Tagihan Bulanan (Rp)</label>
                <input type="number" name="bill_amount" value="<?php echo htmlspecialchars($customer['bill_amount'] ?? 0); ?>" class="mt-1 block w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Catatan / No HP</label>
                <textarea name="notes" class="mt-1 block w-full border rounded p-2"><?php echo htmlspecialchars($customer['notes'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <a href="index.php?page=customer_list" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
            <button type="submit" name="save_customer" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
        </div>
    </form>
</div>

<script>
function getLocation() {
    const status = document.getElementById('geo-status');
    const input = document.getElementById('coordinates');
    
    if (!navigator.geolocation) {
        status.textContent = "Geolocation is not supported by your browser";
        return;
    }

    status.textContent = "Locating...";
    
    navigator.geolocation.getCurrentPosition(
        (position) => {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            input.value = `${latitude}, ${longitude}`;
            status.textContent = "Location acquired!";
            status.className = "text-xs text-green-600 mt-1";
        },
        (error) => {
            status.textContent = "Unable to retrieve your location: " + error.message;
            status.className = "text-xs text-red-600 mt-1";
        }
    );
}
</script>
