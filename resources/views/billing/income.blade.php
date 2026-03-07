@extends('layouts.app')

@section('title', 'Laporan Pemasukan')
@section('header_title', 'Riwayat Pemasukan & Pembayaran')

@section('content')
<script>
    function incomeComponent() {
        return {
            showModal: false,
            editModalOpen: false,
            editData: {},
            viewModalOpen: false,
            viewData: {},
            sourceMode: 'existing', 
            customerId: '',
            newCustomerName: '',
            
            // Caching for Dropdown
            customers: [],

            // Summary UI Data
            summaryToday: { total: 0, count: 0 },
            summaryMonth: { total: 0, count: 0 },
            summaryTotal: { total: 0, count: 0 },

            init() {
                this.summaryToday = @js($summaryToday);
                this.summaryMonth = @js($summaryMonth);
                this.summaryTotal = @js($summaryTotal);
                this.customers = @js($customers->map(function($c) { return ['id' => $c->id, 'name' => $c->name, 'bill_amount' => $c->bill_amount, 'location' => $c->location]; }));
            },

            selectedCustomer: null,
            updateSelected() {
                var self = this;
                this.selectedCustomer = this.customers.find(function(c) { 
                    return c.id == self.customerId; 
                }) || null;
            },
            openView(data) {
                console.log('DEBUG: openView called');
                this.viewData = data;
                this.viewModalOpen = true;
                this.$nextTick(() => console.log('DEBUG: viewModalOpen is now:', this.viewModalOpen));
            },
            openEdit(data) {
                console.log('DEBUG: openEdit called');
                let d = Object.assign({}, data);
                if (d.date && d.date.includes('T')) {
                    d.date = d.date.split('T')[0];
                }
                this.editData = d;
                this.editModalOpen = true;
                this.$nextTick(() => console.log('DEBUG: editModalOpen is now:', this.editModalOpen));
            }
        };
    }
</script>

