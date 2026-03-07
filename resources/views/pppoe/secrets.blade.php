@extends('layouts.app')

@section('title', 'PPPoE Secrets')
@section('header_title', 'PPPoE Secrets')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{
    search: '',
    showModal: false,
    showEditModal: false,
    showViewModal: false,
    selectedSecret: {},
    secrets: {{ json_encode($secrets) }},
    get filteredSecrets() {
        if (this.search === '') return this.secrets;
        const q = this.search.toLowerCase();
        return this.secrets.filter(s => 
            (s.name && s.name.toLowerCase().includes(q)) || 
            (s.profile && s.profile.toLowerCase().includes(q)) ||
            (s.comment && s.comment.toLowerCase().includes(q))
        );
    },
    openEdit(s) {
        this.selectedSecret = Object.assign({}, s);
        // Rename some fields for the form
        this.selectedSecret.local_address = s['local-address'] || '';
        this.selectedSecret.remote_address = s['remote-address'] || '';
        this.showEditModal = true;
    },
    openView(s) {
        this.selectedSecret = s;
        this.showViewModal = true;
    }
}">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">PPPoE Secrets</h2>
            <p class="text-sm text-gray-500">Total Secrets: <span class="font-bold text-blue-600" x-text="secrets.length"></span></p>
        </div>
        <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
             <div class="relative w-full md:w-64">
                <input type="text" x-model="search" placeholder="Cari Secret..." 
                       class="w-full pl-10 pr-4 py-2 border rounded shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
            </div>
            <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition text-sm flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i>Add Secret
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
                        <th class="px-4 py-2 text-left font-bold">Password</th>
                        <th class="px-4 py-2 text-left font-bold">Profile</th>
                        <th class="px-4 py-2 text-left font-bold">Local Address</th>
                        <th class="px-4 py-2 text-left font-bold">Remote Address</th>
                        <th class="px-4 py-2 text-left font-bold">Last Logged Out</th>
                        <th class="px-4 py-2 text-center font-bold w-24">Act</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="s in filteredSecrets" :key="s['.id']">
                        <tr class="hover:bg-blue-50 transition border-b border-gray-100 whitespace-nowrap">
                             <td class="px-4 py-2 font-bold text-gray-800">
                                 <div x-text="s.name"></div>
                                 <div x-show="s.comment" class="text-[9px] text-gray-400 italic" x-text="s.comment"></div>
                             </td>
                             <td class="px-4 py-2 text-gray-600 font-mono" x-text="s.password"></td>
                             <td class="px-4 py-2">
                                 <span class="bg-green-100 text-green-800 text-[10px] px-2 py-0.5 rounded-full font-bold" x-text="s.profile"></span>
                             </td>
                             <td class="px-4 py-2 text-gray-600 font-mono" x-text="s['local-address'] || '-'"></td>
                             <td class="px-4 py-2 text-gray-600 font-mono" x-text="s['remote-address'] || '-'"></td>
                             <td class="px-4 py-2 text-gray-500 text-[10px]" x-text="s['last-logged-out'] || '-'"></td>
                             <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openView(s)" class="text-blue-500 hover:text-blue-700 transition" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button @click="openEdit(s)" class="text-yellow-500 hover:text-yellow-700 transition" title="Edit Secret">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a :href="'{{ route('pppoe.deleteSecret', ['id' => 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', encodeURIComponent(s['.id']))" 
                                       onclick="return confirm('Delete this secret?')"
                                       class="text-red-500 hover:text-red-700 font-bold transition" 
                                       title="Delete Secret">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredSecrets.length === 0">
                        <td colspan="7" class="px-5 py-12 bg-white text-center text-gray-400 italic font-bold">
                            Tidak ada secret ditemukan.
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

    <!-- Modal -->
    <template x-teleport="body">
        <div x-show="showModal" 
             x-cloak
             class="fixed inset-0 z-[1000] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <!-- Backdrop -->
                <div x-show="showModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="showModal = false" 
                     class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

                <!-- Modal Box -->
                <div x-show="showModal" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 -translate-y-12"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-12"
                     class="relative inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full z-[1001] border border-slate-100">
                    <form action="{{ route('pppoe.storeSecret') }}" method="POST">
                        @csrf
                        <div class="bg-white px-8 pt-8 pb-6">
                            <div class="flex justify-between items-center mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-key text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Add PPPoE Secret</h3>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">User Access Management</p>
                                    </div>
                                </div>
                                <button type="button" @click="showModal = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Username / Name</label>
                                    <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all placeholder:text-slate-300" placeholder="e.g. customer_01">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Password</label>
                                    <input type="text" name="password" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-mono font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all placeholder:text-slate-300" placeholder="••••••••">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Profile</label>
                                    <div class="relative">
                                        <select name="profile" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none appearance-none cursor-pointer transition-all">
                                            @foreach($profiles as $p)
                                                <option value="{{ $p['name'] }}">{{ $p['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute right-4 top-3.5 text-slate-400 pointer-events-none">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Local Address</label>
                                        <input type="text" name="local_address" placeholder="e.g. 10.0.0.1" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-mono font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all placeholder:text-slate-300">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Remote Address</label>
                                        <input type="text" name="remote_address" placeholder="e.g. 10.0.0.50" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-mono font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all placeholder:text-slate-300">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Comment</label>
                                    <input type="text" name="comment" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all placeholder:text-slate-300" placeholder="Additional notes...">
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-blue-600 px-8 py-3.5 text-sm font-black text-white hover:bg-blue-700 shadow-xl shadow-blue-500/20 active:scale-95 transition-all">
                                <i class="fas fa-plus"></i> <span>Tambah User</span>
                            </button>
                            <button type="button" @click="showModal = false" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-black text-slate-500 hover:text-slate-800 border border-slate-200 hover:border-slate-300 transition-all shadow-sm">
                                Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- Edit Modal -->
    <template x-teleport="body">
        <div x-show="showEditModal" 
             x-cloak
             class="fixed inset-0 z-[1000] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div x-show="showEditModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="showEditModal = false" 
                     class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

                <div x-show="showEditModal" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 -translate-y-12"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-12"
                     class="relative inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full z-[1001] border border-slate-100">
                    <form action="{{ route('pppoe.updateSecret') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" :value="selectedSecret['.id']">
                        <div class="bg-white px-8 pt-8 pb-6">
                            <div class="flex justify-between items-center mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-edit text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Edit PPPoE Secret</h3>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest" x-text="'ID: ' + selectedSecret['.id']"></p>
                                    </div>
                                </div>
                                <button type="button" @click="showEditModal = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Username / Name</label>
                                    <input type="text" name="name" x-model="selectedSecret.name" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all placeholder:text-slate-300">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Password</label>
                                    <input type="text" name="password" x-model="selectedSecret.password" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-mono font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all placeholder:text-slate-300">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Profile</label>
                                    <div class="relative">
                                        <select name="profile" x-model="selectedSecret.profile" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none appearance-none cursor-pointer transition-all">
                                            @foreach($profiles as $p)
                                                <option value="{{ $p['name'] }}">{{ $p['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute right-4 top-3.5 text-slate-400 pointer-events-none">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Local Address</label>
                                        <input type="text" name="local_address" x-model="selectedSecret.local_address" placeholder="e.g. 10.0.0.1" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-mono font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all placeholder:text-slate-300">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Remote Address</label>
                                        <input type="text" name="remote_address" x-model="selectedSecret.remote_address" placeholder="e.g. 10.0.0.50" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-mono font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all placeholder:text-slate-300">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Comment</label>
                                    <input type="text" name="comment" x-model="selectedSecret.comment" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none transition-all placeholder:text-slate-300" placeholder="Additional notes...">
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-yellow-500 px-8 py-3.5 text-sm font-black text-white hover:bg-yellow-600 shadow-xl shadow-yellow-500/20 active:scale-95 transition-all">
                                <i class="fas fa-save"></i> <span>Update Secret</span>
                            </button>
                            <button type="button" @click="showEditModal = false" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-black text-slate-500 hover:text-slate-800 border border-slate-200 hover:border-slate-300 transition-all shadow-sm">
                                Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- View Modal -->
    <template x-teleport="body">
        <div x-show="showViewModal" 
             x-cloak
             class="fixed inset-0 z-[1000] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div x-show="showViewModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="showViewModal = false" 
                     class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

                <div x-show="showViewModal" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 -translate-y-12"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="relative inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full z-[1001] border border-slate-100">
                    <div class="bg-white px-8 pt-8 pb-6">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                                    <i class="fas fa-eye text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-800 tracking-tight">Detail PPPoE Secret</h3>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest" x-text="'Router ID: ' + selectedSecret['.id']"></p>
                                </div>
                            </div>
                            <button type="button" @click="showViewModal = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Username</p>
                                    <p class="text-sm font-bold text-slate-800 bg-slate-50 p-3 rounded-xl border border-slate-100" x-text="selectedSecret.name"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Password</p>
                                    <p class="text-sm font-mono font-bold text-slate-800 bg-slate-50 p-3 rounded-xl border border-slate-100" x-text="selectedSecret.password"></p>
                                </div>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Service & Profile</p>
                                <div class="flex gap-2">
                                    <span class="px-3 py-2 bg-blue-100 text-blue-700 rounded-xl text-xs font-black uppercase tracking-wider" x-text="selectedSecret.service || 'pppoe'"></span>
                                    <span class="px-3 py-2 bg-green-100 text-green-700 rounded-xl text-xs font-black uppercase tracking-wider" x-text="selectedSecret.profile"></span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Local Address</p>
                                    <p class="text-sm font-mono font-bold text-slate-800 bg-slate-50 p-3 rounded-xl border border-slate-100" x-text="selectedSecret['local-address'] || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Remote Address</p>
                                    <p class="text-sm font-mono font-bold text-slate-800 bg-slate-50 p-3 rounded-xl border border-slate-100" x-text="selectedSecret['remote-address'] || '-'"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Uptime</p>
                                    <p class="text-sm font-bold text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100" x-text="selectedSecret.uptime || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Last Logged Out</p>
                                    <p class="text-sm font-bold text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100" x-text="selectedSecret['last-logged-out'] || '-'"></p>
                                </div>
                            </div>
                            <div x-show="selectedSecret.comment">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Komentar</p>
                                <p class="text-sm font-bold text-slate-800 bg-yellow-50/50 p-3 rounded-xl border border-yellow-100 italic" x-text="selectedSecret.comment"></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50/50 px-8 py-6 flex justify-end">
                        <button type="button" @click="showViewModal = false" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-black text-slate-500 hover:text-slate-800 border border-slate-200 hover:border-slate-300 transition-all shadow-sm">
                            Tutup Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
