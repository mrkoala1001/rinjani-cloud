@extends('p3pot.layouts.app')

@section('title', 'Payment Gateway Settings')
@section('header_title', 'Payment Gateway Settings')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="glass-card p-8 rounded-3xl">
        <div class="mb-8 text-center">
            <div class="w-16 h-16 bg-indigo-500/20 rounded-2xl flex items-center justify-center text-indigo-400 mx-auto mb-4">
                <i class="fas fa-credit-card text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-white">Configure Gateway</h2>
            <p class="text-slate-400">Settings for your automated billing system</p>
        </div>

        <form action="{{ route('p3pot.owner.payment_gateway.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-400 mb-2">Provider</label>
                <select name="provider" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    <option value="xendit">Xendit</option>
                    <option value="midtrans">Midtrans</option>
                    <option value="tripay">Tripay</option>
                </select>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-400 mb-2">Merchant ID</label>
                    <input type="text" name="merchant_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition" placeholder="Enter Merchant ID">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-400 mb-2">Environment</label>
                    <select class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        <option value="sandbox">Sandbox / Testing</option>
                        <option value="production">Production / Live</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-400 mb-2">Secret API Key</label>
                <div class="relative">
                    <input type="password" name="api_key" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition" autocomplete="off" placeholder="xnd_development_...">
                    <button type="button" class="absolute right-4 top-3.5 text-slate-500">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-4 rounded-2xl shadow-xl shadow-indigo-600/30 transition transform hover:-translate-y-1 active:scale-95">
                    Save Configuration
                </button>
            </div>
        </form>
    </div>
    
    <div class="mt-8 p-6 bg-indigo-500/5 rounded-2xl border border-indigo-500/10 flex gap-4 items-start">
        <i class="fas fa-info-circle text-indigo-400 mt-1"></i>
        <p class="text-sm text-slate-400 leading-relaxed">
            Ensure your Webhook URL in the payment gateway dashboard is set to <span class="text-indigo-400 font-mono">https://p3pot.depootcom.com/webhook/payment</span>.
        </p>
    </div>
</div>
@endsection
