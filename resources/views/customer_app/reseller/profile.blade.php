@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-6">
    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100">
        <div class="text-center mb-8">
            <div class="inline-flex w-20 h-20 bg-gradient-to-tr from-indigo-500 to-purple-600 rounded-3xl items-center justify-center text-white text-3xl font-black shadow-xl mb-4">
                {{ substr($customer->name, 0, 1) }}
            </div>
            <h3 class="text-xl font-black text-slate-800 tracking-tight">{{ $customer->name }}</h3>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">{{ $customer->type }} CLIENT</p>
        </div>

        <form action="{{ route('customer_app.reseller.profile.update') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ $customer->name }}" class="w-full bg-slate-50 border-none rounded-2xl py-4 flex items-center px-4 font-bold text-slate-700 text-sm focus:ring-2 focus:ring-indigo-500 transition-all" required>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Ganti Password App</label>
                <input type="password" name="app_password" placeholder="Kosongkan jika tidak ingin ganti" class="w-full bg-slate-50 border-none rounded-2xl py-4 flex items-center px-4 font-bold text-slate-700 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>
            
            <button type="submit" class="w-full bg-slate-900 text-white font-black py-4 rounded-2xl shadow-lg active:scale-95 transition-all text-sm tracking-widest uppercase">
                SIMPAN PERUBAHAN
            </button>
        </form>
    </div>

    <div class="pb-10">
        <a href="{{ route('customer_app.dashboard') }}" class="flex items-center justify-center gap-2 text-slate-400 font-bold text-xs hover:text-indigo-600 transition-all">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
