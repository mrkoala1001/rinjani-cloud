@extends('layouts.app')

@section('title', 'Data Pelanggan')
@section('header_title', $title)

@section('content')
<div class="max-w-7xl mx-auto" x-data="{
    search: '{{ request('search') }}',
    showModal: false,
    editMode: false,
    viewMode: false,
    isLoading: false,
    customers: [],
    
    init() {
        this.customers = @js($customers->items());
    },

    formData: {
        id: '',
        type: '{{ key_exists($type, ['MEMBER' => 1, 'PERUMAHAN' => 1, 'RESELLER' => 1]) ? $type : 'MEMBER' }}',
        name: '',
        location: '',
        coordinates: '',
        bill_amount: 0,
        payment_date: '',
        installation_date: '',
        whatsapp: '',
        notes: '',
        device_name: '',
        device_ip: '',
        device_username: '',
        device_password: '',
        app_username: '',
        app_password: ''
    },
    openModal(data = null, viewOnly = false) {
        this.viewMode = viewOnly;
        if (data) {
            this.editMode = true;
            this.formData = { ...data };
        } else {
            this.editMode = false;
            this.viewMode = false;
            this.formData = {
                id: '',
                type: '{{ key_exists($type, ['MEMBER' => 1, 'PERUMAHAN' => 1, 'RESELLER' => 1]) ? $type : 'MEMBER' }}',
                name: '',
                location: '',
                coordinates: '',
                bill_amount: 0,
                payment_date: '',
                installation_date: '',
                whatsapp: '',
                notes: '',
                device_name: '',
                device_ip: '',
                device_username: '',
                device_password: '',
                app_username: '',
                app_password: ''
            };
        }
        this.showModal = true;
    },
    filterType(t) {
        window.location.href = '{{ route('customer.list') }}/' + t;
    },
    getLocation() {
        if (!navigator.geolocation) {
             alert('Geolocation is not supported by your browser');
             return;
        }
        navigator.geolocation.getCurrentPosition(
            (position) => {
                this.formData.coordinates = `${position.coords.latitude}, ${position.coords.longitude}`;
            },
            (error) => {
                alert('Unable to retrieve location: ' + error.message);
            }
        );
    }
}">
    <!-- Top Bar -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6">
        <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto pb-2 md:pb-0 sidebar-scroll">
            <button @click="filterType('all')" class="flex-shrink-0 px-5 py-2.5 text-xs font-bold rounded-xl transition-all {{ $type == 'all' ? 'bg-slate-900 text-white shadow-lg scale-105' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-200' }}">
                Semua
            </button>
            <button @click="filterType('MEMBER')" class="flex-shrink-0 px-5 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 {{ $type == 'MEMBER' ? 'bg-emerald-600 text-white shadow-lg scale-105' : 'bg-white text-emerald-600 hover:bg-emerald-50 border border-emerald-100' }}">
                <i class="fas fa-wifi"></i> Member
            </button>
            <button @click="filterType('PERUMAHAN')" class="flex-shrink-0 px-5 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 {{ $type == 'PERUMAHAN' ? 'bg-blue-600 text-white shadow-lg scale-105' : 'bg-white text-blue-600 hover:bg-blue-50 border border-blue-100' }}">
                <i class="fas fa-home"></i> Perumahan
            </button>
            <button @click="filterType('RESELLER')" class="flex-shrink-0 px-5 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 {{ $type == 'RESELLER' ? 'bg-purple-600 text-white shadow-lg scale-105' : 'bg-white text-purple-600 hover:bg-purple-50 border border-purple-100' }}">
                <i class="fas fa-user-tie"></i> Reseller
            </button>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
             <form action="" method="GET" class="relative flex-grow md:w-64 group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." 
                       class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-2xl shadow-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none text-sm transition-all group-hover:border-slate-300 font-bold">
                <i class="fas fa-search absolute left-4 top-3.5 text-slate-400 group-hover:text-blue-500 transition-colors"></i>
            </form>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('customer.export.wa_csv', $type) }}" title="Export WA (CSV)" class="bg-green-50 hover:bg-green-100 text-green-600 p-3.5 rounded-2xl border border-green-200 transition-all active:scale-95 shadow-sm">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="{{ route('customer.export.excel', $type) }}" title="Export Excel" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-600 p-3.5 rounded-2xl border border-emerald-200 transition-all active:scale-95 shadow-sm">
                    <i class="fas fa-file-excel"></i>
                </a>
                <a href="{{ route('customer.export.pdf', $type) }}" title="Export PDF" class="bg-red-50 hover:bg-red-100 text-red-600 p-3.5 rounded-2xl border border-red-200 transition-all active:scale-95 shadow-sm">
                    <i class="fas fa-file-pdf"></i>
                </a>
            </div>

            <button @click="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-6 rounded-2xl shadow-xl transition-all active:scale-95 text-sm flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-plus"></i> <span class="hidden xl:inline">Tambah Pelanggan</span>
            </button>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white shadow-sm rounded-3xl overflow-hidden border border-slate-200 mb-10">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-5 text-left font-black text-slate-400 uppercase text-[10px] tracking-widest">Nama / Tipe</th>
                        <th class="px-8 py-5 text-left font-black text-slate-400 uppercase text-[10px] tracking-widest">Lokasi & Infrastruktur</th>
                        <th class="px-8 py-5 text-right font-black text-slate-400 uppercase text-[10px] tracking-widest">Status Finansial</th>
                        <th class="px-8 py-5 text-left font-black text-slate-400 uppercase text-[10px] tracking-widest">Alat / Perangkat</th>
                        <th class="px-8 py-5 text-center font-black text-slate-400 uppercase text-[10px] tracking-widest w-32">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="c in customers" :key="c.id">
                    <tr class="hover:bg-blue-50/30 transition group">
                        <td class="px-8 py-6">
                            <div class="font-black text-slate-800 text-sm group-hover:text-blue-600 transition-colors" x-text="c.name"></div>
                            <span class="px-2.5 py-1 mt-2 inline-flex text-[9px] font-black uppercase tracking-tighter rounded-lg" 
                                :class="c.type == 'MEMBER' ? 'bg-emerald-50 text-emerald-600 shadow-[inset_0_0_0_1px_rgba(16,185,129,0.1)]' : 
                                   (c.type == 'RESELLER' ? 'bg-purple-50 text-purple-600 shadow-[inset_0_0_0_1px_rgba(147,51,234,0.1)]' : 'bg-blue-50 text-blue-600 shadow-[inset_0_0_0_1px_rgba(37,99,235,0.1)]')"
                                x-text="c.type">
                            </span>
                            <template x-if="c.whatsapp">
                                <div class="mt-2 flex items-center gap-1.5 text-[10px] font-bold text-green-600">
                                    <i class="fab fa-whatsapp"></i> <span x-text="c.whatsapp"></span>
                                </div>
                            </template>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-map-marker-alt text-slate-300 mt-1"></i>
                                <div>
                                    <div class="text-xs font-bold text-slate-600 leading-relaxed" x-text="c.location ? (c.location.length > 30 ? c.location.substring(0,30) + '...' : c.location) : 'Alamat belum diset'"></div>
                                    <template x-if="c.coordinates">
                                    <a :href="'https://maps.google.com/?q=' + c.coordinates" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-black text-blue-500 hover:text-blue-700 uppercase tracking-widest mt-1">
                                        View on Maps <i class="fas fa-external-link-alt text-[8px]"></i>
                                    </a>
                                    </template>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="text-sm font-black text-slate-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(c.bill_amount)"></div>
                            <div class="flex items-center justify-end gap-1.5 mt-1 border-t border-slate-50 pt-1">
                                <span class="text-[9px] font-black text-slate-400 uppercase">Paid:</span>
                                <span class="text-[10px] font-black text-emerald-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(c.paid_amount || 0)"></span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                             <div class="flex items-center gap-3">
                                 <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400">
                                     <i class="fas fa-microchip"></i>
                                 </div>
                                 <div class="space-y-0.5">
                                     <div class="text-[11px] font-black text-slate-700 uppercase leading-none" x-text="c.device_name || 'Unknown Engine'"></div>
                                     <div class="text-[10px] font-mono font-bold text-slate-400 tracking-tight" x-text="c.device_ip || '0.0.0.0'"></div>
                                 </div>
                             </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center justify-center gap-1.5">
                                <template x-if="c.whatsapp">
                                <a :href="'{{ route('wa_gateway.send_billing', ['id' => 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', c.id)" 
                                   class="p-2.5 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-xl transition shadow-sm" title="Kirim Tagihan WA">
                                    <i class="fab fa-whatsapp text-sm"></i>
                                </a>
                                </template>
                                <button @click="openModal(c, true)" class="p-2.5 bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white rounded-xl transition shadow-sm" title="View Details">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <button @click="openModal(c)" class="p-2.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition shadow-sm" title="Edit Customer">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <a :href="'{{ route('customer.delete', ['id' => 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', c.id)" 
                                   onclick="return confirm('Hapus pelanggan ini?')"
                                   class="p-2.5 bg-rose-50 text-rose-500 hover:bg-rose-600 hover:text-white rounded-xl transition shadow-sm" title="Delete Account">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    </template>

                    <template x-if="customers.length === 0">
                    <tr>
                        <td colspan="5" class="px-10 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-user-slash text-3xl text-slate-300"></i>
                                </div>
                                <h4 class="font-black text-slate-400 uppercase tracking-widest text-sm mb-2">No Customers Found</h4>
                                <p class="text-xs text-slate-400">Mulai dengan menambahkan pelanggan tercinta kamu di sistem.</p>
                            </div>
                        </td>
                    </tr>
                    </template>
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
        <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100">
            {{ $customers->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

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
                     class="relative inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full z-[1001] border border-slate-100">
                    <form action="{{ route('customer.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" x-model="formData.id">
                        
                        <div class="bg-white px-8 pt-8 pb-6">
                            <div class="flex justify-between items-center mb-8">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-user-edit text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-800 tracking-tight" x-text="viewMode ? 'Account Details' : (editMode ? 'Edit Account' : 'Register New Customer')"></h3>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Customer Management Module</p>
                                    </div>
                                </div>
                                <button type="button" @click="showModal = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Left Column -->
                                <div class="space-y-5">
                                    <div class="group">
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Jenis Pelanggan</label>
                                        <div class="relative">
                                            <select name="type" x-model="formData.type" :disabled="viewMode" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none appearance-none disabled:opacity-75 disabled:bg-slate-50 transition-all cursor-pointer">
                                                <option value="MEMBER">Member (Hotspot)</option>
                                                <option value="PERUMAHAN">Perumahan (PPPoE)</option>
                                                <option value="RESELLER">Reseller</option>
                                            </select>
                                            <div class="absolute right-4 top-3.5 text-slate-400 pointer-events-none">
                                                <i class="fas fa-chevron-down text-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Nama Lengkap</label>
                                        <input type="text" name="name" x-model="formData.name" :disabled="viewMode" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none disabled:bg-slate-50 transition-all placeholder:text-slate-300" placeholder="e.g. John Doe">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Nomor WhatsApp</label>
                                        <input type="text" name="whatsapp" x-model="formData.whatsapp" :disabled="viewMode" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none disabled:bg-slate-50 transition-all placeholder:text-slate-300" placeholder="e.g. 628123456789">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Tgl Pemasangan</label>
                                            <input type="date" name="installation_date" x-model="formData.installation_date" :disabled="viewMode" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none disabled:bg-slate-50 transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Tgl Jatuh Tempo</label>
                                            <input type="date" name="payment_date" x-model="formData.payment_date" :disabled="viewMode" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none disabled:bg-slate-50 transition-all">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Lokasi / Alamat</label>
                                        <textarea name="location" x-model="formData.location" :disabled="viewMode" rows="2" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none disabled:bg-slate-50 transition-all placeholder:text-slate-300" placeholder="Alamat lengkap..."></textarea>
                                    </div>
                                    <div>
                                         <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Koordinat GPS</label>
                                        <div class="flex gap-2">
                                            <div class="relative flex-grow">
                                                <input type="text" name="coordinates" x-model="formData.coordinates" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-[10px] font-mono font-bold text-slate-500 focus:outline-none" readonly placeholder="Wait for GPS...">
                                                <i class="fas fa-crosshairs absolute right-4 top-3.5 text-slate-300"></i>
                                            </div>
                                            <button type="button" @click="getLocation()" x-show="!viewMode" class="bg-blue-600 hover:bg-blue-700 text-white w-12 rounded-xl transition-all shadow-lg active:scale-95 flex items-center justify-center">
                                                <i class="fas fa-location-arrow"></i>
                                            </button>
                                             <a :href="'https://maps.google.com/?q=' + formData.coordinates" target="_blank" x-show="viewMode && formData.coordinates" class="bg-emerald-500 hover:bg-emerald-600 text-white w-12 rounded-xl transition-all shadow-lg flex items-center justify-center">
                                                <i class="fas fa-map-marked-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Tagihan Bulanan (Rp)</label>
                                        <div class="relative">
                                            <input type="number" name="bill_amount" x-model="formData.bill_amount" :disabled="viewMode" class="w-full bg-slate-50 border border-slate-100 rounded-xl pl-12 pr-4 py-3 text-sm font-black text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none disabled:bg-slate-50 transition-all">
                                            <div class="absolute left-4 top-3.5 text-slate-400 font-bold text-[10px]">RP</div>
                                        </div>
                                    </div>
                                </div>
                                    
                                <!-- Right Column -->
                                <div class="space-y-5">
                                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 space-y-4">
                                        <div class="flex items-center gap-2 mb-2">
                                            <i class="fas fa-hdd text-slate-400 text-xs"></i>
                                            <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Infrastruktur Alat</span>
                                        </div>
                                        <div>
                                            <label class="block text-slate-400 text-[9px] font-black uppercase mb-1">Nama Perangkat (CPE)</label>
                                            <input type="text" name="device_name" x-model="formData.device_name" :disabled="viewMode" placeholder="e.g. LHG 5 / LiteBeam" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:border-blue-500 disabled:bg-slate-100/50">
                                        </div>
                                        <div>
                                            <label class="block text-slate-400 text-[9px] font-black uppercase mb-1">IP Management</label>
                                            <input type="text" name="device_ip" x-model="formData.device_ip" :disabled="viewMode" placeholder="192.168.x.x" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-blue-500 disabled:bg-slate-100/50">
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-slate-400 text-[9px] font-black uppercase mb-1">User Alat</label>
                                                <input type="text" name="device_username" x-model="formData.device_username" :disabled="viewMode" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:border-blue-500 disabled:bg-slate-100/50">
                                            </div>
                                            <div>
                                                <label class="block text-slate-400 text-[9px] font-black uppercase mb-1">Pass Alat</label>
                                                <input type="text" name="device_password" x-model="formData.device_password" :disabled="viewMode" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:border-blue-500 disabled:bg-slate-100/50">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-indigo-50/50 rounded-2xl p-5 border border-indigo-100 space-y-4">
                                        <div class="flex items-center gap-2 mb-2">
                                            <i class="fas fa-mobile-alt text-indigo-400 text-xs"></i>
                                            <span class="text-[10px] text-indigo-400 font-black uppercase tracking-widest">Akses Aplikasi</span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-slate-400 text-[9px] font-black uppercase mb-1">Username App</label>
                                                <input type="text" name="app_username" x-model="formData.app_username" :disabled="viewMode" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:border-indigo-500 disabled:bg-slate-100/50">
                                            </div>
                                            <div>
                                                <label class="block text-slate-400 text-[9px] font-black uppercase mb-1">Password App</label>
                                                <input type="text" name="app_password" x-model="formData.app_password" :disabled="viewMode" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:border-indigo-500 disabled:bg-slate-100/50">
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Catatan Tambahan</label>
                                        <textarea name="notes" x-model="formData.notes" :disabled="viewMode" rows="3" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none disabled:bg-slate-50 transition-all placeholder:text-slate-300" placeholder="Informasi pendukung..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                            <button type="submit" x-show="!viewMode" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-blue-600 px-8 py-3.5 text-sm font-black text-white hover:bg-blue-700 shadow-xl shadow-blue-500/20 active:scale-95 transition-all">
                                <i class="fas fa-check"></i> <span>{{ $editMode ?? false ? 'Kemaskini Data' : 'Simpan Data Baru' }}</span>
                            </button>
                            <button type="button" @click="showModal = false" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-black text-slate-500 hover:text-slate-800 border border-slate-200 hover:border-slate-300 transition-all">
                                <i class="fas fa-times"></i> <span x-text="viewMode ? 'Tutup Dialog' : 'Batalkan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
