@extends('layouts.app')

@section('title', 'PPPoE Profiles')
@section('header_title', 'PPPoE Profiles')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{
    search: '',
    showModal: false,
    viewModalOpen: false,
    editModalOpen: false,
    viewData: {},
    editData: {},
    profiles: {{ json_encode($profiles) }},
    get filteredProfiles() {
        if (this.search === '') return this.profiles;
        const q = this.search.toLowerCase();
        return this.profiles.filter(p => 
            (p.name && p.name.toLowerCase().includes(q))
        );
    }
}">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">PPPoE Profiles</h2>
            <p class="text-sm text-gray-500">Total Profiles: <span class="font-bold text-blue-600" x-text="profiles.length"></span></p>
        </div>
        <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
             <div class="relative w-full md:w-64">
                <input type="text" x-model="search" placeholder="Cari Profile..." 
                       class="w-full pl-10 pr-4 py-2 border rounded shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
            </div>
            <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition text-sm flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i>Add Profile
            </button>
        </div>
    </div>

    @if ($routerStatus == 'Connected')
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-blue-600">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal text-[11px]">
                <thead>
                    <tr class="bg-gray-100 uppercase text-gray-600 border-b text-[10px]">
                        <th class="px-4 py-2 text-left font-bold">Name</th>
                        <th class="px-4 py-2 text-left font-bold">Local Address</th>
                        <th class="px-4 py-2 text-left font-bold">Remote Address</th>
                        <th class="px-4 py-2 text-left font-bold">Rate Limit</th>
                        <th class="px-4 py-2 text-left font-bold">DNS Server</th>
                        <th class="px-4 py-2 text-left font-bold">Comment</th>
                        <th class="px-4 py-2 text-center font-bold w-24">Act</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="p in filteredProfiles" :key="p['.id']">
                        <tr class="hover:bg-blue-50 transition border-b border-gray-100 whitespace-nowrap">
                             <td class="px-4 py-2 font-bold text-gray-800" x-text="p.name"></td>
                             <td class="px-4 py-2 text-gray-600 font-mono" x-text="p['local-address'] || '-'"></td>
                             <td class="px-4 py-2 text-gray-600 font-mono" x-text="p['remote-address'] || '-'"></td>
                             <td class="px-4 py-2 text-blue-600 font-mono" x-text="p['rate-limit'] || '-'"></td>
                             <td class="px-4 py-2 text-gray-600 font-mono" x-text="p['dns-server'] || '-'"></td>
                             <td class="px-4 py-2 text-gray-500 italic text-xs" x-text="p.comment || '-'"></td>
                             <td class="px-4 py-2 text-center">
                                <button @click="viewData = p; viewModalOpen = true" class="text-blue-500 hover:text-blue-700 mx-1" title="View Details">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <button @click="editData = p; editModalOpen = true" class="text-yellow-500 hover:text-yellow-700 mx-1" title="Edit Profile">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <a :href="'{{ route('pppoe.deleteProfile', ['id' => 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', encodeURIComponent(p['.id']))" 
                                   onclick="return confirm('Delete this profile?')"
                                   class="text-red-500 hover:text-red-700 mx-1 font-bold transition hover:scale-110 inline-block" 
                                   title="Delete Profile">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </a>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredProfiles.length === 0">
                        <td colspan="7" class="px-5 py-12 bg-white text-center text-gray-400 italic font-bold">
                            Tidak ada profile ditemukan.
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

    <!-- Add Modal -->
    <template x-teleport="body">
        <div x-show="showModal" x-cloak class="fixed inset-0 z-[1000] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showModal = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
                <div x-show="showModal" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 -translate-y-12" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-12" class="relative inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full z-[1001] border border-slate-100">
                    <form action="{{ route('pppoe.storeProfile') }}" method="POST">
                        @csrf
                        <div class="bg-white px-8 pt-8 pb-6">
                            <div class="flex justify-between items-center mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-layer-group text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-800 tracking-tight">New Profile</h3>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Profile Configuration</p>
                                    </div>
                                </div>
                                <button type="button" @click="showModal = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Profile Name</label>
                                    <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all placeholder:text-slate-300" placeholder="e.g. Profile_5Mbps">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Local Address</label>
                                        <input type="text" name="local_address" placeholder="10.0.0.1" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-mono font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Remote Address</label>
                                        <input type="text" name="remote_address" placeholder="PoolName" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Rate Limit</label>
                                        <input type="text" name="rate_limit" placeholder="1M/2M" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-mono font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">DNS Server</label>
                                        <input type="text" name="dns_server" placeholder="8.8.8.8" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-mono font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-blue-600 px-8 py-3.5 text-sm font-black text-white hover:bg-blue-700 shadow-xl shadow-blue-500/20 active:scale-95 transition-all">
                                <i class="fas fa-plus"></i> <span>Create Profile</span>
                            </button>
                            <button type="button" @click="showModal = false" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-black text-slate-500 hover:text-slate-800 border border-slate-200 hover:border-slate-300 transition-all shadow-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
    
    <!-- View Modal -->
    <template x-teleport="body">
        <div x-show="viewModalOpen" x-cloak class="fixed inset-0 z-[1000] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div x-show="viewModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="viewModalOpen = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
                <div x-show="viewModalOpen" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 -translate-y-12" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-12" class="relative inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full z-[1001] border border-slate-100">
                    <div class="bg-white px-8 pt-8 pb-6">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                                    <i class="fas fa-info-circle text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-800 tracking-tight">Profile View</h3>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Configuration Details</p>
                                </div>
                            </div>
                            <button type="button" @click="viewModalOpen = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 space-y-4 font-bold text-sm">
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Name</span>
                                <span class="text-blue-600 font-mono" x-text="viewData.name"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Local Address</span>
                                <span class="text-slate-700 font-mono" x-text="viewData['local-address'] || '-'"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Remote Address</span>
                                <span class="text-slate-700 font-mono" x-text="viewData['remote-address'] || '-'"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rate Limit</span>
                                <span class="text-blue-600 font-mono" x-text="viewData['rate-limit'] || '-'"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">DNS Server</span>
                                <span class="text-slate-700 font-mono" x-text="viewData['dns-server'] || '-'"></span>
                            </div>
                            <div class="flex flex-col gap-1 pt-1" x-show="viewData.comment">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Comment</span>
                                <span class="text-xs text-slate-500 italic" x-text="viewData.comment"></span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse">
                        <button @click="viewModalOpen = false" type="button" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-black text-slate-500 hover:text-slate-800 border border-slate-200 hover:border-slate-300 transition-all shadow-sm">
                            Close Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
    
    <!-- Edit Modal -->
    <template x-teleport="body">
        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-[1000] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="editModalOpen = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
                <div x-show="editModalOpen" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 -translate-y-12" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-12" class="relative inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full z-[1001] border border-slate-100">
                    <form action="{{ route('pppoe.updateProfile') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" x-model="editData['.id']">
                        <div class="bg-white px-8 pt-8 pb-6">
                            <div class="flex justify-between items-center mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-edit text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Edit Profile</h3>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Update Configuration</p>
                                    </div>
                                </div>
                                <button type="button" @click="editModalOpen = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Profile Name</label>
                                    <input type="text" name="name" x-model="editData.name" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-yellow-500/10 focus:border-yellow-500 focus:outline-none transition-all">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Local Address</label>
                                        <input type="text" name="local_address" x-model="editData['local-address']" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-mono font-bold text-slate-800 focus:ring-4 focus:ring-yellow-500/10 focus:border-yellow-500 focus:outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Remote Address</label>
                                        <input type="text" name="remote_address" x-model="editData['remote-address']" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-yellow-500/10 focus:border-yellow-500 focus:outline-none transition-all">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Rate Limit</label>
                                        <input type="text" name="rate_limit" x-model="editData['rate-limit']" placeholder="1M/2M" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-mono font-bold text-slate-800 focus:ring-4 focus:ring-yellow-500/10 focus:border-yellow-500 focus:outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">DNS Server</label>
                                        <input type="text" name="dns_server" x-model="editData['dns-server']" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-mono font-bold text-slate-800 focus:ring-4 focus:ring-yellow-500/10 focus:border-yellow-500 focus:outline-none transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Comment</label>
                                    <input type="text" name="comment" x-model="editData.comment" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-yellow-500/10 focus:border-yellow-500 focus:outline-none transition-all">
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-blue-600 px-8 py-3.5 text-sm font-black text-white hover:bg-blue-700 shadow-xl shadow-blue-500/20 active:scale-95 transition-all">
                                Update Data
                            </button>
                            <button type="button" @click="editModalOpen = false" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-black text-slate-500 hover:text-slate-800 border border-slate-200 hover:border-slate-300 transition-all shadow-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
