@extends('layouts.app')

@section('title', 'Topup Saldo')
@section('header_title', 'Topup Saldo')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-8 bg-indigo-600 rounded-full"></div>
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Topup Saldo Otomatis</h2>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest leading-none mt-1">Pilih metode pembayaran (Powered by Pakasir).</p>
            </div>
        </div>
    </div>

    <form action="{{ route('reseller.topup.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Amount Input -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Nominal Topup</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-slate-400">Rp</span>
                        <input type="number" name="amount" id="amountInput" min="10000" step="1000" value="50000" required
                            class="w-full pl-12 pr-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-black text-2xl text-slate-800">
                    </div>
                    <p class="mt-4 text-[10px] text-slate-400 font-bold uppercase tracking-tight italic">Minimal topup Rp 10.000</p>
                    
                    <div class="mt-8 grid grid-cols-2 gap-2">
                        <button type="button" onclick="setAmount(20000)" class="py-2 px-3 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl text-xs font-black text-slate-600 border border-slate-100 transition-all">20rb</button>
                        <button type="button" onclick="setAmount(50000)" class="py-2 px-3 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl text-xs font-black text-slate-600 border border-slate-100 transition-all">50rb</button>
                        <button type="button" onclick="setAmount(100000)" class="py-2 px-3 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl text-xs font-black text-slate-600 border border-slate-100 transition-all">100rb</button>
                        <button type="button" onclick="setAmount(250000)" class="py-2 px-3 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl text-xs font-black text-slate-600 border border-slate-100 transition-all">250rb</button>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Pilih Metode Pembayaran</label>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($channels as $channel)
                        <label class="relative cursor-pointer h-full block group">
                            <input type="radio" name="method" value="{{ $channel['code'] }}" class="peer sr-only" required {{ $loop->first ? 'checked' : '' }}>
                            
                            <!-- Main Card Container -->
                            <div class="card-container flex flex-col p-5 bg-white border-2 border-slate-100 rounded-3xl transition-all h-full relative shadow-sm hover:shadow-md peer-checked:bg-indigo-600 peer-checked:border-indigo-600 peer-checked:shadow-indigo-100">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="icon-box w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-xl text-indigo-500 transition-colors peer-checked:bg-white/20 peer-checked:text-white">
                                         <i class="fas fa-{{ str_contains($channel['code'], 'qris') ? 'qrcode' : 'university' }}"></i>
                                    </div>
                                    <div class="check-icon opacity-0 peer-checked:opacity-100 transition-opacity">
                                        <i class="fas fa-check-circle text-white text-xl"></i>
                                    </div>
                                </div>
                                
                                <span class="card-title text-xs font-black text-slate-800 uppercase tracking-tight peer-checked:text-white transition-colors">{{ $channel['name'] }}</span>
                                
                                <div class="card-footer mt-auto pt-4 flex justify-between items-center border-t border-slate-50 peer-checked:border-white/10 transition-colors">
                                    <span class="text-[10px] font-bold text-slate-400 peer-checked:text-indigo-100 italic transition-colors">Pengecekan Otomatis</span>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="mt-12">
                        <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-slate-900 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center gap-3 group uppercase">
                            LANJUTKAN KE PEMBAYARAN
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function setAmount(val) {
        document.getElementById('amountInput').value = val;
    }
</script>

<style>
    /* Manual style override for selection because build might be cached */
    .peer:checked + .card-container {
        background-color: #4f46e5 !important; /* indigo-600 */
        border-color: #4f46e5 !important;
        box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.1), 0 10px 10px -5px rgba(79, 70, 229, 0.04);
    }
    
    .peer:checked + .card-container .icon-box {
        background-color: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
    }
    
    .peer:checked + .card-container .check-icon {
        opacity: 1 !important;
    }
    
    .peer:checked + .card-container .card-title {
        color: white !important;
    }
    
    .peer:checked + .card-container .card-footer {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    
    .peer:checked + .card-container .card-footer span {
        color: #e0e7ff !important; /* indigo-100 */
    }
</style>
@endsection
