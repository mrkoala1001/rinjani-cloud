@extends('layouts.app')

@section('title', 'Pengeluaran')
@section('header_title', 'Data Pengeluaran (Expenses)')

@section('content')
<script>
    function expenseComponent() {
        return {
            showModal: false, 
            formData: { id: '', description: '', amount: '', date: '{{ date('Y-m-d') }}', category: 'Operasional', debtId: '' },
            debts: @js($debts->map(function($d) { return ['id' => $d->id, 'description' => $d->description, 'amount' => $d->amount]; })),
            selectedDebt: null,
            updateDebt() {
                var self = this;
                this.selectedDebt = this.debts.find(function(d) { return d.id == self.formData.debtId; }) || null;
            }
        };
    }
</script>

<div class="max-w-7xl mx-auto" x-data="expenseComponent()">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Today -->
        <div class="bg-red-600 bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Pengeluaran Hari Ini</p>
                    <h3 class="text-3xl font-bold mt-2">Rp {{ number_format($summaryToday->total ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-red-100 text-xs mt-1">{{ $summaryToday->count ?? 0 }} transaksi</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-calendar-day text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-orange-600 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Pengeluaran Bulan Ini</p>
                    <h3 class="text-3xl font-bold mt-2">Rp {{ number_format($summaryMonth->total ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-orange-100 text-xs mt-1">{{ $summaryMonth->count ?? 0 }} transaksi</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-calendar-alt text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Total -->
        <div class="bg-gray-600 bg-gradient-to-br from-gray-600 to-gray-700 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-100 text-sm font-medium">Total Pengeluaran</p>
                    <h3 class="text-3xl font-bold mt-2">Rp {{ number_format($summaryTotal->total ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-gray-100 text-xs mt-1">{{ $summaryTotal->count ?? 0 }} transaksi</p>
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
        <form action="{{ route('billing.expense') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end w-full xl:w-auto flex-grow">
            <!-- pertahankan pencarian (search) jika ada -->
            <input type="hidden" name="search" value="{{ $search ?? '' }}">
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                <div class="w-full sm:w-40 md:w-48">
                    <label class="block text-xs font-bold text-slate-500 mb-1">DARI TANGGAL</label>
                    <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
                </div>
                <div class="w-full sm:w-40 md:w-48">
                    <label class="block text-xs font-bold text-slate-500 mb-1">SAMPAI TANGGAL</label>
                    <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
                </div>
            </div>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none justify-center bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm flex items-center">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                @if(!empty($startDate) && !empty($endDate))
                <a href="{{ route('billing.expense.export', ['start_date' => $startDate, 'end_date' => $endDate, 'search' => $search ?? '']) }}" class="flex-1 sm:flex-none justify-center bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-green-700 transition shadow-sm whitespace-nowrap flex items-center">
                    <i class="fas fa-file-excel mr-2"></i> Export
                </a>
                @endif
                @if(!empty($startDate) || !empty($endDate))
                <a href="{{ route('billing.expense', ['search' => $search ?? '']) }}" class="flex-1 sm:flex-none justify-center bg-slate-100 text-slate-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-200 transition flex items-center">
                    Reset
                </a>
                @endif
            </div>
        </form>

        <!-- Search Form -->
        <form action="{{ route('billing.expense') }}" method="GET" class="flex gap-2 items-end w-full xl:w-auto pt-2 border-t border-slate-100 xl:border-t-0 xl:pt-0">
            <!-- pertahankan filter tanggal jika ada -->
            <input type="hidden" name="start_date" value="{{ $startDate ?? '' }}">
            <input type="hidden" name="end_date" value="{{ $endDate ?? '' }}">
            
            <div class="w-full sm:w-64 flex-grow">
                <label class="block text-xs font-bold text-slate-500 mb-1">CARI DATA</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Ketik kata kunci..." class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
            </div>
            <div class="flex gap-2 shrink-0">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm flex items-center justify-center h-[38px] w-[46px]" title="Cari">
                    <i class="fas fa-search"></i>
                </button>
                @if(!empty($search))
                <a href="{{ route('billing.expense', ['start_date' => $startDate ?? '', 'end_date' => $endDate ?? '']) }}" class="bg-slate-100 text-slate-600 px-3 py-2 rounded-lg text-sm font-bold hover:bg-slate-200 transition flex items-center justify-center h-[38px] w-[46px]" title="Hapus Pencarian">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Action Bar -->
     <div class="mb-6 flex justify-end">
        <button @click="showModal = true; formData = { id: '', description: '', amount: '', date: '{{ date('Y-m-d') }}', category: 'Operasional', debtId: '' }" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center">
            <i class="fas fa-plus mr-2"></i> Tambah Pengeluaran
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-red-500">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                        <th class="py-3 px-6 text-left">Tanggal</th>
                        <th class="py-3 px-6 text-left">Kategori</th>
                        <th class="py-3 px-6 text-left">Keterangan</th>
                        <th class="py-3 px-6 text-right">Jumlah (Rp)</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse($expenses as $item)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">
                            {{ date('d M Y', strtotime($item->date)) }}
                        </td>
                        <td class="py-3 px-6 text-left">
                            <span class="px-2 py-1 rounded-full text-xs font-bold 
                                {{ $item->category == 'Bayar Hutang' ? 'bg-yellow-100 text-yellow-700' : 
                                  ($item->category == 'Konsumsi' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ $item->category }}
                            </span>
                        </td>
                        <td class="py-3 px-6 text-left">
                            {{ $item->description }}
                            @if($item->debt)
                                <div class="text-xs text-gray-400">Hutang: {{ $item->debt->description }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-6 text-right font-bold text-red-600">
                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center">
                                <a href="{{ route('billing.deleteExpense', $item->id) }}" onclick="return confirm('Hapus data ini?')" class="w-4 transform hover:text-red-500 hover:scale-110">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-3 px-6 text-center">Tidak ada data pengeluaran.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $expenses->appends(request()->query())->links() }}
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
                     class="relative inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full z-[1001] border border-slate-100">
                    <form action="{{ route('billing.storeExpense') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" x-model="formData.id">
                        <div class="bg-white px-8 pt-8 pb-6">
                            <div class="flex justify-between items-center mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-minus-circle text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-800 tracking-tight" x-text="formData.id ? 'Edit Pengeluaran' : 'Tambah Pengeluaran'"></h3>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Expense Tracking</p>
                                    </div>
                                </div>
                                <button type="button" @click="showModal = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Tanggal</label>
                                        <input type="date" name="date" x-model="formData.date" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 focus:outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Kategori</label>
                                        <div class="relative">
                                            <select name="category" x-model="formData.category" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 focus:outline-none appearance-none cursor-pointer transition-all">
                                                <option value="Operasional">Operasional</option>
                                                <option value="Konsumsi">Konsumsi</option>
                                                <option value="Bayar Hutang">Bayar Hutang</option>
                                                <option value="DLL">DLL (Lain-lain)</option>
                                            </select>
                                            <div class="absolute right-4 top-3.5 text-slate-400 pointer-events-none">
                                                <i class="fas fa-chevron-down text-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Debt Selection (conditional) -->
                                <div x-show="formData.category === 'Bayar Hutang'" class="bg-amber-50 rounded-2xl p-4 border border-amber-100 mb-2">
                                    <label class="block text-amber-600 text-[10px] font-black uppercase tracking-widest mb-2 ml-1">Pilih Hutang</label>
                                    <div class="relative">
                                        <select name="debt_id" x-model="formData.debtId" @change="updateDebt()" class="w-full bg-white border border-amber-200 rounded-xl px-4 py-3 text-sm font-bold text-amber-800 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 focus:outline-none appearance-none cursor-pointer mb-2">
                                            <option value="">-- Pilih Hutang --</option>
                                            @foreach($debts as $debt)
                                                <option value="{{ $debt->id }}">{{ $debt->description }} (Sisa: Rp {{ number_format($debt->amount, 0, ',', '.') }})</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute right-4 top-3.5 text-amber-400 pointer-events-none">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Debt Info Display -->
                                    <template x-if="selectedDebt">
                                        <div class="flex justify-between items-center px-1">
                                            <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Sisa Hutang:</span>
                                            <span class="text-xs font-black text-amber-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedDebt.amount)"></span>
                                        </div>
                                    </template>
                                </div>
                                
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Keterangan</label>
                                    <input type="text" name="description" x-model="formData.description" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 focus:outline-none transition-all" placeholder="e.g. Biaya Listrik, Internet">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Jumlah (Rp)</label>
                                    <div class="relative">
                                        <input type="number" name="amount" x-model="formData.amount" required class="w-full bg-slate-50 border border-slate-100 rounded-xl pl-12 pr-4 py-3 text-sm font-black text-rose-600 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 focus:outline-none transition-all" placeholder="0">
                                        <div class="absolute left-4 top-3.5 text-slate-400 font-bold text-[10px]">RP</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-rose-600 px-8 py-3.5 text-sm font-black text-white hover:bg-rose-700 shadow-xl shadow-rose-500/20 active:scale-95 transition-all">
                                <i class="fas fa-save"></i> <span>Simpan Data</span>
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
</div>
@endsection