@extends('layouts.app')

@section('title', 'My Profile')
@section('header_title', 'Profil Akun')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-10 text-white flex flex-col items-center">
            <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center text-4xl font-black shadow-inner mb-4 border-2 border-white/50">
                {{ substr($user->name, 0, 1) }}
            </div>
            <h2 class="text-2xl font-black tracking-tight">{{ $user->name }}</h2>
            <div class="mt-2 inline-flex items-center px-3 py-1 bg-white/20 rounded-full text-xs font-bold uppercase tracking-widest backdrop-blur-sm shadow-sm border border-white/10">
                {{ $user->role === 'isp' ? 'SUPERDUPER ADMIN' : ($user->role === 'builder' ? 'BUILDER' : ($user->role === 'reseller' ? 'RESELLER' : 'OWNER')) }}
            </div>
            @if($user->notes)
            <p class="mt-4 text-center max-w-lg text-blue-100 text-sm italic border-t border-white/20 pt-4">
                "{{ $user->notes }}"
            </p>
            @endif
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="p-8">
            @csrf
            
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-4">
                <i class="fas fa-user-edit mr-3 text-blue-500"></i> Edit Informasi Profil
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative">
                
                <div class="mb-4">
                    <label class="block text-slate-500 text-xs font-bold mb-2 uppercase tracking-wide" for="name">
                        Nama Perusahaan / Akun <span class="text-red-500">*</span>
                    </label>
                    <input class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-4 shadow-sm" id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-slate-500 text-xs font-bold mb-2 uppercase tracking-wide" for="username">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-4 shadow-sm" id="username" type="text" name="username" value="{{ old('username', $user->username) }}" required>
                    @error('username') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-slate-500 text-xs font-bold mb-2 uppercase tracking-wide" for="email">
                        Email Alamat
                    </label>
                    <input class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-4 shadow-sm" id="email" type="email" name="email" value="{{ old('email', $user->email) }}">
                    @error('email') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-slate-500 text-xs font-bold mb-2 uppercase tracking-wide" for="whatsapp">
                        No. WhatsApp
                    </label>
                    <input class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-4 shadow-sm" id="whatsapp" type="text" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}">
                    @error('whatsapp') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4 md:col-span-2">
                    <label class="block text-slate-500 text-xs font-bold mb-2 uppercase tracking-wide" for="location">
                        Lokasi / Kota
                    </label>
                    <input class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-4 shadow-sm" id="location" type="text" name="location" value="{{ old('location', $user->location) }}" placeholder="Contoh: Surabaya, Jawa Timur">
                    @error('location') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <h3 class="text-lg font-black text-slate-800 mt-8 mb-6 flex items-center border-b border-slate-100 pb-4">
                <i class="fas fa-lock mr-3 text-red-500"></i> Keamanan Akun
            </h3>

            <div class="bg-red-50 border border-red-100 rounded-xl p-5 mb-6">
                <label class="block text-slate-700 text-sm font-bold mb-2" for="password">
                    Ganti Password (Opsional)
                </label>
                <input class="w-full bg-white border-red-200 rounded-lg text-sm focus:ring-red-500 focus:border-red-500 py-2.5 px-4 shadow-sm" id="password" type="password" name="password" placeholder="Kosongkan jika tidak ingin ganti">
                <p class="text-xs text-red-400 mt-2 font-medium"><i class="fas fa-info-circle mr-1"></i> Jika diisi, password akan segera diubah. Anda mungkin perlu login ulang menggunakan password baru.</p>
                @error('password') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-md transition-all active:scale-95 flex items-center">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
