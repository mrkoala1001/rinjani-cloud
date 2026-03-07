@extends('layouts.app')

@section('title', 'WAN-IP STATIC')
@section('header_title', 'WAN-IP STATIC (Simple Queues)')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{
    search: '',
    showModal: false,
    showEditModal: false,
    showViewModal: false,
    selectedQueue: {},
    queues: {{ json_encode($queues) }},
    sortTarget: true,
    currentPage: 1,
    perPage: 7,
    formatLimit(limit) {
        if (!limit) return '-';
        let num = parseInt(limit);
        if (isNaN(num)) return limit;
        if (num >= 1000000) return (num / 1000000) + 'M';
        if (num >= 1000) return (num / 1000) + 'k';
        return num;
    },
    formatTarget(target) {
        if (!target) return '-';
        return target.replace('/32', '');
    },
    get filteredQueues() {
        let qList = this.queues;
        if (this.search !== '') {
            const q = this.search.toLowerCase();
            qList = qList.filter(s => 
                (s.name && s.name.toLowerCase().includes(q)) || 
                (s.target && s.target.toLowerCase().includes(q)) ||
                (s.comment && s.comment.toLowerCase().includes(q))
            );
        }
        
        // Always sort by target if sortTarget is true
        if (this.sortTarget) {
            qList.sort((a, b) => {
                const ipA = (a.target || '').split('/')[0];
                const ipB = (b.target || '').split('/')[0];
                return ipA.localeCompare(ipB, undefined, { numeric: true });
            });
        }
        return qList;
    },
    get paginatedQueues() {
        // Reset to page 1 if search changes
        const start = (this.currentPage - 1) * this.perPage;
        const end = start + this.perPage;
        return this.filteredQueues.slice(start, end);
    },
    get totalPages() {
        return Math.ceil(this.filteredQueues.length / this.perPage);
    },
    get pages() {
        let pages = [];
        for (let i = 1; i <= this.totalPages; i++) pages.push(i);
        return pages;
    },
    openEdit(q) {
        this.selectedQueue = Object.assign({}, q);
        // Split max-limit if it exists
        if (q['max-limit']) {
            const limits = q['max-limit'].split('/');
            this.selectedQueue.max_limit_up = limits[0] || '';
            this.selectedQueue.max_limit_down = limits[1] || '';
        } else {
            this.selectedQueue.max_limit_up = '';
            this.selectedQueue.max_limit_down = '';
        }
        this.selectedQueue.id = q['.id'];
        this.showEditModal = true;
    },
    openView(q) {
        this.selectedQueue = q;
        this.showViewModal = true;
    }
}">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">WAN-IP STATIC</h2>
            <p class="text-sm text-gray-500">Total Queues: <span class="font-bold text-blue-600" x-text="queues.length"></span></p>
        </div>
        <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
             <div class="relative w-full md:w-64">
                <input type="text" x-model="search" placeholder="Cari Name/Target..." 
                       class="w-full pl-10 pr-4 py-2 border rounded shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
            </div>
            <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition text-sm flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i>Add Static IP
            </button>
        </div>
    </div>

    @if ($routerStatus == 'Connected')
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-blue-600">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal text-[11px]">
                <thead>
                    <tr class="bg-gray-100 uppercase text-gray-600 border-b text-[10px]">
                        <th class="px-4 py-3 text-left font-bold">Name</th>
                        <th class="px-4 py-3 text-left font-bold cursor-pointer hover:text-blue-600 transition" @click="sortTarget = !sortTarget">
                            Target <i class="fas" :class="sortTarget ? 'fa-sort-numeric-down text-blue-600' : 'fa-sort'"></i>
                        </th>
                        <th class="px-4 py-3 text-left font-bold">Upload Max</th>
                        <th class="px-4 py-3 text-left font-bold">Download Max</th>
                        <th class="px-4 py-3 text-center font-bold w-24">Act</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="q in paginatedQueues" :key="q['.id']">
                        <tr class="hover:bg-blue-50 transition border-b border-gray-100 whitespace-nowrap">
                             <td class="px-4 py-2 font-bold text-gray-800">
                                 <div x-text="q.name"></div>
                                 <div x-show="q.comment" class="text-[9px] text-gray-400 italic" x-text="q.comment"></div>
                             </td>
                             <td class="px-4 py-2 text-gray-600 font-mono font-bold" x-text="formatTarget(q.target)"></td>
                             <td class="px-4 py-2 text-rose-600 font-bold" x-text="formatLimit(q['max-limit'] ? q['max-limit'].split('/')[0] : '-')"></td>
                             <td class="px-4 py-2 text-green-600 font-bold" x-text="formatLimit(q['max-limit'] ? q['max-limit'].split('/')[1] : '-')"></td>
                             <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openView(q)" class="text-blue-500 hover:text-blue-700 transition" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button @click="openEdit(q)" class="text-yellow-500 hover:text-yellow-700 transition" title="Edit Queue">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a :href="'{{ route('wan-static.destroy', ['id' => 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', encodeURIComponent(q['.id']))" 
                                       onclick="return confirm('Hapus antrian IP statis ini?')"
                                       class="text-red-500 hover:text-red-700 font-bold transition" 
                                       title="Delete Queue">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredQueues.length === 0">
                        <td colspan="5" class="px-5 py-12 bg-white text-center text-gray-400 italic font-bold">
                            Tidak ada data antrian ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Controls -->
        <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center justify-between">
            <span class="text-xs xs:text-sm text-gray-900" x-show="filteredQueues.length > 0">
                Showing <span x-text="(currentPage - 1) * perPage + 1"></span> to 
                <span x-text="Math.min(currentPage * perPage, filteredQueues.length)"></span> of 
                <span x-text="filteredQueues.length"></span> Entries
            </span>
            <div class="inline-flex mt-2 xs:mt-0 gap-1">
                <button @click="if(currentPage > 1) currentPage--" 
                        :disabled="currentPage === 1"
                        :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-600 active:scale-95'"
                        class="text-sm bg-blue-500 text-white font-semibold py-2 px-4 rounded-l transition">
                    Prev
                </button>
                
                <div class="flex gap-1 overflow-x-auto max-w-[200px] no-scrollbar">
                    <template x-for="p in pages" :key="p">
                        <button @click="currentPage = p" 
                                :class="currentPage === p ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                class="text-sm font-semibold py-2 px-3 rounded transition"
                                x-text="p">
                        </button>
                    </template>
                </div>

                <button @click="if(currentPage < totalPages) currentPage++" 
                        :disabled="currentPage === totalPages"
                        :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-600 active:scale-95'"
                        class="text-sm bg-blue-500 text-white font-semibold py-2 px-4 rounded-r transition">
                    Next
                </button>
            </div>
        </div>
    </div>
    @else
     <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
        <p class="font-bold">Router Disconnected</p>
        <p>Could not connect to MikroTik. {{ $error ?? '' }}</p>
    </div>
    @endif

    <!-- Add Modal -->
    <template x-teleport="body">
        <div x-show="showModal" x-cloak class="fixed inset-0 z-[1000] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div @click="showModal = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
                <div x-show="showModal" class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100">
                    <form action="{{ route('wan-static.store') }}" method="POST">
                        @csrf
                        <div class="px-8 pt-8 pb-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                                    <i class="fas fa-network-wired text-lg"></i>
                                </div>
                                <h3 class="text-xl font-black text-slate-800">Add WAN-IP STATIC</h3>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-400 ml-1 mb-1.5">Name</label>
                                    <input type="text" name="name" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Bpk. Andi">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-400 ml-1 mb-1.5">Target IP (CIDR)</label>
                                    <input type="text" name="target" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500" placeholder="192.168.10.2">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-slate-400 ml-1 mb-1.5">Upload Max</label>
                                        <input type="text" name="max_limit_up" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500" placeholder="1M">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-slate-400 ml-1 mb-1.5">Download Max</label>
                                        <input type="text" name="max_limit_down" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500" placeholder="2M">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-400 ml-1 mb-1.5">Comment (Opsional)</label>
                                    <input type="text" name="comment" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500" placeholder="Keterangan tambahan">
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-8 rounded-2xl shadow-lg transition active:scale-95 text-sm uppercase tracking-wider">Simpan</button>
                            <button type="button" @click="showModal = false" class="w-full sm:w-auto bg-white border border-slate-200 text-slate-500 font-bold py-3 px-8 rounded-2xl hover:bg-slate-50 transition text-sm">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- Edit Modal -->
    <template x-teleport="body">
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[1000] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div @click="showEditModal = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
                <div x-show="showEditModal" class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100">
                    <form action="{{ route('wan-static.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" x-model="selectedQueue.id">
                        <div class="px-8 pt-8 pb-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center">
                                    <i class="fas fa-edit text-lg"></i>
                                </div>
                                <h3 class="text-xl font-black text-slate-800">Edit WAN-IP STATIC</h3>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-400 ml-1 mb-1.5">Name</label>
                                    <input type="text" name="name" x-model="selectedQueue.name" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-400 ml-1 mb-1.5">Target IP (CIDR)</label>
                                    <input type="text" name="target" x-model="selectedQueue.target" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-slate-400 ml-1 mb-1.5">Upload Max</label>
                                        <input type="text" name="max_limit_up" x-model="selectedQueue.max_limit_up" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-slate-400 ml-1 mb-1.5">Download Max</label>
                                        <input type="text" name="max_limit_down" x-model="selectedQueue.max_limit_down" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-400 ml-1 mb-1.5">Comment (Opsional)</label>
                                    <input type="text" name="comment" x-model="selectedQueue.comment" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-8 rounded-2xl shadow-lg transition active:scale-95 text-sm uppercase tracking-wider">Update</button>
                            <button type="button" @click="showEditModal = false" class="w-full sm:w-auto bg-white border border-slate-200 text-slate-500 font-bold py-3 px-8 rounded-2xl hover:bg-slate-50 transition text-sm">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- View Modal -->
    <template x-teleport="body">
        <div x-show="showViewModal" x-cloak class="fixed inset-0 z-[1000] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div @click="showViewModal = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
                <div x-show="showViewModal" class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100">
                    <div class="px-8 pt-8 pb-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-eye text-lg"></i>
                            </div>
                            <h3 class="text-xl font-black text-slate-800">Detail Queue</h3>
                        </div>
                        <div class="grid grid-cols-1 gap-6">
                            <div class="bg-slate-50 p-4 rounded-2xl">
                                <p class="text-[10px] font-black uppercase text-slate-400 mb-1">Queue Name</p>
                                <p class="text-lg font-black text-slate-800 leading-none" x-text="selectedQueue.name"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-4 rounded-2xl">
                                    <p class="text-[10px] font-black uppercase text-slate-400 mb-1">Target Address</p>
                                    <p class="text-sm font-bold text-slate-700" x-text="formatTarget(selectedQueue.target)"></p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl">
                                    <p class="text-[10px] font-black uppercase text-slate-400 mb-1">ID MikroTik</p>
                                    <p class="text-sm font-bold text-slate-700" x-text="selectedQueue['.id']"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-rose-50 p-4 rounded-2xl border border-rose-100">
                                    <p class="text-[10px] font-black uppercase text-rose-400 mb-1">Upload Max</p>
                                    <p class="text-lg font-black text-rose-600" x-text="formatLimit(selectedQueue['max-limit'] ? selectedQueue['max-limit'].split('/')[0] : '-')"></p>
                                </div>
                                <div class="bg-green-50 p-4 rounded-2xl border border-green-100">
                                    <p class="text-[10px] font-black uppercase text-green-400 mb-1">Download Max</p>
                                    <p class="text-lg font-black text-green-600" x-text="formatLimit(selectedQueue['max-limit'] ? selectedQueue['max-limit'].split('/')[1] : '-')"></p>
                                </div>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl" x-show="selectedQueue.comment">
                                <p class="text-[10px] font-black uppercase text-slate-400 mb-1">Comment</p>
                                <p class="text-sm font-bold text-slate-700 italic" x-text="selectedQueue.comment"></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-8 py-6 flex justify-end">
                        <button type="button" @click="showViewModal = false" class="bg-white border border-slate-200 text-slate-500 font-bold py-3 px-8 rounded-2xl hover:bg-slate-50 transition text-sm">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
