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
                {{ 
                    $user->role === 'isp' ? 'SUPERDUPER ADMIN' : 
                    ($user->role === 'builder' ? 'BUILDER' : 
                    ($user->role === 'mitra-reseller' ? 'MITRA RESELLER' : 
                    ($user->role === 'reseller' ? 'RESELLER' : 'OWNER'))) 
                }}
            </div>
            @if($user->notes)
            <p class="mt-4 text-center max-w-lg text-blue-50 text-sm italic border-t border-white/20 pt-4">
                "{{ $user->notes }}"
            </p>
            @endif
        </div>

        <div class="p-8">
            @if(auth()->user()->role !== 'owner-member')
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8 flex items-start gap-3">
                <i class="fas fa-info-circle text-amber-500 mt-1"></i>
                <p class="text-xs font-bold text-amber-700 leading-relaxed uppercase tracking-tight">
                    Jika anda ingin mengedit data silahkan hubungi ISP anda.
                </p>
            </div>
            @endif

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4 flex items-start gap-3">
                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                <p class="text-xs font-bold text-green-700 leading-relaxed uppercase tracking-tight">
                    {{ session('success') }}
                </p>
            </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4 flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 mt-1"></i>
                    <ul class="text-xs font-bold text-red-700 leading-relaxed uppercase tracking-tight list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-4">
                    <i class="fas fa-user mr-3 text-blue-500"></i> Informasi Profil
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black mb-2 uppercase tracking-widest">
                            Nama Perusahaan / Akun
                        </label>
                        <input name="name" class="w-full bg-slate-100 border-slate-200 rounded-xl text-sm py-3 px-4 shadow-sm text-slate-500 {{ auth()->user()->role !== 'owner-member' ? 'cursor-not-allowed font-bold' : '' }}" type="text" value="{{ old('name', $user->name) }}" {{ auth()->user()->role !== 'owner-member' ? 'readonly disabled' : '' }}>
                    </div>

                    <div>
                        <label class="block text-slate-500 text-[10px] font-black mb-2 uppercase tracking-widest">
                            Username
                        </label>
                        <input name="username" class="w-full bg-slate-100 border-slate-200 rounded-xl text-sm py-3 px-4 shadow-sm text-slate-500 {{ auth()->user()->role !== 'owner-member' ? 'cursor-not-allowed font-bold' : '' }}" type="text" value="{{ old('username', $user->username) }}" {{ auth()->user()->role !== 'owner-member' ? 'readonly disabled' : '' }}>
                    </div>

                    <div>
                        <label class="block text-slate-500 text-[10px] font-black mb-2 uppercase tracking-widest">
                            Email Alamat
                        </label>
                        <input name="email" class="w-full bg-slate-100 border-slate-200 rounded-xl text-sm py-3 px-4 shadow-sm text-slate-500 {{ auth()->user()->role !== 'owner-member' ? 'cursor-not-allowed font-bold' : '' }}" type="email" value="{{ old('email', $user->email) }}" {{ auth()->user()->role !== 'owner-member' ? 'readonly disabled' : '' }}>
                    </div>

                    <div>
                        <label class="block text-slate-500 text-[10px] font-black mb-2 uppercase tracking-widest">
                            No. WhatsApp
                        </label>
                        <input name="whatsapp" class="w-full bg-slate-100 border-slate-200 rounded-xl text-sm py-3 px-4 shadow-sm text-slate-500 {{ auth()->user()->role !== 'owner-member' ? 'cursor-not-allowed font-bold' : '' }}" type="text" value="{{ old('whatsapp', $user->whatsapp) }}" {{ auth()->user()->role !== 'owner-member' ? 'readonly disabled' : '' }}>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-slate-500 text-[10px] font-black mb-2 uppercase tracking-widest">
                            Lokasi / Kota
                        </label>
                        <input name="location" class="w-full bg-slate-100 border-slate-200 rounded-xl text-sm py-3 px-4 shadow-sm text-slate-500 {{ auth()->user()->role !== 'owner-member' ? 'cursor-not-allowed font-bold' : '' }}" type="text" value="{{ old('location', $user->location) }}" {{ auth()->user()->role !== 'owner-member' ? 'readonly disabled' : '' }}>
                    </div>
                </div>

                <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-4">
                    <i class="fas fa-server mr-3 text-emerald-500"></i> Server & Koneksi
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-2">
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black mb-2 uppercase tracking-widest">
                            DNS / Link Login
                        </label>
                        <input name="dns" class="w-full bg-slate-100 border-slate-200 rounded-xl text-sm py-3 px-4 shadow-sm text-slate-500 {{ auth()->user()->role !== 'owner-member' ? 'cursor-not-allowed font-mono font-bold' : '' }}" type="text" value="{{ old('dns', $user->dns) }}" {{ auth()->user()->role !== 'owner-member' ? 'readonly disabled' : '' }}>
                    </div>
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black mb-2 uppercase tracking-wide">
                            Winbox (Host/IP)
                        </label>
                        <input name="winbox" class="w-full bg-slate-100 border-slate-200 rounded-xl text-sm py-3 px-4 shadow-sm text-slate-500 {{ auth()->user()->role !== 'owner-member' ? 'cursor-not-allowed font-mono font-bold' : '' }}" type="text" value="{{ old('winbox', $user->winbox) }}" {{ auth()->user()->role !== 'owner-member' ? 'readonly disabled' : '' }}>
                    </div>
                    <div>
                        <label class="block text-slate-500 text-[10px] font-black mb-2 uppercase tracking-wide">
                            IP API
                        </label>
                        <input name="ip_api" class="w-full bg-slate-100 border-slate-200 rounded-xl text-sm py-3 px-4 shadow-sm text-slate-500 {{ auth()->user()->role !== 'owner-member' ? 'cursor-not-allowed font-mono font-bold' : '' }}" type="text" value="{{ old('ip_api', $user->ip_api) }}" {{ auth()->user()->role !== 'owner-member' ? 'readonly disabled' : '' }}>
                    </div>
                </div>
                
                @if(auth()->user()->role === 'owner-member')
                <div class="flex items-center justify-end mt-8 pt-6 border-t border-slate-100">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl focus:outline-none focus:shadow-outline transition flex items-center shadow-lg">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
