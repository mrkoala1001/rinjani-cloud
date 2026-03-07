@extends('layouts.app')

@section('title', 'Monitor Keuangan')
@section('header_title', 'Billing & Sales Monitor')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ showCloseModal: false, startDate: '{{ date('Y-m-01') }}', endDate: '{{ date('Y-m-t') }}' }">
    <!-- Month Info & Actions -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-700">Periode Berjalan: {{ date('F Y') }}</h2>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1 italic">Pantau & Kelola Laporan Keuangan Berkala</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
            <form action="{{ route('billing.monitor.exportPdf') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 bg-white p-2 rounded-xl shadow-sm border border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter ml-2">Mulai</span>
                    <input type="date" name="start_date" x-model="startDate" required class="bg-slate-50 border-none rounded-lg text-xs font-bold focus:ring-0 py-1.5 px-3">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">S/D</span>
                    <input type="date" name="end_date" x-model="endDate" required class="bg-slate-50 border-none rounded-lg text-xs font-bold focus:ring-0 py-1.5 px-3">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase py-2 px-6 rounded-lg shadow-lg shadow-blue-500/20 transition-all flex items-center justify-center">
                    <i class="fas fa-file-pdf mr-2 text-sm text-blue-200"></i> Cetak Laporan
                </button>
            </form>

            <button @click="showCloseModal = true" class="bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-black uppercase py-2.5 px-6 rounded-xl shadow-lg shadow-rose-500/20 transition-all flex items-center justify-center">
                <i class="fas fa-book-reader mr-2 text-sm text-rose-200"></i> Tutup Buku
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Pemasukan -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-arrow-down text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Pemasukan</p>
                    <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($income, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- Pengeluaran -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-arrow-up text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Pengeluaran</p>
                    <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($expense, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- Hutang -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-hand-holding-usd text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Hutang</p>
                    <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($debt, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- Profit -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 {{ $profit >= 0 ? 'border-blue-500' : 'border-red-600' }}">
            <div class="flex items-center">
                <div class="p-3 {{ $profit >= 0 ? 'bg-blue-100' : 'bg-red-100' }} rounded-full">
                    <i class="fas fa-wallet {{ $profit >= 0 ? 'text-blue-600' : 'text-red-600' }} text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Keuntungan Bersih</p>
                    <h3 class="text-2xl font-bold {{ $profit >= 0 ? 'text-blue-600' : 'text-red-600' }}">Rp {{ number_format($profit, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Breakdown Panels -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Voucher Sales -->
        <div class="bg-white rounded-lg shadow p-4 border-t-4 border-purple-500">
            <h4 class="text-gray-500 text-sm uppercase font-bold mb-2">Pemasukan Voucher</h4>
            <div class="flex justify-between items-end">
                <span class="text-2xl font-bold text-gray-800">Rp {{ number_format($income_voucher, 0, ',', '.') }}</span>
                <i class="fas fa-ticket-alt text-purple-200 text-4xl"></i>
            </div>
        </div>

        <!-- Member Sales -->
        <div class="bg-white rounded-lg shadow p-4 border-t-4 border-blue-500">
            <h4 class="text-gray-500 text-sm uppercase font-bold mb-2">Pemasukan Member</h4>
            <div class="flex justify-between items-end">
                <span class="text-2xl font-bold text-gray-800">Rp {{ number_format($income_member, 0, ',', '.') }}</span>
                <i class="fas fa-users text-blue-200 text-4xl"></i>
            </div>
        </div>

        <!-- Reseller Sales -->
        <div class="bg-white rounded-lg shadow p-4 border-t-4 border-orange-500">
            <h4 class="text-gray-500 text-sm uppercase font-bold mb-2">Pemasukan Reseller</h4>
            <div class="flex justify-between items-end">
                <span class="text-2xl font-bold text-gray-800">Rp {{ number_format($income_reseller, 0, ',', '.') }}</span>
                <i class="fas fa-user-tie text-orange-200 text-4xl"></i>
            </div>
        </div>
    </div>

    <!-- Chart Placeholder or Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Ringkasan</h3>
            <p class="text-gray-600">Grafik dan statistik detail akan ditampilkan di sini.</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Tips</h3>
            <ul class="list-disc list-inside text-gray-600 space-y-2">
                <li>Catat setiap pengeluaran operasional untuk perhitungan profit yang akurat.</li>
                <li>Monitor hutang pelanggan secara berkala.</li>
                <li>Pemasukan otomatis tercatat dari penjualan voucher dan billing.</li>
            </ul>
        </div>
    </div>
    <!-- Tutup Buku Modal -->
    <template x-teleport="body">
        <div x-show="showCloseModal" x-cloak class="fixed inset-0 z-[1000] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div x-show="showCloseModal" @click="showCloseModal = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
                
                <div x-show="showCloseModal" class="relative inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full z-[1001] border border-slate-100">
                    <div class="bg-white px-8 pt-8 pb-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-archive text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-800 tracking-tight">Proses Tutup Buku</h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Arsip & Pembersihan Data</p>
                            </div>
                        </div>

                        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 mb-6">
                            <div class="flex gap-3">
                                <i class="fas fa-exclamation-triangle text-amber-500 mt-1"></i>
                                <div class="text-xs text-amber-700 font-bold leading-relaxed">
                                    PERHATIAN: Proses ini akan menghapus permanen data riwayat transaksi pada periode yang dipilih. Pastikan Anda sudah mengunduh laporan PDF sebagai arsip.
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('billing.monitor.closePeriod') }}" method="POST" id="closeBookForm">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1.5 ml-1">Periode Tutup Buku</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="date" name="start_date" x-model="startDate" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 focus:outline-none transition-all">
                                        <input type="date" name="end_date" x-model="endDate" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 focus:outline-none transition-all">
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-2">
                                    <input type="checkbox" id="confirm_pdf" required class="mt-1 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                    <label for="confirm_pdf" class="text-[11px] font-bold text-slate-600 cursor-pointer">Saya mengonfirmasi bahwa saya sudah mengunduh (arsip) laporan keuangan untuk periode ini.</label>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="bg-slate-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                        <button type="submit" form="closeBookForm" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-rose-600 px-8 py-3.5 text-sm font-black text-white hover:bg-rose-700 shadow-xl shadow-rose-500/20 active:scale-95 transition-all">
                            <i class="fas fa-trash-alt"></i> <span>Tutup Buku & Hapus Data</span>
                        </button>
                        <button type="button" @click="showCloseModal = false" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-black text-slate-500 hover:text-slate-800 border border-slate-200 hover:border-slate-300 transition-all">
                            Batalkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
