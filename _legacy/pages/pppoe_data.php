<?php
// pages/pppoe_data.php
?>
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Data Pelanggan PPPoE</h2>
        <button onclick="alert('Feature coming soon: Link local data with MikroTik')" class="bg-secondary hover:bg-teal-600 text-white font-bold py-2 px-4 rounded shadow transition text-sm">
            <i class="fas fa-plus mr-2"></i>Tambah Data
        </button>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Username</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Profile</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Remote IP</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($pppoe_customers->num_rows > 0): ?>
                    <?php while($row = $pppoe_customers->fetch_assoc()): ?>
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold font-mono"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($row['profile'] ?? '-'); ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($row['remote_address'] ?? '-'); ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($row['comment'] ?? '-'); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="px-5 py-5 border-b border-gray-200 bg-white text-center">No local customer data.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
