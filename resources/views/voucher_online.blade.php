@extends('layouts.app')

@section('title', 'Voucher Online')
@section('header_title', 'Voucher Online')

@section('content')
<script>
    window.hotspotData = {!! json_encode($activeUsers ?? [], JSON_UNESCAPED_UNICODE) ?: '[]' !!};
</script>

<div class="max-w-7xl mx-auto" x-data="{
    search: '',
    users: window.hotspotData || [],
    viewModalOpen: false,
    viewData: {},
    
    // Pagination
    page: 1,
    perPage: 25,
    get totalPages() {
        return Math.ceil(this.filteredUsers.length / this.perPage);
    },
    get paginatedUsers() {
        let start = (this.page - 1) * this.perPage;
        return this.filteredUsers.slice(start, start + this.perPage);
    },

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
    },
    init() {
        this.$watch('search', () => this.page = 1);
        if (typeof Echo !== 'undefined') {
            Echo.channel('hotspot-monitoring.{{ auth()->id() }}')
                .listen('HotspotDataUpdated', (e) => {
                    this.users = e.users;
                });
        }
    }
}">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            @php
                $user = auth()->user();
                $plan = $user->plan ?? 'basic';
                $isExpired = $plan !== 'basic' && (!$user->plan_expires_at || $user->plan_expires_at->isPast());
                $pConfig = \App\Helpers\PlanHelper::getPlanConfig($isExpired ? 'basic' : $plan);
                $maxOnline = $pConfig['quotas']['voucher_online_max'];
                $currentOnline = count($activeUsers ?? []);
                $onlineQuotaReached = ($maxOnline != -1 && $currentOnline >= $maxOnline);
            @endphp
            <h2 class="text-2xl font-bold text-gray-800">Hotspot Active Users</h2>
            <div class="flex items-center gap-2 mt-1">
                <p class="text-sm text-gray-500">Total Online: <span class="font-bold text-blue-600" x-text="users.length"></span> User</p>
                <span class="text-[9px] font-black px-1.5 py-0.5 rounded border {{ $onlineQuotaReached ? 'border-red-200 bg-red-50 text-red-600' : 'border-blue-200 bg-blue-50 text-blue-600' }}">
                    LIMIT PLAN: {{ $maxOnline == -1 ? 'UNLIMITED' : $maxOnline }}
                </span>
            </div>
            @if($onlineQuotaReached)
                <p class="text-[10px] text-red-500 font-bold mt-1 uppercase tracking-tighter">
                    <i class="fas fa-exclamation-circle mr-1"></i> Kuota online plan {{ strtoupper($pConfig['name']) }} sudah penuh!
                </p>
            @endif
        </div>
        <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <input type="text" x-model="search" placeholder="Cari user online..." 
                       class="w-full pl-10 pr-4 py-2 border rounded shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
            </div>
            <a href="{{ route('voucher.online') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition text-sm flex items-center justify-center">
                <i class="fas fa-sync mr-2"></i>Refresh
            </a>
        </div>
    </div>
    
    @if ($routerStatus == 'Connected')
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-blue-600">
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
                        <th class="px-4 py-2 text-center font-bold w-20">Act</th>
                    </tr>
                </thead>
                <!-- Outer TBODY removed to avoid nesting -->
                    <template x-for="(u, index) in paginatedUsers" :key="u['.id']">
                        <!-- SATU TBODY untuk setiap user -->
                        <tbody class="divide-y divide-gray-200 border-b border-gray-100" x-bind:key="u['.id'] + '-group'">
                            <!-- Comment Row - hanya tampil jika ada comment -->
                            <tr x-show="u.comment && u.comment.trim() !== ''" class="bg-blue-50/30 border-b border-gray-100">
                                <td colspan="8" class="px-4 py-1 text-blue-800 font-mono text-[10px] italic">
                                    <span class="opacity-50 font-bold">;;;</span> 
                                    <span x-text="u.comment"></span>
                                </td>
                            </tr>
                            <!-- Data Row -->
                            <tr class="hover:bg-blue-50 transition border-b border-gray-100 text-[10px] whitespace-nowrap">
                                <td class="px-4 py-2 text-gray-500 font-mono" x-text="((page - 1) * perPage) + index + 1"></td>
                                <td class="px-4 py-2 text-gray-600 font-mono" x-text="u.server || '-'"></td>
                                <td class="px-4 py-2 font-bold text-blue-600" x-text="u.user"></td>
                                <td class="px-4 py-2 text-gray-600 font-mono">
                                    <span x-text="u.address"></span>
                                    <span x-show="u['mac-address']" class="text-xs text-gray-400 block md:inline md:ml-1" x-text="'(' + u['mac-address'] + ')'"></span>
                                </td>
                                <td class="px-4 py-2 font-semibold text-orange-600 font-mono text-right" x-text="u.uptime || '0s'"></td>
                                <td class="px-4 py-2 text-gray-500 font-mono text-right" x-text="u['idle-time'] || '0s'"></td>
                                <td class="px-4 py-2 text-gray-600 font-mono text-right">
                                    <span class="text-green-600" x-text="formatBytes(u['bytes-in']) || '0 B'"></span> / 
                                    <span class="text-blue-600" x-text="formatBytes(u['bytes-out']) || '0 B'"></span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button @click="viewData = u; viewModalOpen = true" class="text-blue-500 hover:text-blue-700 mx-1" title="View Details">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    <a :href="'{{ route('voucher.kick') }}?id=' + encodeURIComponent(u['.id'])" 
                                       onclick="return confirm('Yakin ingin memutuskan user ini?')"
                                       class="text-red-500 hover:text-red-700 mx-1 font-bold transition hover:scale-110 inline-block" 
                                       title="Remove Session">
                                        <i class="fas fa-sign-out-alt text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </template>
            </table>
        </div>
        
        <!-- Pagination Controls -->
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between" x-show="totalPages > 1">
            <div class="text-xs font-bold text-gray-500">
                Halaman <span x-text="page"></span> dari <span x-text="totalPages"></span>
            </div>
            <div class="flex gap-2">
                <button @click="page--" :disabled="page <= 1" class="px-3 py-1 bg-white border border-gray-300 rounded text-xs font-bold text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    Prev
                </button>
                <button @click="page++" :disabled="page >= totalPages" class="px-3 py-1 bg-white border border-gray-300 rounded text-xs font-bold text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    Next
                </button>
            </div>
        </div>

        <!-- Empty State -->
        <template x-if="filteredUsers.length === 0">
            <div class="px-5 py-12 bg-white text-center">
                <div class="flex flex-col items-center justify-center">
                    <i class="fas fa-users-slash text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-400 italic font-medium">
                        <span x-show="search === ''">Tidak ada user online saat ini.</span>
                        <span x-show="search !== ''">Tidak ada user yang sesuai dengan pencarian.</span>
                    </p>
                </div>
            </div>
        </template>
            </table>
        </div>
    </div>
    @else
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle mr-3"></i>
            <div>
                <p class="font-bold">Koneksi Gagal</p>
                <p class="text-sm">Tidak dapat terhubung ke MikroTik. Silakan periksa pengaturan API.</p>
                <p class="text-xs text-gray-500 mt-2">{{ $routerStatus }}</p>
            </div>
        </div>
    </div>
    @endif
    
    <!-- View Modal -->
    <div x-show="viewModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="viewModalOpen" @click="viewModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Session Details</h3>
                    <div class="space-y-3 text-sm">
                        <div class="grid grid-cols-3 gap-2">
                            <span class="font-bold text-gray-600">User:</span>
                            <span class="col-span-2 text-blue-600 font-mono" x-text="viewData.user"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <span class="font-bold text-gray-600">Server:</span>
                            <span class="col-span-2 font-mono" x-text="viewData.server || '-'"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <span class="font-bold text-gray-600">IP Address:</span>
                            <span class="col-span-2 font-mono" x-text="viewData.address"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <span class="font-bold text-gray-600">MAC Address:</span>
                            <span class="col-span-2 font-mono text-purple-600" x-text="viewData['mac-address'] || '-'"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <span class="font-bold text-gray-600">Profile:</span>
                            <span class="col-span-2" x-text="viewData.profile || '-'"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <span class="font-bold text-gray-600">Uptime:</span>
                            <span class="col-span-2 text-orange-600 font-mono" x-text="viewData.uptime || '0s'"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <span class="font-bold text-gray-600">Idle Time:</span>
                            <span class="col-span-2 font-mono" x-text="viewData['idle-time'] || '0s'"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <span class="font-bold text-gray-600">Bytes In:</span>
                            <span class="col-span-2 text-green-600 font-mono" x-text="formatBytes(viewData['bytes-in'])"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <span class="font-bold text-gray-600">Bytes Out:</span>
                            <span class="col-span-2 text-blue-600 font-mono" x-text="formatBytes(viewData['bytes-out'])"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-2" x-show="viewData.comment">
                            <span class="font-bold text-gray-600">Comment:</span>
                            <span class="col-span-2 italic text-gray-700" x-text="viewData.comment"></span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="viewModalOpen = false" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk format bytes (Client-Side)
function formatBytes(bytes) {
    // Handle null, undefined, string
    if (bytes === null || bytes === undefined || bytes === '') return '0 B';
    
    // Convert to number
    const num = typeof bytes === 'string' ? parseInt(bytes, 10) : bytes;
    
    // Validate number
    if (isNaN(num) || num < 0) return '0 B';
    if (num === 0) return '0 B';
    
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(num) / Math.log(1024));
    
    return parseFloat((num / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];
}

document.addEventListener('alpine:init', () => {
    console.log('Hotspot Data:', window.hotspotData);
});
</script>

<style>
.font-mono {
    font-family: 'Courier New', Courier, monospace;
}
.bg-blue-50\/30 {
    background-color: rgba(239, 246, 255, 0.3);
}
</style>
@endsection
