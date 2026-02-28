@extends('layouts.app')

@section('title', 'PPPoE Active')
@section('header_title', 'PPPoE Active')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{
    search: '',
    users: {{ json_encode($active) }},
    viewModalOpen: false,
    viewData: {},
    get filteredUsers() {
        if (this.search === '') return this.users;
        const q = this.search.toLowerCase();
        return this.users.filter(u => 
            (u.name && u.name.toLowerCase().includes(q)) || 
            (u.address && u.address.toLowerCase().includes(q)) ||
            (u['caller-id'] && u['caller-id'].toLowerCase().includes(q)) ||
            (u.service && u.service.toLowerCase().includes(q)) ||
            (u.profile && u.profile.toLowerCase().includes(q))
        );
    },
    init() {
        if (typeof Echo !== 'undefined') {
            Echo.channel('pppoe-monitoring.{{ auth()->id() }}')
                .listen('PppoeSessionConnected', (e) => {
                    if (!this.users.find(u => u.name === e.session.name)) {
                        this.users.unshift(e.session);
                    }
                })
                .listen('PppoeSessionDisconnected', (e) => {
                    this.users = this.users.filter(u => u.name !== e.session.name);
                });
        }
    }
}">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">PPPoE Active Sessions</h2>
            <p class="text-sm text-gray-500">Total Active: <span class="font-bold text-blue-600" x-text="users.length"></span></p>
        </div>
        <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
             <div class="relative w-full md:w-64">
                <input type="text" x-model="search" placeholder="Cari (User, IP, Mac)..." 
                       class="w-full pl-10 pr-4 py-2 border rounded shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
            </div>
            <a href="{{ route('pppoe.active') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition text-sm flex items-center justify-center">
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
                        <th class="px-4 py-2 text-left font-bold">User</th>
                        <th class="px-4 py-2 text-left font-bold">Profile</th>
                        <th class="px-4 py-2 text-left font-bold">Service</th>
                        <th class="px-4 py-2 text-left font-bold">IP Address</th>
                        <th class="px-4 py-2 text-left font-bold">Mac Address</th>
                        <th class="px-4 py-2 text-right font-bold">Uptime</th>
                        <th class="px-4 py-2 text-left font-bold">Comment</th>
                        <th class="px-4 py-2 text-center font-bold w-20">Act</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="(u, index) in filteredUsers" :key="u['.id']">
                        <tr class="hover:bg-blue-50 transition border-b border-gray-100 whitespace-nowrap">
                             <td class="px-4 py-2 text-gray-500 font-mono" x-text="index + 1"></td>
                             <td class="px-4 py-2 font-bold text-blue-600" x-text="u.name"></td>
                             <td class="px-4 py-2">
                                <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-700 font-bold" x-text="u.profile || '-'"></span>
                             </td>
                             <td class="px-4 py-2 text-gray-600" x-text="u.service"></td>
                             <td class="px-4 py-2 text-gray-600 font-mono" x-text="u.address"></td>
                             <td class="px-4 py-2 text-purple-600 font-mono" x-text="u['caller-id']"></td>
                             <td class="px-4 py-2 font-semibold text-green-600 font-mono text-right" x-text="u.uptime"></td>
                             <td class="px-4 py-2 text-gray-500 italic text-xs" x-text="u.comment || '-'"></td>
                             <td class="px-4 py-2 text-center">
                                <button @click="viewData = u; viewModalOpen = true" class="text-blue-500 hover:text-blue-700 mx-1" title="View Details">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <a :href="'{{ route('pppoe.active.kick', ['id' => 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', encodeURIComponent(u['.id']))" 
                                   onclick="return confirm('Disconnect this session?')"
                                   class="text-red-500 hover:text-red-700 mx-1 font-bold transition hover:scale-110 inline-block" 
                                   title="Remove Session">
                                    <i class="fas fa-sign-out-alt text-sm"></i>
                                </a>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredUsers.length === 0">
                        <td colspan="8" class="px-5 py-12 bg-white text-center text-gray-400 italic font-bold">
                             <i class="fas fa-users-slash fa-3x mb-3 text-gray-200"></i><br>
                            Tidak ada active session.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @else
     <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
        <p class="font-bold">Error</p>
        <p>Could not connect to MikroTik. {{ $routerStatus }}</p>
    </div>
    @endif
    
    <!-- View Modal -->
    <template x-teleport="body">
        <div x-show="viewModalOpen" 
             x-cloak
             class="fixed inset-0 z-[1000] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <!-- Backdrop -->
                <div x-show="viewModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="viewModalOpen = false" 
                     class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

                <!-- Modal Box -->
                <div x-show="viewModalOpen" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 -translate-y-12"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-12"
                     class="relative inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full z-[1001] border border-slate-100">
                    
                    <div class="bg-white px-8 pt-8 pb-6">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                                    <i class="fas fa-info-circle text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-800 tracking-tight">Session Details</h3>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">PPPoE Monitoring System</p>
                                </div>
                            </div>
                            <button type="button" @click="viewModalOpen = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 space-y-4">
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Username</span>
                                <span class="text-sm font-black text-blue-600 font-mono" x-text="viewData.name"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Profile</span>
                                <span class="text-sm font-black text-purple-600" x-text="viewData.profile || '-'"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Service</span>
                                <span class="text-sm font-bold text-slate-700" x-text="viewData.service || '-'"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">IP Address</span>
                                <span class="text-sm font-black text-slate-700 font-mono" x-text="viewData.address"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">MAC / Caller ID</span>
                                <span class="text-sm font-black text-purple-600 font-mono" x-text="viewData['caller-id'] || '-'"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Uptime</span>
                                <span class="text-sm font-black text-emerald-600 font-mono" x-text="viewData.uptime || '0s'"></span>
                            </div>
                            <div class="flex flex-col gap-1 pt-1" x-show="viewData.comment">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Comment</span>
                                <span class="text-xs font-bold text-slate-500 italic" x-text="viewData.comment"></span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse">
                        <button @click="viewModalOpen = false" type="button" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-black text-slate-500 hover:text-slate-800 border border-slate-200 hover:border-slate-300 transition-all shadow-sm">
                            Tutup Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
