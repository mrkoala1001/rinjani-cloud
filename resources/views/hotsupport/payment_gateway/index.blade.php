@extends('layouts.app')

@section('title', 'Konfigurasi Pakasir')
@section('header_title', 'Konfigurasi Pakasir')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
        <div class="p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                        <div class="p-3 bg-indigo-500 rounded-2xl shadow-lg shadow-indigo-200">
                            <i class="fas fa-credit-card text-white"></i>
                        </div>
                        <span>Pakasir Configuration</span>
                    </h2>
                    <p class="text-slate-500 font-medium mt-2">Atur integrasi Pakasir Anda untuk memungkinkan reseller melakukan topup otomatis.</p>
                </div>
                
                @if($config && $config->is_active)
                <div class="flex items-center gap-2 px-4 py-2 bg-green-50 rounded-full border border-green-100">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="text-xs font-bold text-green-700 uppercase tracking-wider">Gateway Aktif</span>
                </div>
                @else
                <div class="flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-full border border-slate-200">
                    <span class="relative flex h-3 w-3">
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-slate-400"></span>
                    </span>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Non-Aktif</span>
                </div>
                @endif
            </div>

            <form action="{{ route('hotsupport.payment-gateway.update') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Project Slug -->
                    <div class="space-y-4">
                        <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                            <i class="fas fa-id-badge mr-2 text-indigo-500"></i> Project Slug / Domain
                        </label>
                        <input type="text" name="merchant_code" value="{{ $config->merchant_code ?? '' }}" required
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-slate-800" placeholder="Contoh: depodomain">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight italic">Domain proyek Anda di Pakasir tanpa .pakasir.com</p>
                    </div>

                    <!-- API Key -->
                    <div class="space-y-4">
                        <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                            <i class="fas fa-key mr-2 text-indigo-500"></i> API Key
                        </label>
                        <input type="password" name="api_key" value="{{ $config->api_key ?? '' }}" required
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-slate-800" placeholder="Masukkan API Key Pakasir">
                    </div>

                    <!-- Mode Selection -->
                    <div class="space-y-4">
                        <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                            <i class="fas fa-flask mr-2 text-indigo-500"></i> Gateway Mode
                        </label>
                        <select name="mode" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-slate-800">
                             <option value="sandbox" {{ ($config->mode ?? '') == 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                             <option value="production" {{ ($config->mode ?? '') == 'production' ? 'selected' : '' }}>Production (Live)</option>
                        </select>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight italic">Gunakan Sandbox untuk uji coba sistem.</p>
                    </div>

                    <!-- Is Active Toggle -->
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-3 cursor-pointer select-none">
                            <input type="checkbox" name="is_active" value="1" {{ ($config->is_active ?? false) ? 'checked' : '' }} class="w-6 h-6 rounded-lg text-indigo-600 border-slate-300 focus:ring-indigo-500 transition-all">
                            <span class="text-sm font-black text-slate-700 uppercase tracking-wider">Aktifkan Payment Gateway</span>
                        </label>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-slate-100">
                    <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                        <i class="fas fa-save"></i>
                        SIMPAN KONFIGURASI
                    </button>
                </div>
            </form>

            <div class="mt-8 p-6 bg-slate-50 rounded-3xl border border-slate-100">
                <h4 class="font-black text-slate-900 uppercase text-xs tracking-widest mb-3 flex items-center gap-2">
                    <i class="fas fa-link text-indigo-500"></i> Webhook URL
                </h4>
                <p class="text-xs text-slate-500 font-medium mb-3">Copy URL ini dan tempel di pengaturan Webhook pada dashboard Pakasir Anda:</p>
                <div class="bg-white p-4 rounded-xl border border-slate-200 font-mono text-xs text-indigo-600 break-all">
                    {{ url('/topup/callback') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
