@extends('layouts.app')

@section('title', 'Daftar Voucher')
@section('header_title', 'Daftar Voucher')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ 
    search: '', 
    users: {{ json_encode($users) }},
    sortKey: 'name',
    sortOrder: 'asc',
    viewModalOpen: false,
    editModalOpen: false,
    viewUser: {},
    editUser: {},
    profiles: {{ json_encode($profiles ?? []) }},
    
    // Pagination Logic
    page: 1,
    perPage: 15,
    get totalPages() {
        return Math.ceil(this.filteredUsers.length / this.perPage);
    },
    get paginatedUsers() {
        let start = (this.page - 1) * this.perPage;
        return this.filteredUsers.slice(start, start + this.perPage);
    },

    toggleSort(key) {
        if (this.sortKey === key) {
            this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortKey = key;
            this.sortOrder = 'asc';
        }
        this.page = 1; // Reset to page 1 on sort
    },
    get filteredUsers() {
        let filtered = this.users.filter(u => 
            (u.name && u.name.toLowerCase().includes(this.search.toLowerCase())) || 
            (u.profile && u.profile.toLowerCase().includes(this.search.toLowerCase())) ||
            (u.comment && u.comment.toLowerCase().includes(this.search.toLowerCase()))
        );

        return filtered.sort((a, b) => {
            let valA = a[this.sortKey] || '';
            let valB = b[this.sortKey] || '';
            
            if (typeof valA === 'string') valA = valA.toLowerCase();
            if (typeof valB === 'string') valB = valB.toLowerCase();

            if (valA < valB) return this.sortOrder === 'asc' ? -1 : 1;
            if (valA > valB) return this.sortOrder === 'asc' ? 1 : -1;
            return 0;
        });
    }
}">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6">
        <div class="flex items-center gap-4">
            <div class="w-1.5 h-10 bg-blue-600 rounded-full"></div>
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Voucher Realtime</h2>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                    MikroTik Hotspot Users: <span class="text-blue-600" x-text="users.length"></span> Total
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative flex-grow md:w-80 group">
                <input type="text" x-model="search" placeholder="Cari username, profile, atau comment..." 
                       class="w-full pl-12 pr-4 py-3 bg-white border border-slate-300 rounded-2xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm transition-all group-hover:border-blue-400">
                <i class="fas fa-search absolute left-4 top-3.5 text-slate-400 group-hover:text-blue-500 transition-colors"></i>
            </div>
            <a href="{{ route('voucher.list') }}" class="bg-blue-600 hover:bg-blue-700 text-white p-3.5 rounded-2xl shadow-md transition-all active:scale-95 group" title="Sync Manual">
                <i class="fas fa-sync group-hover:rotate-180 transition-transform duration-500"></i>
            </a>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white shadow-md rounded-2xl border border-slate-200 overflow-hidden mb-10">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-200">
                        <th @click="toggleSort('name')" class="px-8 py-4 text-left font-bold text-slate-600 uppercase text-[11px] tracking-wider cursor-pointer hover:bg-slate-200 transition-colors group/head">
                            <div class="flex items-center gap-2">
                                User Credentials
                                <i class="fas" :class="sortKey === 'name' ? (sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort text-slate-300'"></i>
                            </div>
                        </th>
                        <th @click="toggleSort('profile')" class="px-8 py-4 text-left font-bold text-slate-600 uppercase text-[11px] tracking-wider cursor-pointer hover:bg-slate-200 transition-colors group/head">
                            <div class="flex items-center gap-2">
                                Paket / Profile
                                <i class="fas" :class="sortKey === 'profile' ? (sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort text-slate-300'"></i>
                            </div>
                        </th>
                        <th class="px-8 py-4 text-left font-bold text-slate-600 uppercase text-[11px] tracking-wider">Limit Uptime</th>
                        <th class="px-8 py-4 text-left font-bold text-slate-600 uppercase text-[11px] tracking-wider">MikroTik Server</th>
                        <th @click="toggleSort('comment')" class="px-8 py-4 text-left font-bold text-slate-600 uppercase text-[11px] tracking-wider cursor-pointer hover:bg-slate-200 transition-colors group/head">
                            <div class="flex items-center gap-2">
                                Komentar
                                <i class="fas" :class="sortKey === 'comment' ? (sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort text-slate-300'"></i>
                            </div>
                        </th>
                        <th class="px-8 py-4 text-center font-bold text-slate-600 uppercase text-[11px] tracking-wider w-32">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <template x-for="u in paginatedUsers" :key="u['.id']">
                        <tr class="hover:bg-blue-50 transition group">
                            <td class="px-8 py-4">
                                <div class="font-bold text-slate-800 text-sm group-hover:text-blue-700 transition-colors" x-text="u.name"></div>
                                <div class="flex items-center gap-1.5 mt-1 border-t border-slate-100 pt-1">
                                    <span class="text-[9px] font-semibold text-slate-500 uppercase">PWD:</span>
                                    <span class="text-[10px] font-mono font-medium text-slate-700" x-text="u.password || 'none'"></span>
                                </div>
                            </td>
                            <td class="px-8 py-4">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-[10px] font-bold uppercase tracking-tight rounded-full border border-blue-200 shadow-sm" x-text="u.profile"></span>
                            </td>
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-hourglass-half text-xs text-slate-400"></i>
                                    <span class="text-xs font-medium text-slate-700" x-text="u['limit-uptime'] || '∞'"></span>
                                </div>
                            </td>
                            <td class="px-8 py-4 font-mono text-xs font-medium text-slate-600 uppercase tracking-tight" x-text="u.server || 'all'"></td>
                            <td class="px-8 py-4">
                                <span class="text-xs text-slate-500 italic block truncate max-w-[150px]" :title="u.comment" x-text="u.comment || '-'"></span>
                            </td>
                            <td class="px-8 py-4 text-center">
                                <div class="flex justify-center items-center gap-1">
                                    <!-- View Button -->
                                    <button @click="viewUser = u; viewModalOpen = true" 
                                            class="inline-flex items-center justify-center p-2 bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition shadow-sm" title="Lihat Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    
                                    <!-- Edit Button -->
                                    <button @click="editUser = { ...u }; editModalOpen = true" 
                                            class="inline-flex items-center justify-center p-2 bg-yellow-100 text-yellow-600 hover:bg-yellow-600 hover:text-white rounded-lg transition shadow-sm" title="Edit Voucher">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
 
                                    <!-- Delete Button -->
                                    <a :href="'{{ url('voucher/delete') }}/' + u['.id']" 
                                       onclick="return confirm('Hapus voucher ini dari MikroTik?')"
                                       class="inline-flex items-center justify-center p-2 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition shadow-sm" title="Delete Voucher">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="px-8 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between" x-show="totalPages > 1">
            <div class="text-xs font-bold text-slate-500">
                Halaman <span x-text="page"></span> dari <span x-text="totalPages"></span>
            </div>
            <div class="flex gap-2">
                <button @click="page--" :disabled="page <= 1" class="px-4 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    <i class="fas fa-chevron-left mr-1"></i> Prev
                </button>
                <button @click="page++" :disabled="page >= totalPages" class="px-4 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    Next <i class="fas fa-chevron-right ml-1"></i>
                </button>
            </div>
        </div>
        
        <!-- Empty State -->
        <div x-show="filteredUsers.length === 0" class="px-10 py-20 text-center">
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-ticket-alt text-3xl text-slate-400"></i>
                </div>
                <h4 class="font-bold text-slate-500 uppercase tracking-widest text-sm mb-2">No Vouchers Found</h4>
                <p class="text-xs text-slate-400">Gunakan pencarian lain atau sinkronkan ulang data dari MikroTik.</p>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div x-show="viewModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="viewModalOpen" @click="viewModalOpen = false" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="viewModalOpen" x-transition.scale class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-user-tag text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Detail Voucher</h3>
                            <div class="mt-4 space-y-3">
                                <div class="grid grid-cols-3 gap-2 text-sm border-b pb-2">
                                    <span class="font-bold text-gray-500">Username</span>
                                    <span class="col-span-2 font-mono" x-text="viewUser.name"></span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-sm border-b pb-2">
                                    <span class="font-bold text-gray-500">Password</span>
                                    <span class="col-span-2 font-mono" x-text="viewUser.password"></span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-sm border-b pb-2">
                                    <span class="font-bold text-gray-500">Profile</span>
                                    <span class="col-span-2" x-text="viewUser.profile"></span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-sm border-b pb-2">
                                    <span class="font-bold text-gray-500">Server</span>
                                    <span class="col-span-2 font-mono" x-text="viewUser.server || 'all'"></span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-sm border-b pb-2">
                                    <span class="font-bold text-gray-500">Uptime Limit</span>
                                    <span class="col-span-2" x-text="viewUser['limit-uptime'] || '∞'"></span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-sm border-b pb-2">
                                    <span class="font-bold text-gray-500">Comment</span>
                                    <span class="col-span-2 italic" x-text="viewUser.comment || '-'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="viewModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                    <!-- Login URL Helper -->
                    <!-- <a :href="'http://hotspot.mikhmon/login?username=' + viewUser.name + '&password=' + viewUser.password" target="_blank" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Test Login URL
                    </a> -->
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="editModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="editModalOpen" @click="editModalOpen = false" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="editModalOpen" x-transition.scale class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('voucher.updateUser') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" x-model="editUser['.id']">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-edit text-yellow-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Edit Voucher</h3>
                                <div class="mt-4 space-y-4">
                                    <!-- Name -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Username</label>
                                        <input type="text" name="name" x-model="editUser.name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                    </div>
                                    
                                    <!-- Password -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Password</label>
                                        <input type="text" name="password" x-model="editUser.password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                    </div>

                                    <!-- Profile -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Profile</label>
                                        <select name="profile" x-model="editUser.profile" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            <template x-for="p in profiles" :key="p['.id']">
                                                <option :value="p.name" x-text="p.name" :selected="p.name == editUser.profile"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <!-- Limit Uptime -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Limit Uptime</label>
                                        <input type="text" name="limit_uptime" x-model="editUser['limit-uptime']" placeholder="e.g. 1h, 0s (unlimited)" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>

                                    <!-- Comment -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Comment</label>
                                        <input type="text" name="comment" x-model="editUser.comment" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" @click="editModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection