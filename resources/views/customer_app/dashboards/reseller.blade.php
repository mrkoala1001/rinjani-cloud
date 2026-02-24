@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-6">
    <!-- Status Card -->
    <!-- Card 1: Generate Voucher -->
    <a href="{{ route('customer_app.reseller.vouchers') }}" class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 hover:shadow-md transition group block">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-xl bg-indigo-50 text-indigo-500 mr-4 group-hover:scale-110 transition">
                <i class="fas fa-magic fa-lg"></i>
            </div>
            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Voucher Saya</p>
        </div>
        <div class="flex items-baseline gap-2">
            <p class="text-2xl font-black text-slate-800">{{ number_format($totalVouchers) }}</p>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Pcs</span>
        </div>
    </a>

    <!-- Card 2: Voucher Terjual -->
    <a href="{{ route('customer_app.reseller.transactions') }}" class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 hover:shadow-md transition group block">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-xl bg-green-50 text-green-500 mr-4 group-hover:scale-110 transition">
                <i class="fas fa-shopping-cart fa-lg"></i>
            </div>
            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Total Terjual</p>
        </div>
        <div class="flex items-baseline gap-2">
            <p class="text-2xl font-black text-slate-800">{{ number_format($totalSold) }}</p>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Pcs</span>
        </div>
        <p class="text-[10px] font-black text-green-600 mt-2">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
    </a>

    <!-- Card 3: User Aktif -->
    <a href="{{ route('customer_app.reseller.active') }}" class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 hover:shadow-md transition group block">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-xl bg-blue-50 text-blue-500 mr-4 group-hover:scale-110 transition">
                <i class="fas fa-users fa-lg"></i>
            </div>
            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">User Aktif</p>
        </div>
        <div class="flex items-baseline gap-2">
            <p class="text-2xl font-black text-slate-800">Cek</p>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Online</span>
        </div>
    </a>

    <!-- Card 4: Saldo Aktif -->
    <a href="{{ route('customer_app.reseller.balance_logs') }}" class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 hover:shadow-md transition group block">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-xl bg-orange-50 text-orange-500 mr-4 group-hover:scale-110 transition">
                <i class="fas fa-wallet fa-lg"></i>
            </div>
            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Saldo Anda</p>
        </div>
        <p class="text-2xl font-black text-slate-800">Rp {{ number_format($customer->balance, 0, ',', '.') }}</p>
    </a>

    <!-- Quick Actions -->
    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Menu Reseller</h3>
    <div class="grid grid-cols-3 gap-4">
        <a href="{{ route('customer_app.reseller.vouchers') }}" class="aspect-square bg-white rounded-3xl border border-slate-100 shadow-sm flex flex-col items-center justify-center gap-3 group active:scale-95 transition-all">
            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition-all">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Voucher</span>
        </a>
        <a href="{{ route('customer_app.reseller.balance_logs') }}" class="aspect-square bg-white rounded-3xl border border-slate-100 shadow-sm flex flex-col items-center justify-center gap-3 group active:scale-95 transition-all text-center">
            <div class="w-12 h-12 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center text-xl group-hover:bg-orange-600 group-hover:text-white transition-all">
                <i class="fas fa-wallet"></i>
            </div>
            <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest px-1">Saldo</span>
        </a>
        <a href="{{ route('customer_app.reseller.distribution') }}" class="aspect-square bg-white rounded-3xl border border-slate-100 shadow-sm flex flex-col items-center justify-center gap-3 group active:scale-95 transition-all text-center">
            <div class="w-12 h-12 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center text-xl group-hover:bg-purple-600 group-hover:text-white transition-all">
                <i class="fas fa-history"></i>
            </div>
            <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest px-1">Distribusi</span>
        </a>
    </div>

    <!-- Info Section -->
    <div class="bg-blue-600 rounded-[2rem] p-6 text-white overflow-hidden relative">
        <div class="absolute top-0 right-0 p-4 opacity-20 text-4xl">
            <i class="fas fa-info-circle"></i>
        </div>
        <h4 class="font-black text-sm uppercase tracking-widest mb-2">Panel Reseller</h4>
        <p class="text-xs font-medium opacity-80 leading-relaxed">Gunakan panel ini untuk memonitor stok voucher dan riwayat setoran Anda ke pusat.</p>
    </div>
</div>
@endsection
