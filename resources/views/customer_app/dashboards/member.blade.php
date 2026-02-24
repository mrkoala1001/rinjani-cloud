@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-6">
    <!-- Active Voucher Card -->
    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 text-slate-50 text-8xl">
            <i class="fas fa-ticket-alt transform rotate-12"></i>
        </div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sisa Kuota / Waktu</h3>
                <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black rounded-full uppercase">Member Layanan</span>
            </div>
            <div class="flex items-baseline gap-2 mb-2">
                <h4 class="text-4xl font-black text-slate-900 tracking-tighter">Unlimited</h4>
            </div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-6">Aktif s/d 30 hari ke depan</p>
            
            <button class="w-full bg-indigo-600 text-white font-black py-4 rounded-2xl shadow-lg shadow-indigo-200 active:scale-95 transition-all text-sm tracking-widest uppercase flex items-center justify-center gap-2">
                <i class="fas fa-redo-alt"></i>
                PERPANJANG MASA AKTIF
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
            <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-lg mb-4">
                <i class="fas fa-bolt"></i>
            </div>
            <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest mb-1">Kecepatan</p>
            <p class="text-sm font-black text-slate-800">5 Mbps</p>
        </div>
        <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
            <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-lg mb-4">
                <i class="fas fa-user-friends"></i>
            </div>
            <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest mb-1">Max Perangkat</p>
            <p class="text-sm font-black text-slate-800">2 Device</p>
        </div>
    </div>

    <!-- Location Info -->
    <div class="bg-slate-900 rounded-[2rem] p-6 text-white flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-map-marker-alt text-red-500"></i>
            </div>
            <div>
                <h4 class="text-sm font-black">Lokasi Terdaftar</h4>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $customer->location ?: 'Belum diatur' }}</p>
            </div>
        </div>
        <i class="fas fa-chevron-right text-slate-600"></i>
    </div>
</div>
@endsection
