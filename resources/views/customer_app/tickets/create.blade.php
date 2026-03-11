@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-6 pb-10">
    <!-- Header Page -->
    <div class="flex items-center gap-4 -mt-10 mb-8 relative z-20 px-2">
        <a href="{{ route('customer_app.tickets') }}" class="w-10 h-10 bg-white shadow-md rounded-xl flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Buat Laporan</h2>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Kami siap membantu Anda</p>
        </div>
    </div>

    <form action="{{ route('customer_app.tickets.store') }}" method="POST" class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm space-y-6">
        @csrf

        <div class="space-y-2">
            <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest ml-1">Subjek Laporan</label>
            <input type="text" name="subject" required 
                   class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:outline-none transition-all placeholder:text-slate-300"
                   placeholder="Contoh: Koneksi Terputus, Internet Lambat, dll">
            @error('subject') <p class="text-[10px] text-rose-500 font-bold ml-1 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="block text-slate-400 text-[10px] font-black uppercase tracking-widest ml-1">Pesan / Detail Masalah</label>
            <textarea name="message" rows="6" required 
                      class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold text-slate-800 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:outline-none transition-all placeholder:text-slate-300"
                      placeholder="Jelaskan kendala Anda secara detail agar kami bisa menangani lebih cepat..."></textarea>
            @error('message') <p class="text-[10px] text-rose-500 font-bold ml-1 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-5 rounded-2xl shadow-xl shadow-indigo-200 active:scale-[0.98] transition-all flex items-center justify-center gap-3 group">
                KIRIM LAPORAN SEKARANG
                <i class="fas fa-paper-plane text-sm group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
            </button>
            <p class="text-center text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-6 bg-slate-50 py-3 rounded-2xl italic">
                <i class="fas fa-info-circle mr-1"></i> Admin akan segera merespon laporan Anda.
            </p>
        </div>
    </form>
</div>
@endsection
