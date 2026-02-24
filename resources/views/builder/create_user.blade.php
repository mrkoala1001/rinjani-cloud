@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('builder.dashboard') }}" class="inline-flex items-center text-gray-400 hover:text-white transition-colors mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Kembali
        </a>
        <h1 class="text-3xl font-bold text-white flex items-center gap-3">
             <span class="p-2 bg-indigo-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </span>
            Tambah Akun Baru
        </h1>
    </div>

    <div class="max-w-2xl bg-gray-800 rounded-xl shadow-xl border border-gray-700 overflow-hidden">
        <form action="{{ route('builder.user.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nama Lengkap / Perusahaan</label>
                    <input type="text" name="name" required value="{{ old('name') }}" 
                           class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Username (Login)</label>
                    <input type="text" name="username" required value="{{ old('username') }}" 
                           class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email (Opsional)</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Role Akun</label>
                    <select name="role" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        <option value="isp" {{ old('role') == 'isp' ? 'selected' : '' }}>ISP (Superduper Admin)</option>
                        <option value="builder" {{ old('role') == 'builder' ? 'selected' : '' }}>Builder (Developer)</option>
                        <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner (Mitra)</option>
                    </select>
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Lokasi / Wilayah</label>
                    <input type="text" name="location" value="{{ old('location') }}" 
                           class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                           placeholder="Contoh: Jakarta Selatan">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Akses Aplikasi (Origin)</label>
                    <select name="origin" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        <option value="hotpot" {{ old('origin') == 'hotpot' ? 'selected' : '' }}>Hotpot (Only)</option>
                        <option value="p3pot" {{ old('origin') == 'p3pot' ? 'selected' : '' }}>P3POT (Only)</option>
                        <option value="blog" {{ old('origin') == 'blog' ? 'selected' : '' }}>Blog / Landing</option>
                        <option value="semua" {{ old('origin', 'semua') == 'semua' ? 'selected' : '' }}>Semua (Hotpot + P3POT + Blog)</option>
                    </select>
                    @error('origin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <input type="password" name="password" required autocomplete="new-password" 
                           class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password" 
                           class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Catatan Tambahan</label>
                <textarea name="notes" rows="3" 
                          class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition duration-200">
                    Buat Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
<style>
    body { background-color: #111827; }
</style>
