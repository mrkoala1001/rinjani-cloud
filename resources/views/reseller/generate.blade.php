@extends('layouts.app')

@section('title', 'Generate Voucher')
@section('header_title', 'Generate Voucher')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-8 bg-indigo-600 rounded-full"></div>
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Generate Voucher</h2>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest leading-none mt-1">Buat kode voucher baru secara instan</p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-start gap-3 text-red-600 animate-pulse">
            <i class="fas fa-exclamation-circle mt-1"></i>
            <div>
                <p class="font-black text-xs uppercase tracking-widest mb-1">Terjadi Kesalahan</p>
                <ul class="text-xs font-bold space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-50 px-8 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-black text-slate-800 uppercase tracking-wider text-sm flex items-center gap-2">
                <i class="fas fa-magic text-indigo-500"></i>
                Voucher Generator
            </h3>
            <span class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded-full text-[10px] font-black uppercase tracking-widest">Reseller Mode</span>
        </div>
        
        <form action="{{ route('voucher.store') }}" method="POST" class="p-8">
            @csrf
            <input type="hidden" name="reseller_id" value="{{ auth()->id() }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilih Server MikroTik</label>
                        <select name="server" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none bg-white">
                            <option value="all">all</option>
                            @foreach($serverProfiles as $srv)
                                <option value="{{ $srv['name'] ?? '' }}">{{ $srv['name'] ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Mode Username</label>
                        <select name="user_mode" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none bg-white">
                            <option value="up">Username = Password</option>
                            <option value="u+p">Username & Password</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Panjang Kode</label>
                            <input type="number" name="user_length" value="6" min="3" max="12" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-black text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Prefix (Opsional)</label>
                            <input type="text" name="prefix" placeholder="VC-" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-black text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Profile / Paket</label>
                        <select name="profile" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-black text-indigo-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none bg-white">
                            <option value="">-- Pilih Profile --</option>
                            @foreach ($profiles as $prof)
                                <option value="{{ $prof['name'] }}">{{ $prof['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Template Layout</label>
                        <select name="template_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none bg-white">
                            <option value="0">-- Default Setup --</option>
                            @foreach ($templates as $tpl)
                                <option value="{{ $tpl->id }}">{{ $tpl->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-6 bg-indigo-50 rounded-2xl border border-indigo-100">
                        <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-3">Jumlah Voucher (Qty)</label>
                        <div class="flex items-center gap-4">
                            <input type="number" name="qty" value="10" min="1" max="100" class="w-full bg-white border border-indigo-200 rounded-xl px-4 py-4 font-black text-2xl text-slate-800 text-center focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none shadow-sm">
                            <div class="text-xs font-bold text-indigo-400 uppercase tracking-tighter w-20 leading-tight">Voucher Per Batch</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-8">
                <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl shadow-indigo-600/20 transition-all active:scale-95 flex items-center justify-center gap-3 text-lg">
                    <i class="fas fa-play"></i>
                    GENERATE SEKARANG
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
