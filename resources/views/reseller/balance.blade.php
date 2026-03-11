@extends('layouts.app')

@section('title', 'Saldo Saya')
@section('header_title', 'Saldo Saya')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="w-1.5 h-8 bg-emerald-600 rounded-full"></div>
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Saldo & Keuangan</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest leading-none mt-1">Kelola saldo transaksi voucher Anda</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Card Saldo -->
    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-3xl p-8 text-white shadow-xl shadow-emerald-600/20 flex flex-col justify-between min-h-[250px] relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
        
        <div class="relative z-10">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-2">Saldo Aktif</p>
            <h3 class="text-5xl font-black tracking-tighter">Rp {{ number_format($profile->balance, 0, ',', '.') }}</h3>
        </div>

        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60">Reseller ID</p>
                <p class="font-bold">#RS-{{ str_pad($profile->user_id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="p-4 bg-white/20 backdrop-blur-md rounded-2xl">
                <i class="fas fa-wallet text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Request Top Up -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 flex flex-col items-center text-center">
        <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-2xl mb-4">
            <i class="fas fa-hand-holding-usd"></i>
        </div>
        <h3 class="text-xl font-black text-slate-800 mb-2">Tambah Saldo?</h3>
        <p class="text-sm text-slate-500 font-medium mb-8 leading-relaxed">
            Untuk melakukan pengisian saldo, silakan hubungi owner/admin melalui WhatsApp atau kunjungi kantor pusat kami.
        </p>
        
        <a href="{{ route('reseller.topup.index') }}" class="w-full py-4 bg-gradient-to-r from-indigo-600 to-blue-700 hover:from-indigo-700 hover:to-blue-800 text-white font-black rounded-2xl shadow-lg shadow-indigo-200 transition-all active:scale-95 flex items-center justify-center gap-3 mb-4">
            <i class="fas fa-bolt text-yellow-300"></i>
            TOP-UP OTOMATIS (GATEWAY)
        </a>
        
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">— ATAU —</p>

        <a href="https://wa.me/6281234567890?text=Halo%20Owner,%20saya%20reseller%20{{ auth()->user()->name }}%20ingin%20top%20up%20saldo." target="_blank" class="w-full py-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black rounded-2xl transition-all active:scale-95 flex items-center justify-center gap-3 group">
            <i class="fab fa-whatsapp text-green-500 group-hover:scale-110 transition-transform"></i>
            KONTAK OWNER (MANUAL)
        </a>

        <div class="mt-8 grid grid-cols-2 gap-4 w-full">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-tight mb-1">Setoran Terakhir</p>
                <p class="font-bold text-slate-800 text-sm">Rp {{ number_format($lastDeposit->amount ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-tight mb-1">Total Transaksi</p>
                <p class="font-bold text-slate-800 text-sm">{{ $totalTransactions ?? 0 }} Trx</p>
            </div>
        </div>
    </div>
</div>
@endsection
