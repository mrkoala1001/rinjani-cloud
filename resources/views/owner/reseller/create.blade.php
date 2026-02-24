@extends('layouts.app')

@section('title', 'Tambah Reseller')
@section('header_title', 'Tambah Reseller Baru')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
        <h3 class="font-black text-slate-800 flex items-center gap-2">
            <i class="fas fa-user-plus text-blue-500"></i>
            Form Data Reseller
        </h3>
        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Isi detail akun untuk reseller baru</p>
    </div>
    
    <form action="{{ route('owner.reseller.store') }}" method="POST" class="p-8">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="space-y-2">
                <label class="block text-slate-700 text-sm font-black uppercase tracking-wider" for="name">
                    Nama Reseller
                </label>
                <input class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium" 
                    id="name" type="text" name="name" placeholder="Contoh: Budi Voucher" required>
            </div>

            <div class="space-y-2">
                <label class="block text-slate-700 text-sm font-black uppercase tracking-wider" for="username">
                    Username
                </label>
                <input class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium" 
                    id="username" type="text" name="username" placeholder="budivoucher" required>
            </div>

            <div class="space-y-2">
                <label class="block text-slate-700 text-sm font-black uppercase tracking-wider" for="password">
                    Password
                </label>
                <input class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium" 
                    id="password" type="text" name="password" placeholder="Min. 4 karakter" required>
                <p class="text-[10px] text-slate-400 font-bold italic">Password akan dienkripsi secara aman.</p>
            </div>

            <div class="space-y-2">
                <label class="block text-slate-700 text-sm font-black uppercase tracking-wider" for="location">
                    Lokasi / Alamat
                </label>
                <input class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium" 
                    id="location" type="text" name="location" placeholder="Contoh: Jl. Merdeka No. 10">
            </div>

            <div class="space-y-2">
                <label class="block text-slate-700 text-sm font-black uppercase tracking-wider" for="email">
                    Email (Opsional)
                </label>
                <input class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium" 
                    id="email" type="email" name="email" placeholder="email@reseller.com">
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="block text-slate-700 text-sm font-black uppercase tracking-wider" for="whatsapp">
                    WhatsApp (Opsional)
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">+62</span>
                    <input class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium" 
                        id="whatsapp" type="text" name="whatsapp" placeholder="8xxxxxxxxxx">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-8 pt-6 border-t border-slate-100">
            <a href="{{ route('owner.reseller.index') }}" class="text-slate-400 hover:text-slate-600 font-black text-sm uppercase tracking-widest transition-colors">
                Batal
            </a>
            <button class="bg-slate-900 hover:bg-black text-white font-black py-4 px-8 rounded-xl shadow-lg shadow-slate-200 transition-all active:scale-95 flex items-center gap-2" type="submit">
                <span>Simpan Reseller</span>
                <i class="fas fa-check-circle text-blue-400"></i>
            </button>
        </div>
    </form>
</div>
@endsection
