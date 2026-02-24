@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-6">
    <!-- Payment Card -->
    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 text-center">
        <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] mb-3">Tagihan Bulan Ini</p>
        <h3 class="text-4xl font-black text-slate-900 mb-6">Rp {{ number_format($customer->bill_amount, 0, ',', '.') }}</h3>
        <button class="w-full bg-slate-900 text-white font-black py-4 rounded-2xl shadow-lg active:scale-95 transition-all text-sm tracking-widest uppercase">
            BAYAR SEKARANG
        </button>
    </div>

    <!-- Usage Details -->
    <div class="grid grid-cols-1 gap-4">
        <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl">
                    <i class="fas fa-wifi"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-800">Paket Internet</h4>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">{{ $customer->device_name }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-indigo-600 font-black text-sm">Active</span>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-xl">
                    <i class="fas fa-server"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-800">IP Management</h4>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">{{ $customer->device_ip }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div class="p-6 bg-amber-50 rounded-[2rem] border border-amber-100 flex gap-4">
        <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <h5 class="text-xs font-black text-amber-800 uppercase tracking-widest mb-1">Pengingat Pembayaran</h5>
            <p class="text-[11px] text-amber-600 font-medium leading-relaxed">Tagihan Anda akan jatuh tempo pada tanggal 5 setiap bulannya. Pastikan pembayaran tepat waktu untuk menghindari isolir otomatis.</p>
        </div>
    </div>
</div>
@endsection
