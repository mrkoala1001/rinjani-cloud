@extends('layouts.app')

@section('title', 'Reseller Dashboard')
@section('header_title', 'Reseller Dashboard')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="w-1.5 h-8 bg-green-600 rounded-full"></div>
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Selamat Datang, {{ auth()->user()->name }}!</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest leading-none mt-1">Panel Ringkasan Penjualan Reseller</p>
        </div>
    </div>
    <a href="{{ route('report.form') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-black text-white font-bold rounded-xl shadow-lg transition-all active:scale-95 group">
        <i class="fas fa-paper-plane text-blue-400 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i> 
        <span>Report to Mr. Koala</span>
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Generate Voucher (Like Owner's Total Voucher) -->
    <a href="{{ route('reseller.generate') }}" class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 hover:shadow-md transition group block">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-xl bg-indigo-50 text-indigo-500 mr-4 group-hover:scale-110 transition">
                <i class="fas fa-magic fa-lg"></i>
            </div>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Generate Voucher</p>
        </div>
        <div class="flex items-center justify-between">
            <p class="text-2xl font-black text-slate-800">Buat Baru</p>
            <i class="fas fa-plus-circle text-indigo-500"></i>
        </div>
    </a>

    <!-- Card 2: Voucher Terjual (Like Owner's Total Sold) -->
    <a href="{{ route('reseller.vouchers') }}" class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 hover:shadow-md transition group block">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-xl bg-green-50 text-green-500 mr-4 group-hover:scale-110 transition">
                <i class="fas fa-shopping-cart fa-lg"></i>
            </div>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Total Terjual</p>
        </div>
        <div class="flex items-baseline gap-2">
            <p class="text-2xl font-black text-slate-800">{{ number_format($totalSoldCount) }}</p>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Voucher</span>
        </div>
        <p class="text-[10px] font-black text-green-600 mt-2">Rp {{ number_format($totalSoldPrice, 0, ',', '.') }}</p>
    </a>

    <!-- Card 3: Distribusi (Like Owner's Total Voucher) -->
    <a href="{{ route('reseller.distribution') }}" class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 hover:shadow-md transition group block">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-xl bg-blue-50 text-blue-500 mr-4 group-hover:scale-110 transition">
                <i class="fas fa-ticket-alt fa-lg"></i>
            </div>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Stock / Distribusi</p>
        </div>
        <div class="flex items-baseline gap-2">
            <p class="text-2xl font-black text-slate-800">{{ number_format($totalVouchersCount) }}</p>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Voucher</span>
        </div>
    </a>

    <!-- Card 4: Saldo Aktif (Like Owner's Pemasukan) -->
    <a href="{{ route('reseller.balance') }}" class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 hover:shadow-md transition group block">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-xl bg-orange-50 text-orange-500 mr-4 group-hover:scale-110 transition">
                <i class="fas fa-wallet fa-lg"></i>
            </div>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Saldo Saat Ini</p>
        </div>
        <p class="text-2xl font-black text-slate-800">Rp {{ number_format($balance, 0, ',', '.') }}</p>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="flex items-center gap-3 mb-6">
            <i class="fas fa-info-circle text-blue-500 text-xl"></i>
            <h3 class="text-lg font-black text-slate-800">Informasi Reseller</h3>
        </div>
        
        <div class="prose prose-slate max-w-none">
            <p class="text-slate-600 leading-relaxed font-medium">
                Selamat datang di panel reseller Hotpot. Di sini Anda dapat memantau produktivitas penjualan voucher Anda. 
                Data di atas ditarik berdasarkan identitas username Anda yang tercatat pada sistem billing utama.
            </p>
            <div class="mt-8 p-4 bg-blue-50 rounded-xl border border-blue-100 flex items-start gap-4">
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div>
                    <h4 class="font-black text-blue-800 text-sm uppercase tracking-wider leading-none mb-1">Tips Penjualan</h4>
                    <p class="text-xs text-blue-600 font-medium">Pastikan setiap melakukan setoran ke owner, ingatkan untuk mencatat username Anda pada catatan transaksi agar statistik di sini terupdate secara otomatis.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-slate-900 rounded-2xl p-8 text-white relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 text-white/5 text-9xl transition-transform group-hover:scale-110">
            <i class="fas fa-rocket"></i>
        </div>
        <div class="relative z-10">
            <h3 class="text-xl font-black mb-4">Quick Action</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('reseller.generate') }}" class="p-4 bg-white/10 hover:bg-white/20 rounded-xl transition flex flex-col items-center gap-2 border border-white/10">
                    <i class="fas fa-plus text-blue-400"></i>
                    <span class="text-xs font-bold uppercase tracking-widest">Generate</span>
                </a>
                <a href="{{ route('reseller.balance') }}" class="p-4 bg-white/10 hover:bg-white/20 rounded-xl transition flex flex-col items-center gap-2 border border-white/10">
                    <i class="fas fa-plus-circle text-orange-400"></i>
                    <span class="text-xs font-bold uppercase tracking-widest">Isi Saldo</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
