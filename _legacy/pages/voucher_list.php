<?php
// pages/voucher_list.php
?>
<div class="max-w-6xl mx-auto" x-data="{ 
    search: '', 
    users: <?php echo htmlspecialchars(json_encode($users), ENT_QUOTES, 'UTF-8'); ?>,
    get filteredUsers() {
        return this.users.filter(u => 
            (u.name && u.name.toLowerCase().includes(this.search.toLowerCase())) || 
            (u.profile && u.profile.toLowerCase().includes(this.search.toLowerCase())) ||
            (u.comment && u.comment.toLowerCase().includes(this.search.toLowerCase()))
        );
    }
}">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Voucher (Hotspot Users)</h2>
            <p class="text-sm text-gray-500">Total: <span class="font-bold text-primary" x-text="users.length"></span> User</p>
        </div>
        <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
            <div class="relative">
                <input type="text" x-model="search" placeholder="Cari voucher..." 
                       class="w-full md:w-64 pl-10 pr-4 py-2 border rounded shadow-sm focus:ring-2 focus:ring-secondary focus:outline-none text-sm">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
            </div>
            <form method="POST">
                <button type="submit" name="sync_users" class="w-full bg-secondary hover:bg-teal-600 text-white font-bold py-2 px-4 rounded shadow transition text-sm">
                    <i class="fas fa-sync mr-2"></i>Sync
                </button>
            </form>
        </div>
    </div>

    <?php if (!$router_connected): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
            <p class="font-bold">Koneksi Gagal</p>
            <p>Tidak dapat terhubung ke MikroTik. Silakan periksa pengaturan API di menu Settings.</p>
        </div>
    <?php else: ?>
        <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-secondary">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="px-4 py-3 text-left font-bold text-gray-600 uppercase">Username</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-600 uppercase">Password</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-600 uppercase">Profile</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-600 uppercase">Uptime</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-600 uppercase">Server</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-600 uppercase">Comment</th>
                            <th class="px-4 py-3 text-center font-bold text-gray-600 uppercase w-20">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="u in filteredUsers" :key="u['.id']">
                            <tr class="hover:bg-gray-50 transition border-b">
                                <td class="px-4 py-2 font-bold text-gray-800" x-text="u.name"></td>
                                <td class="px-4 py-2 font-mono text-gray-600" x-text="u.password || '-'"></td>
                                <td class="px-4 py-2">
                                    <span class="bg-teal-50 text-teal-700 px-2 py-0.5 rounded border border-teal-100 font-bold" x-text="u.profile"></span>
                                </td>
                                <td class="px-4 py-2 text-gray-600" x-text="u.uptime || '0s'"></td>
                                <td class="px-4 py-2 text-gray-500 font-mono" x-text="u.server || 'all'"></td>
                                <td class="px-4 py-2 text-[10px] text-gray-400 max-w-xs truncate" x-text="u.comment || ''"></td>
                                <td class="px-4 py-2 text-center">
                                    <a :href="'index.php?page=voucher_list&del=' + encodeURIComponent(u['.id'])" 
                                       onclick="return confirm('Hapus voucher ini dari MikroTik?')"
                                       class="text-red-500 hover:text-red-700 transition">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredUsers.length === 0">
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                                <i class="fas fa-search fa-3x mb-3 block opacity-20"></i>
                                Tidak ada data voucher ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
