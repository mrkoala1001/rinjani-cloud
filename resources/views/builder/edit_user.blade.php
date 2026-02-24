@extends('layouts.app')

@section('title', 'Edit User - Koala Builder')
@section('header_title', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumbs -->
    <nav class="flex mb-6 text-xs font-bold uppercase tracking-widest text-slate-400" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('builder.dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right mx-2 text-[8px]"></i>
                    <span class="text-slate-600">Edit User</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="p-8 bg-slate-900 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-indigo-500 rounded-full blur-3xl opacity-20"></div>
            <div class="relative z-10 flex items-center gap-5">
                <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center text-2xl font-black border border-white/20">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-black leading-tight">{{ $user->name }}</h2>
                    <p class="text-indigo-300 text-sm font-bold uppercase tracking-widest mt-1">{{ $user->role }} Account</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            <form action="{{ route('builder.user.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Info Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Nama Lengkap</label>
                            <div class="relative">
                                <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition duration-300 text-slate-700 font-bold">
                            </div>
                            @error('name') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Username Login</label>
                            <div class="relative">
                                <i class="fas fa-user-circle absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                                    class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition duration-300 text-slate-700 font-bold">
                            </div>
                            @error('username') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Alamat Email (Opsional)</label>
                            <div class="relative">
                                <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition duration-300 text-slate-700 font-bold">
                            </div>
                            @error('email') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Akses Aplikasi (Origin)</label>
                        <select name="origin" class="w-full px-4 py-3.5 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition duration-300 text-slate-700 font-bold">
                            <option value="hotpot" {{ old('origin', $user->origin ?? 'hotpot') == 'hotpot' ? 'selected' : '' }}>Hotpot (Only)</option>
                            <option value="p3pot" {{ old('origin', $user->origin) == 'p3pot' ? 'selected' : '' }}>P3POT (Only)</option>
                            <option value="blog" {{ old('origin', $user->origin) == 'blog' ? 'selected' : '' }}>Blog / Landing</option>
                            <option value="semua" {{ old('origin', $user->origin) == 'semua' ? 'selected' : '' }}>Semua (Hotpot + P3POT + Blog)</option>
                        </select>
                        @error('origin') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <!-- Security Section -->
                    <div class="pt-4 mt-4 border-t border-slate-50">
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Password Baru (Kosongkan jika tidak diubah)</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="password" name="password" autocomplete="new-password"
                                class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition duration-300 text-slate-700 font-bold"
                                placeholder="Min. 8 karakter">
                        </div>
                        @error('password') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <!-- Status Section -->
                    <div class="pt-4 mt-4 border-t border-slate-50">
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-4 ml-1">Status Keanggotaan</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative group cursor-pointer">
                                <input type="radio" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="peer sr-only">
                                <div class="p-4 rounded-2xl border-2 border-slate-100 bg-white peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition duration-300">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-slate-800">Aktif</div>
                                            <div class="text-[10px] text-slate-500 font-bold">Akses normal</div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <label class="relative group cursor-pointer">
                                <input type="radio" name="is_active" value="0" {{ !$user->is_active ? 'checked' : '' }} class="peer sr-only">
                                <div class="p-4 rounded-2xl border-2 border-slate-100 bg-white peer-checked:border-rose-500 peer-checked:bg-rose-50 transition duration-300">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                                            <i class="fas fa-ban"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-slate-800">Suspend</div>
                                            <div class="text-[10px] text-slate-500 font-bold">Blokir akses</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="pt-6 flex gap-3">
                        <a href="{{ route('builder.dashboard') }}" class="flex-1 py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black rounded-2xl transition duration-300 text-center uppercase tracking-widest text-xs">
                            Batalkan
                        </a>
                        <button type="submit" class="flex-[2] py-4 bg-indigo-600 hover:bg-slate-900 text-white font-black rounded-2xl shadow-lg shadow-indigo-200 hover:shadow-none transition duration-300 uppercase tracking-widest text-xs">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
