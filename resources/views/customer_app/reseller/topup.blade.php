@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-6">
    <!-- Current Balance Info -->
    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 text-center">
        <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] mb-3">Saldo Saat Ini</p>
        <h3 class="text-4xl font-black text-slate-900 mb-2">Rp {{ number_format($customer->balance, 0, ',', '.') }}</h3>
        <p class="text-[10px] text-green-600 font-bold uppercase tracking-widest leading-none mt-2">Status Akun: AKTIF</p>
    </div>

    <!-- Topup Info -->
    <div class="bg-slate-900 rounded-[2rem] p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl">
            <i class="fas fa-university"></i>
        </div>
        <h4 class="font-black text-sm uppercase tracking-[0.2em] mb-6 decoration-indigo-500 underline decoration-4 underline-offset-8">Instruksi Isi Saldo</h4>
        
        <div class="space-y-6 relative z-10">
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center font-black text-xs shrink-0 mt-1">1</div>
                <div>
                    <p class="text-xs font-bold leading-relaxed opacity-80">Hubungi Administrator/Owner (Mr. Koala) untuk melakukan transfer dana.</p>
                </div>
            </div>
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center font-black text-xs shrink-0 mt-1">2</div>
                <div>
                    <p class="text-xs font-bold leading-relaxed opacity-80">Kirimkan bukti transfer dan sebutkan username app Anda: <span class="text-indigo-400">@{{ session('customer_name') }}</span></p>
                </div>
            </div>
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center font-black text-xs shrink-0 mt-1">3</div>
                <div>
                    <p class="text-xs font-bold leading-relaxed opacity-80">Saldo akan diinput oleh admin dan muncul secara otomatis di dashboard ini.</p>
                </div>
            </div>
        </div>

        <a href="https://wa.me/62817200386" target="_blank" class="w-full bg-green-500 hover:bg-green-600 text-white font-black py-4 rounded-2xl shadow-lg mt-8 transition-all flex items-center justify-center gap-2 group text-sm tracking-widest uppercase">
            <i class="fab fa-whatsapp text-lg"></i>
            CHAT ADMIN SEKARANG
        </a>
    </div>

    <div class="pb-10">
        <a href="{{ route('customer_app.dashboard') }}" class="flex items-center justify-center gap-2 text-slate-400 font-bold text-xs hover:text-indigo-600 transition-all">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
