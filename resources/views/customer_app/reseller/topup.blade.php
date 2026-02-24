@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-6">
    <!-- Current Balance Info -->
    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 text-center">
        <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] mb-3">Saldo Saat Ini</p>
        <h3 class="text-4xl font-black text-slate-900 mb-2">Rp {{ number_format($customer->balance, 0, ',', '.') }}</h3>
        <p class="text-[10px] text-green-600 font-bold uppercase tracking-widest leading-none mt-2">Status Akun: AKTIF</p>
    </div>

    <!-- Topup Form -->
    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100">
        <h4 class="font-black text-sm uppercase tracking-[0.2em] mb-6 decoration-indigo-500 underline decoration-4 underline-offset-8 text-slate-800">Isi Saldo Otomatis</h4>
        
        <form action="{{ route('customer_app.reseller.topup.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Pilih atau Masukkan Nominal</label>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <button type="button" onclick="setAmount(50000)" class="py-3 px-4 rounded-xl border-2 border-slate-100 text-slate-600 font-bold text-sm hover:border-indigo-500 hover:text-indigo-600 transition-all flex items-center justify-center gap-2">
                        <span>Rp 50rb</span>
                    </button>
                    <button type="button" onclick="setAmount(100000)" class="py-3 px-4 rounded-xl border-2 border-slate-100 text-slate-600 font-bold text-sm hover:border-indigo-500 hover:text-indigo-600 transition-all flex items-center justify-center gap-2">
                        <span>Rp 100rb</span>
                    </button>
                    <button type="button" onclick="setAmount(200000)" class="py-3 px-4 rounded-xl border-2 border-slate-100 text-slate-600 font-bold text-sm hover:border-indigo-500 hover:text-indigo-600 transition-all flex items-center justify-center gap-2">
                        <span>Rp 200rb</span>
                    </button>
                    <button type="button" onclick="setAmount(500000)" class="py-3 px-4 rounded-xl border-2 border-slate-100 text-slate-600 font-bold text-sm hover:border-indigo-500 hover:text-indigo-600 transition-all flex items-center justify-center gap-2">
                        <span>Rp 500rb</span>
                    </button>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-slate-400 font-bold text-sm">Rp</span>
                    </div>
                    <input type="number" name="amount" id="topup-amount" min="10000" step="1000" placeholder="Minimal 10.000" class="w-full bg-slate-50 border-none rounded-2xl py-4 pl-12 pr-4 text-slate-900 font-black text-lg focus:ring-2 focus:ring-indigo-500 shadow-inner" required>
                </div>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2 group">
                BAYAR SEKARANG
                <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
            </button>
        </form>

        <div class="mt-6 flex items-center justify-center gap-2 opacity-50 grayscale hover:grayscale-0 transition-all">
            <img src="https://pakasir.com/assets/img/logo.png" alt="Pakasir" class="h-4" onerror="this.onerror=null; this.src='https://pakasir.com/favicon.ico';">
            <span class="text-[8px] font-black uppercase tracking-widest text-slate-400">Payment by Pakasir</span>
        </div>
    </div>



    <!-- Manual Info -->
    <div class="bg-slate-900 rounded-[2rem] p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl">
            <i class="fas fa-university"></i>
        </div>
        <h4 class="font-black text-sm uppercase tracking-[0.2em] mb-4">Cara Lain (Manual)</h4>
        <p class="text-[10px] font-bold opacity-60 leading-relaxed mb-6">Jika sistem pembayaran otomatis bermasalah, Anda masih bisa menggunakan cara manual dengan menghubungi owner.</p>
        
        <a href="https://wa.me/62817200386" target="_blank" class="w-full bg-green-500/10 hover:bg-green-500/20 text-green-400 border border-green-500/30 font-black py-4 rounded-2xl transition-all flex items-center justify-center gap-2 group text-xs tracking-widest uppercase">
            <i class="fab fa-whatsapp text-lg"></i>
            CHAT MR. KOALA
        </a>
    </div>

    <div class="pb-10">
        <a href="{{ route('customer_app.dashboard') }}" class="flex items-center justify-center gap-2 text-slate-400 font-bold text-[9px] hover:text-indigo-600 transition-all uppercase tracking-widest">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<script>
    function setAmount(val) {
        document.getElementById('topup-amount').value = val;
        // visual feedback
        const btn = event.currentTarget;
        const allBtns = btn.parentElement.querySelectorAll('button');
        allBtns.forEach(b => b.classList.remove('bg-indigo-50', 'border-indigo-500', 'text-indigo-600'));
        btn.classList.add('bg-indigo-50', 'border-indigo-500', 'text-indigo-600');
    }
</script>
@endsection
