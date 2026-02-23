@extends('layouts.app')

@section('title', 'Hutang')
@section('header_title', 'Catatan Hutang (Debts)')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ showModal: false, formData: { id: '', description: '', amount: '', date: '{{ date('Y-m-d') }}' } }">
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Total Debt -->
        <div class="bg-yellow-600 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Total Hutang</p>
                    <h3 class="text-3xl font-bold mt-2">Rp {{ number_format($totalDebt ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-yellow-100 text-xs mt-1">{{ $countDebt ?? 0 }} hutang</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-hand-holding-usd text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Paid Amount -->
        <div class="bg-green-600 bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Sudah Dibayar</p>
                    <h3 class="text-3xl font-bold mt-2">Rp {{ number_format($totalPaid ?? 0, 0, ',', '.') }}</h3>
                    @php
                        $percentage = ($totalDebt > 0) ? round(($totalPaid / ($totalDebt + $totalPaid)) * 100, 1) : 0;
                    @endphp
                    <p class="text-green-100 text-xs mt-1">{{ $percentage }}% terbayar</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-check-circle text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
     <div class="mb-6 flex justify-end">
        <button @click="showModal = true; formData = { id: '', description: '', amount: '', date: '{{ date('Y-m-d') }}' }" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded shadow transition flex items-center">
            <i class="fas fa-plus mr-2"></i> Tambah Hutang
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-yellow-500">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                        <th class="py-3 px-6 text-left">Tanggal</th>
                        <th class="py-3 px-6 text-left">Keterangan</th>
                        <th class="py-3 px-6 text-right">Jumlah (Rp)</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse($debts as $item)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">
                            {{ date('d M Y', strtotime($item->date)) }}
                        </td>
                        <td class="py-3 px-6 text-left">
                            {{ $item->description }}
                        </td>
                        <td class="py-3 px-6 text-right font-bold text-yellow-600">
                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center">
                                <button @click="formData = { id: {{ $item->id }}, description: '{{ $item->description }}', amount: '{{ $item->amount }}', date: '{{ $item->date }}' }; showModal = true" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="{{ route('billing.deleteDebt', $item->id) }}" onclick="return confirm('Hapus data ini?')" class="w-4 transform hover:text-red-500 hover:scale-110">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-3 px-6 text-center">Tidak ada data hutang.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($debts->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $debts->appends(request()->query())->links() }}
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
                    <form action="{{ route('billing.storeDebt') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" x-model="formData.id">
                        <div class="bg-white px-8 pt-8 pb-6">
                            <div class="flex justify-between items-center mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-hand-holding-usd text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-800 tracking-tight" x-text="formData.id ? 'Edit Hutang' : 'Tambah Hutang'"></h3>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Liability Management</p>
                                    </div>
                                </div>
                                <button type="button" @click="showModal = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Tanggal</label>
                                    <input type="date" name="date" x-model="formData.date" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Keterangan / Deskripsi</label>
                                    <input type="text" name="description" x-model="formData.description" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 focus:outline-none transition-all" placeholder="e.g. Hutang Hardware ke Toko ABC">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Jumlah (Rp)</label>
                                    <div class="relative">
                                        <input type="number" name="amount" x-model="formData.amount" required class="w-full bg-slate-50 border border-slate-100 rounded-xl pl-12 pr-4 py-3 text-sm font-black text-amber-600 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 focus:outline-none transition-all" placeholder="0">
                                        <div class="absolute left-4 top-3.5 text-slate-400 font-bold text-[10px]">RP</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-amber-600 px-8 py-3.5 text-sm font-black text-white hover:bg-amber-700 shadow-xl shadow-amber-500/20 active:scale-95 transition-all">
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