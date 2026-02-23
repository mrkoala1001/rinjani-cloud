@extends('layouts.app')

@section('title', 'Edit Reseller')
@section('header_title', 'Edit Akun Reseller')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
        <h3 class="font-black text-slate-800 flex items-center gap-2">
            <i class="fas fa-user-edit text-blue-500"></i>
            Edit Data Reseller
        </h3>
        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Ubah informasi akun {{ $reseller->name }}</p>
    </div>
    
    <form action="{{ route('owner.reseller.update', $reseller->id) }}" method="POST" class="p-8">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="space-y-2">
                <label class="block text-slate-700 text-sm font-black uppercase tracking-wider" for="name">
                    Nama Reseller
                </label>
                <input class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium" 
                    id="name" type="text" name="name" value="{{ $reseller->name }}" required>
            </div>

            <div class="space-y-2">
                <label class="block text-slate-700 text-sm font-black uppercase tracking-wider" for="username">
                    Username
                </label>
                <input class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium" 
                    id="username" type="text" name="username" value="{{ $reseller->username }}" required>
            </div>

            <div class="space-y-2">
                <label class="block text-slate-700 text-sm font-black uppercase tracking-wider" for="password">
                    Password Baru (Kosongkan jika tidak diubah)
                </label>
                <input class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium" 
                    id="password" type="text" name="password" placeholder="Min. 4 karakter">
                <p class="text-[10px] text-slate-400 font-bold italic">Biarkan kosong untuk mempertahankan password lama.</p>
            </div>

            <div class="space-y-2">
                <label class="block text-slate-700 text-sm font-black uppercase tracking-wider" for="location">
                    Lokasi / Alamat
                </label>
                <input class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium" 
                    id="location" type="text" name="location" value="{{ $reseller->location }}" placeholder="Contoh: Jl. Merdeka No. 10">
            </div>
        </div>

        <div class="flex items-center justify-between mt-8 pt-6 border-t border-slate-100">
            <a href="{{ route('owner.reseller.index') }}" class="text-slate-400 hover:text-slate-600 font-black text-sm uppercase tracking-widest transition-colors">
                Batal
            </a>
            <button class="bg-slate-900 hover:bg-black text-white font-black py-4 px-8 rounded-xl shadow-lg shadow-slate-200 transition-all active:scale-95 flex items-center gap-2" type="submit">
                <span>Simpan Perubahan</span>
                <i class="fas fa-save text-blue-400"></i>
            </button>
        </div>
    </form>
</div>
@endsection