<div class="max-w-7xl mx-auto" x-data="incomeComponent()">

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Today -->
        <div class="bg-green-600 bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm font-medium">Pemasukan Hari Ini</p>
                    <h3 class="text-3xl font-bold mt-2" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(summaryToday.total || 0)"></h3>
                    <p class="text-white/80 text-xs mt-1" x-text="(summaryToday.count || 0) + ' transaksi'"></p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-calendar-day text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-blue-600 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm font-medium">Pemasukan Bulan Ini</p>
                    <h3 class="text-3xl font-bold mt-2" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(summaryMonth.total || 0)"></h3>
                    <p class="text-white/80 text-xs mt-1" x-text="(summaryMonth.count || 0) + ' transaksi'"></p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-calendar-alt text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Total -->
        <div class="bg-purple-600 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm font-medium">Total Pemasukan</p>
                    <h3 class="text-3xl font-bold mt-2" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(summaryTotal.total || 0)"></h3>
                    <p class="text-white/80 text-xs mt-1" x-text="(summaryTotal.count || 0) + ' transaksi'"></p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-chart-line text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6 flex flex-col xl:flex-row gap-6 justify-between items-start xl:items-end">
        <!-- Date Filter Form -->
        <form action="{{ route('billing.income') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end w-full xl:w-auto flex-grow">
            <!-- pertahankan pencarian (search) jika ada -->
            <input type="hidden" name="search" value="{{ $search ?? '' }}">
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                <div class="w-full sm:w-40 md:w-48">
                    <label class="block text-xs font-bold text-slate-500 mb-1">DARI TANGGAL</label>
                    <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div class="w-full sm:w-40 md:w-48">
                    <label class="block text-xs font-bold text-slate-500 mb-1">SAMPAI TANGGAL</label>
                    <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                </div>
            </div>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none justify-center bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm flex items-center">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                @if(!empty($startDate) && !empty($endDate))
                <a href="{{ route('billing.income.export', ['start_date' => $startDate, 'end_date' => $endDate, 'search' => $search ?? '']) }}" class="flex-1 sm:flex-none justify-center bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-green-700 transition shadow-sm whitespace-nowrap flex items-center">
                    <i class="fas fa-file-excel mr-2"></i> Export
                </a>
                @endif
                @if(!empty($startDate) || !empty($endDate))
                <a href="{{ route('billing.income', ['search' => $search ?? '']) }}" class="flex-1 sm:flex-none justify-center bg-slate-100 text-slate-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-200 transition flex items-center">
                    Reset
                </a>
                @endif
            </div>
        </form>

        <!-- Search Form -->
        <form action="{{ route('billing.income') }}" method="GET" class="flex gap-2 items-end w-full xl:w-auto pt-2 border-t border-slate-100 xl:border-t-0 xl:pt-0">
            <!-- pertahankan filter tanggal jika ada -->
            <input type="hidden" name="start_date" value="{{ $startDate ?? '' }}">
            <input type="hidden" name="end_date" value="{{ $endDate ?? '' }}">
            
            <div class="w-full sm:w-64 flex-grow">
                <label class="block text-xs font-bold text-slate-500 mb-1">CARI DATA</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Ketik kata kunci..." class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div class="flex gap-2 shrink-0">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm flex items-center justify-center h-[38px] w-[46px]" title="Cari">
                    <i class="fas fa-search"></i>
                </button>
                @if(!empty($search))
                <a href="{{ route('billing.income', ['start_date' => $startDate ?? '', 'end_date' => $endDate ?? '']) }}" class="bg-slate-100 text-slate-600 px-3 py-2 rounded-lg text-sm font-bold hover:bg-slate-200 transition flex items-center justify-center h-[38px] w-[46px]" title="Hapus Pencarian">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Action Bar -->
    <div class="mb-6 flex justify-end">
        <button @click="showModal = true" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center">
            <i class="fas fa-plus mr-2"></i> Input Pembayaran
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-green-500">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                        <th class="py-3 px-6 text-left">Tanggal</th>
                        <th class="py-3 px-6 text-left">Kategori</th>
                        <th class="py-3 px-6 text-left">Pelanggan / Kode</th>
                        <th class="py-3 px-6 text-left">Keterangan</th>
                        <th class="py-3 px-6 text-right">Jumlah (Rp)</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse($incomes as $income)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">
                            {{ date('d M Y', strtotime($income->date)) }}
                        </td>
                        <td class="py-3 px-6 text-left">
                            <span class="px-2 py-1 rounded-full text-xs font-bold 
                                {{ $income->category == 'Voucher' ? 'bg-purple-100 text-purple-700' : 
                                  ($income->category == 'Member' ? 'bg-blue-100 text-blue-700' : 
                                  ($income->category == 'Reseller' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-700')) }}">
                                {{ $income->category }}
                            </span>
                        </td>
                        <td class="py-3 px-6 text-left">
                            @if($income->customer_name)
                                <div class="font-bold">{{ $income->customer_name }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-6 text-left">
                            {{ $income->description }}
                            @if($income->payment_method)
                                <div class="text-xs text-gray-400">Via: {{ $income->payment_method }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-6 text-right font-bold text-green-600">
                            Rp {{ number_format($income->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <!-- Tombol Lihat -->
                                <button @click="openView(@js($income))" 
                                        class="hover:scale-110 transition-transform text-blue-500 hover:text-blue-700 p-1" 
                                        title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <!-- Tombol Edit -->
                                <button @click="openEdit(@js($income))" 
                                        class="hover:scale-110 transition-transform text-yellow-500 hover:text-yellow-700 p-1" 
                                        title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Tombol Print -->
                                <a href="{{ route('billing.printIncome', $income->id) }}" 
                                   target="_blank" 
                                   class="hover:scale-110 transition-transform text-gray-500 hover:text-gray-700 p-1" 
                                   title="Cetak">
                                    <i class="fas fa-print"></i>
                                </a>

                                <!-- Tombol Hapus -->
                                <form action="{{ route('billing.deleteIncome', $income->id) }}" 
                                      method="POST" 
                                      class="inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="hover:scale-110 transition-transform text-red-500 hover:text-red-700 p-1" 
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                                <!-- Bukti Gambar (Jika ada) -->
                                @if($income->proof_image)
                                    <a href="{{ asset('storage/' . $income->proof_image) }}" 
                                       target="_blank" 
                                       class="hover:scale-110 transition-transform text-green-500 hover:text-green-700 p-1" 
                                       title="Lihat Bukti Foto">
                                        <i class="fas fa-image"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-3 px-6 text-center text-gray-500">Tidak ada data pemasukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($incomes->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $incomes->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Manual Input Modal -->
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
                     class="relative inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full z-[1001] border border-slate-100">
                    <form action="{{ route('billing.storeIncome') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white px-8 pt-8 pb-6">
                            <div class="flex justify-between items-center mb-8">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-plus-circle text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Input Pembayaran</h3>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Billing & Financial System</p>
                                    </div>
                                </div>
                                <button type="button" @click="showModal = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="space-y-6">
                                <!-- Source Selection -->
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-3 ml-1">Sumber Pelanggan</label>
                                    <div class="flex gap-4">
                                        <label class="flex-1 relative cursor-pointer group">
                                            <input type="radio" name="source_mode" value="existing" x-model="sourceMode" class="peer absolute opacity-0">
                                            <div class="p-3 text-center rounded-xl bg-white border border-slate-200 text-slate-600 font-bold text-xs transition-all peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 peer-checked:shadow-lg peer-checked:shadow-emerald-500/20 group-hover:border-emerald-200">
                                                Pelanggan Ada
                                            </div>
                                        </label>
                                        <label class="flex-1 relative cursor-pointer group">
                                            <input type="radio" name="source_mode" value="new" x-model="sourceMode" class="peer absolute opacity-0">
                                            <div class="p-3 text-center rounded-xl bg-white border border-slate-200 text-slate-600 font-bold text-xs transition-all peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 peer-checked:shadow-lg peer-checked:shadow-emerald-500/20 group-hover:border-emerald-200">
                                                Guest / Baru
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Customer Selection Fields -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div x-show="sourceMode === 'existing'" class="col-span-1 md:col-span-2">
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Pilih Pelanggan</label>
                                        <div class="relative">
                                            <select name="customer_id" x-model="customerId" @change="updateSelected()" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:outline-none appearance-none cursor-pointer transition-all">
                                                <option value="">-- Pilih Pelanggan --</option>
                                                @foreach($customers as $c)
                                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->type }})</option>
                                                @endforeach
                                            </select>
                                            <div class="absolute right-4 top-3.5 text-slate-400 pointer-events-none">
                                                <i class="fas fa-chevron-down text-xs"></i>
                                            </div>
                                        </div>
                                        
                                        <!-- Selected Customer Info -->
                                        <template x-if="selectedCustomer">
                                            <div class="mt-4 p-5 bg-blue-50/50 border border-blue-100 rounded-2xl space-y-2">
                                                <div class="flex justify-between items-center">
                                                    <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Tagihan Bulanan</span>
                                                    <span class="text-sm font-black text-blue-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedCustomer.bill_amount)"></span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Lokasi</span>
                                                    <span class="text-xs font-bold text-blue-700" x-text="selectedCustomer.location || '-'"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <div x-show="sourceMode === 'new'" class="col-span-1 md:col-span-2">
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Nama Pelanggan / Guest</label>
                                        <input type="text" name="new_customer_name" x-model="newCustomerName" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:outline-none transition-all" placeholder="e.g. Bpk. Ahmad">
                                    </div>

                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Kategori</label>
                                        <div class="relative">
                                            <select name="category" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:outline-none appearance-none cursor-pointer transition-all">
                                                <option value="Member">Member (Hotspot)</option>
                                                <option value="Reseller">Reseller</option>
                                                <option value="Voucher">Voucher</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                            <div class="absolute right-4 top-3.5 text-slate-400 pointer-events-none">
                                                <i class="fas fa-chevron-down text-xs"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Metode Pembayaran</label>
                                        <div class="relative">
                                            <select name="payment_method" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:outline-none appearance-none cursor-pointer transition-all">
                                                <option value="Cash">Tunai (Cash)</option>
                                                <option value="Transfer">Transfer Bank</option>
                                                <option value="E-Wallet">E-Wallet (OVO/DANA/Gopay)</option>
                                            </select>
                                            <div class="absolute right-4 top-3.5 text-slate-400 pointer-events-none">
                                                <i class="fas fa-chevron-down text-xs"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Tanggal Transaksi</label>
                                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:outline-none transition-all">
                                    </div>

                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Jumlah Bayar (Rp)</label>
                                        <div class="relative">
                                            <input type="number" name="amount" required class="w-full bg-slate-50 border border-slate-100 rounded-xl pl-12 pr-4 py-3 text-sm font-black text-emerald-600 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:outline-none transition-all">
                                            <div class="absolute left-4 top-3.5 text-slate-400 font-bold text-[10px]">RP</div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Keterangan / Deskripsi</label>
                                    <input type="text" name="description" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:outline-none transition-all placeholder:text-slate-300" placeholder="e.g. Pembayaran Bulan Januari">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Bukti Foto</label>
                                        <div class="relative group">
                                            <input type="file" name="proof_image" accept="image/*" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 text-[10px] font-bold text-slate-500 focus:outline-none group-hover:border-emerald-200 transition-all cursor-pointer">
                                            <i class="fas fa-camera absolute right-4 top-3 text-slate-300"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Catatan Internal</label>
                                        <textarea name="notes" rows="1" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 text-[10px] font-bold text-slate-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:outline-none transition-all placeholder:text-slate-300" placeholder="Notes..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-emerald-600 px-8 py-3.5 text-sm font-black text-white hover:bg-emerald-700 shadow-xl shadow-emerald-500/20 active:scale-95 transition-all">
                                <i class="fas fa-check"></i> <span>Simpan Pembayaran</span>
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
        <div x-show="editModalOpen" 
             x-cloak 
             class="fixed inset-0 z-[1000] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <!-- Backdrop -->
                <div x-show="editModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="editModalOpen = false" 
                     class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

                <!-- Modal Box -->
                <div x-show="editModalOpen"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 -translate-y-12"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-12"
                     class="relative inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full z-[1001] border border-slate-100">
                    <form action="{{ route('billing.updateIncome') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" x-model="editData.id">
                        <div class="bg-white px-8 pt-8 pb-6">
                            <div class="flex justify-between items-center mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-edit text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Edit Pemasukan</h3>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Update Data Transaksi</p>
                                    </div>
                                </div>
                                <button type="button" @click="editModalOpen = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Kategori</label>
                                        <div class="relative">
                                            <select name="category" x-model="editData.category" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none appearance-none cursor-pointer">
                                                <option value="Member">Member (Hotspot)</option>
                                                <option value="Reseller">Reseller</option>
                                                <option value="Voucher">Voucher</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                            <div class="absolute right-4 top-3.5 text-slate-400 pointer-events-none">
                                                <i class="fas fa-chevron-down text-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Jumlah (Rp)</label>
                                        <input type="number" name="amount" x-model="editData.amount" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-black text-emerald-600 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none">
                                    </div>
                                </div>
    
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Tanggal</label>
                                        <input type="date" name="date" x-model="editData.date" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Metode</label>
                                        <div class="relative">
                                            <select name="payment_method" x-model="editData.payment_method" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none appearance-none cursor-pointer">
                                                <option value="Cash">Tunai (Cash)</option>
                                                <option value="Transfer">Transfer Bank</option>
                                                <option value="E-Wallet">E-Wallet (OVO/DANA/Gopay)</option>
                                            </select>
                                            <div class="absolute right-4 top-3.5 text-slate-400 pointer-events-none">
                                                <i class="fas fa-chevron-down text-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Keterangan</label>
                                    <input type="text" name="description" x-model="editData.description" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none">
                                </div>
                                
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Catatan</label>
                                    <textarea name="notes" x-model="editData.notes" rows="2" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:outline-none"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-blue-600 px-8 py-3.5 text-sm font-black text-white hover:bg-blue-700 shadow-xl shadow-blue-500/20 active:scale-95 transition-all">
                                <i class="fas fa-save"></i> <span>Update Pemasukan</span>
                            </button>
                            <button type="button" @click="editModalOpen = false" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-black text-slate-500 hover:text-slate-800 border border-slate-200 hover:border-slate-300 transition-all shadow-sm">
                                Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
    <!-- Detail Modal -->
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
                                    <h3 class="text-xl font-black text-slate-800 tracking-tight">Detail Pemasukan</h3>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Billing & Financial System</p>
                                </div>
                            </div>
                            <button type="button" @click="viewModalOpen = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 space-y-4 font-bold text-sm">
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</span>
                                <span class="text-slate-700" x-text="viewData.date ? new Date(viewData.date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '-'"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori</span>
                                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-black uppercase" x-text="viewData.category"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pelanggan</span>
                                <span class="text-slate-800" x-text="viewData.customer_name || '-'"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Jumlah</span>
                                <span class="text-emerald-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(viewData.amount)"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Metode</span>
                                <span class="text-slate-700" x-text="viewData.payment_method"></span>
                            </div>
                            <div class="flex flex-col gap-1 border-b border-slate-200/50 pb-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Keterangan</span>
                                <span class="text-slate-700 font-medium" x-text="viewData.description"></span>
                            </div>
                            <div class="flex flex-col gap-1 pt-1" x-show="viewData.notes">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Catatan</span>
                                <span class="text-xs text-slate-500 italic" x-text="viewData.notes"></span>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-slate-200" x-show="viewData.proof_image">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">BUKTI TRANSAKSI:</p>
                                <div class="rounded-2xl overflow-hidden border border-slate-200 bg-white">
                                    <img :src="'/storage/' + viewData.proof_image" class="w-full h-auto object-cover max-h-64" alt="Bukti">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                        <a :href="'/billing/income/print/' + viewData.id" target="_blank" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-gray-600 px-8 py-3.5 text-sm font-black text-white hover:bg-gray-700 shadow-xl shadow-gray-500/20 active:scale-95 transition-all">
                            <i class="fas fa-print"></i> <span>Cetak Bukti</span>
                        </a>
                        <button type="button" @click="viewModalOpen = false" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-black text-slate-500 hover:text-slate-800 border border-slate-200 hover:border-slate-300 transition-all shadow-sm">
                            Tutup Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection