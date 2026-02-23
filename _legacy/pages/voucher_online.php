<?php
// pages/voucher_online.php
?>
<script>
    window.hotspotData = <?php 
        $utf8ize = function($d) use (&$utf8ize) {
            if (is_array($d)) {
                foreach ($d as $k => $v) {
                    $d[$k] = $utf8ize($v);
                }
            } else if (is_string($d)) {
                return mb_convert_encoding($d, 'UTF-8', 'UTF-8');
            }
            return $d;
        };
        echo json_encode($utf8ize($active_users)) ?: '[]';
    ?>;
</script>

<div class="max-w-6xl mx-auto" x-data="{
    search: '',
    users: window.hotspotData || [],
    get filteredUsers() {
        if (this.search === '') return this.users;
        const q = this.search.toLowerCase();
        return this.users.filter(u => 
            (u.user && u.user.toLowerCase().includes(q)) || 
            (u.address && u.address.toLowerCase().includes(q)) ||
            (u['mac-address'] && u['mac-address'].toLowerCase().includes(q)) ||
            (u.profile && u.profile.toLowerCase().includes(q)) ||
            (u.comment && u.comment.toLowerCase().includes(q)) ||
            (u.server && u.server.toLowerCase().includes(q))
        );
    }
}">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Hotspot Active Users</h2>
            <p class="text-sm text-gray-500">Total Online: <span class="font-bold text-primary" x-text="users.length"></span> User</p>
        </div>
        <div class="relative w-full md:w-auto">
            <input type="text" x-model="search" placeholder="Cari user online..." 
                   class="w-full md:w-64 pl-10 pr-4 py-2 border rounded shadow-sm focus:ring-2 focus:ring-secondary focus:outline-none text-sm">
            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
        </div>
    </div>
    
    <?php if ($router_connected): ?>
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-secondary">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal text-[11px]">
                <thead>
                    <tr class="bg-gray-100 uppercase text-gray-600 border-b text-[10px]">
                        <th class="px-4 py-2 text-left font-bold w-10">#</th>
                        <th class="px-4 py-2 text-left font-bold">Server</th>
                        <th class="px-4 py-2 text-left font-bold">User</th>
                        <th class="px-4 py-2 text-left font-bold">Address</th>
                        <th class="px-4 py-2 text-right font-bold">Uptime</th>
                        <th class="px-4 py-2 text-right font-bold">Idle Time</th>
                        <th class="px-4 py-2 text-right font-bold">Rx / Tx</th>
                        <th class="px-4 py-2 text-center font-bold w-10">Act</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="(u, index) in filteredUsers" :key="u['.id']">
                        <tbody class="hover:bg-blue-50 transition border-b border-gray-100 group">
                            <!-- Comment Row -->
                            <tr x-show="u.comment">
                                <td colspan="8" class="px-4 pt-1 text-blue-800 font-mono text-[10px] italic">
                                    <span class="opacity-50 font-bold">;;;</span> <span x-text="u.comment"></span>
                                </td>
                            </tr>
                            <!-- Data Row -->
                            <tr class="text-[10px] whitespace-nowrap">
                                <td class="px-4 py-1 text-gray-500 font-mono" x-text="index + 1"></td>
                                <td class="px-4 py-1 text-gray-600 font-mono" x-text="u.server || '-'"></td>
                                <td class="px-4 py-1 font-bold text-secondary" x-text="u.user"></td>
                                <td class="px-4 py-1 text-blue-600 font-mono" x-text="u.address"></td>
                                <td class="px-4 py-1 font-semibold text-orange-600 font-mono text-right" x-text="u.uptime"></td>
                                <td class="px-4 py-1 text-gray-500 font-mono text-right" x-text="u['idle-time'] || '-'"></td>
                                <td class="px-4 py-1 text-gray-600 font-mono text-right">
                                    <span class="text-green-600" x-text="u['bytes-in_fmt'] || '0 B'"></span> / 
                                    <span class="text-blue-600" x-text="u['bytes-out_fmt'] || '0 B'"></span>
                                </td>
                                <td class="px-4 py-1 text-center">
                                    <a :href="'index.php?page=voucher_online&kick=' + encodeURIComponent(u['.id'])" 
                                       onclick="return confirm('Disconnect this user?')"
                                       class="text-red-500 hover:text-red-700 font-bold transition" title="Kick User">
                                        <i class="fas fa-power-off"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </template>
                    <tr x-show="filteredUsers.length === 0">
                        <td colspan="8" class="px-5 py-20 bg-white text-center text-gray-400 italic font-bold">
                            Tidak ada user online yang sesuai.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
        <p class="font-bold">Error</p>
        <p>Could not connect to MikroTik. Please check your API settings.</p>
    </div>
    <?php endif; ?>
</div>
