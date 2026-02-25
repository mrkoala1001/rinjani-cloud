@extends('layouts.app')

@section('title', 'Monitor Keuangan')
@section('header_title', 'Billing & Sales Monitor')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Month Info & Actions -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <h2 class="text-xl font-bold text-gray-700">Periode Berjalan: {{ date('F Y') }}</h2>
        
        <form action="{{ route('billing.monitor.exportPdf') }}" method="GET" class="w-full md:w-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-3 bg-white p-3 rounded-xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-2 flex-1 sm:flex-none">
                <span class="text-xs font-bold text-slate-500 w-16 md:w-auto text-left">DARI:</span>
                <input type="date" name="start_date" value="{{ date('Y-m-01') }}" required class="w-full sm:w-36 bg-slate-50 border-slate-200 rounded-lg text-xs focus:ring-blue-500 focus:border-blue-500 py-2 px-3">
            </div>
            <div class="flex items-center gap-2 flex-1 sm:flex-none">
                <span class="text-xs font-bold text-slate-500 w-16 md:w-auto text-left">S/D:</span>
                <input type="date" name="end_date" value="{{ date('Y-m-t') }}" required class="w-full sm:w-36 bg-slate-50 border-slate-200 rounded-lg text-xs focus:ring-blue-500 focus:border-blue-500 py-2 px-3">
            </div>
            <button type="submit" class="w-full sm:w-auto mt-2 sm:mt-0 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold py-2 px-4 rounded-lg shadow-sm transition flex items-center justify-center whitespace-nowrap">
                <i class="fas fa-file-pdf mr-2 text-lg"></i> Cetak PDF
            </button>
        </form>
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
</div>
@endsection
