<?php
// pages/voucher_profiles.php
?>
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">User Profiles</h2>
        <div class="flex space-x-2">
            <form method="POST">
                <button type="submit" name="sync_profiles" class="bg-secondary hover:bg-teal-600 text-white font-bold py-2 px-4 rounded shadow transition text-sm">
                    <i class="fas fa-sync mr-2"></i>Sync Profiles
                </button>
            </form>
            <button onclick="document.getElementById('addProfileModal').classList.remove('hidden')" class="bg-primary hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow transition text-sm">
                <i class="fas fa-plus mr-2"></i>Tambah Paket
            </button>
        </div>
    </div>
    
    <?php if ($router_connected): ?>
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-secondary">
        <table class="min-w-full leading-normal text-xs">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 border-b border-gray-200 text-left font-bold text-gray-700">#</th>
                    <th class="px-4 py-3 border-b border-gray-200 text-left font-bold text-gray-700">Name</th>
                    <th class="px-4 py-3 border-b border-gray-200 text-left font-bold text-gray-700">Shared</th>
                    <th class="px-4 py-3 border-b border-gray-200 text-left font-bold text-gray-700">Rate Limit</th>
                    <th class="px-4 py-3 border-b border-gray-200 text-left font-bold text-gray-700">Validity</th>
                    <th class="px-4 py-3 border-b border-gray-200 text-left font-bold text-gray-700">Price</th>
                    <th class="px-4 py-3 border-b border-gray-200 text-left font-bold text-gray-700">Selling Price</th>
                    <th class="px-4 py-3 border-b border-gray-200 text-center font-bold text-gray-700">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($profiles as $prof): 
                    $meta = $prof['local_metadata'];
                    $validity = $meta['validity'] ?? '-';
                    $price = $meta['price'] ?? 0;
                    $sell_price = $meta['selling_price'] ?? 0;
                ?>
                <tr class="hover:bg-gray-50 transition border-b">
                    <td class="px-4 py-3 font-medium text-gray-800"><?php echo $prof['name']; ?></td>
                    <td class="px-4 py-3 font-mono text-gray-600"><?php echo $prof['shared-users'] ?? '1'; ?></td>
                    <td class="px-4 py-3 font-mono text-gray-600"><?php echo $prof['rate-limit'] ?? '-'; ?></td>
                    <td class="px-4 py-3 text-orange-600 font-bold"><?php echo $validity; ?></td>
                    <td class="px-4 py-3 text-green-600 font-bold"><?php echo format_currency($price); ?></td>
                    <td class="px-4 py-3 text-primary font-bold"><?php echo format_currency($sell_price); ?></td>
                    <td class="px-4 py-3">
                        <div class="flex space-x-2">
                            <a href="index.php?page=voucher_profiles&del=<?php echo $prof['.id']; ?>&name=<?php echo urlencode($prof['name']); ?>" 
                               onclick="return confirm('Hapus profil ini?')"
                               class="bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1 rounded text-xs transition border border-red-200">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
        <p class="font-bold">Error</p>
        <p>Could not connect to MikroTik. Please check your settings.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Add Profile Modal -->
<div id="addProfileModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-[450px] shadow-2xl rounded-lg bg-white">
        <div class="mt-2">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-xl font-bold text-gray-800">Tambah Profil Baru</h3>
                <button onclick="document.getElementById('addProfileModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Profil</label>
                        <input type="text" name="name" required class="w-full border rounded px-3 py-2 text-sm focus:ring-primary focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Shared Users</label>
                        <input type="number" name="shared_users" value="1" class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Rate Limit (up/down)</label>
                    <input type="text" name="rate_limit" placeholder="e.g. 1M/1M" class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Validity</label>
                        <input type="text" name="validity" placeholder="e.g. 1d" class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Price</label>
                        <input type="number" name="price" placeholder="5000" class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Selling Price</label>
                        <input type="number" name="sell_price" placeholder="6000" class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t">
                    <button type="submit" name="save_profile" class="w-full bg-primary hover:bg-red-600 text-white font-bold py-2 rounded transition shadow-md">
                        <i class="fas fa-save mr-2"></i>Simpan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
