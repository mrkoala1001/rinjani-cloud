@extends('layouts.app')

@section('title', 'Generate Voucher')
@section('header_title', 'Generate Voucher')

@section('content')
<div class="max-w-5xl mx-auto" x-data="{ 
    selectedProfile: '',
    profiles: {{ json_encode($profiles) }},
    get currentProfile() {
        return this.profiles.find(p => p.name === this.selectedProfile) || null;
    }
}">
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

    <div class="flex flex-col lg:flex-row gap-8 items-start">
        <!-- Dashboard Left: Form -->
        <div class="w-full lg:flex-1">
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
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Karakter</label>
                                    <select name="char_set" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none bg-white">
                                        <option value="mixed">Mixed (a,B,1)</option>
                                        <option value="uppercase">Uppercase (A,B,1)</option>
                                        <option value="lowercase">Lowercase (a,b,1)</option>
                                        <option value="numbers">Numbers (1,2,3)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Limit Waktu</label>
                                    <input type="text" name="timelimit" placeholder="e.g. 1h, 30m" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-black text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Limit Kuota</label>
                                    <input type="text" name="datalimit" placeholder="e.g. 1G, 500M" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-black text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Profile / Paket <span class="text-indigo-500">*</span></label>
                                <select name="profile" x-model="selectedProfile" required class="w-full bg-white border-2 border-indigo-100 rounded-xl px-4 py-3 font-black text-indigo-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
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

        <!-- Dashboard Right: Detail Preview -->
        <div class="w-full lg:w-80 shrink-0">
            <div class="sticky top-6">
                 <!-- Selected Profile Details Card -->
                 <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden" :class="selectedProfile ? 'ring-2 ring-indigo-500' : ''">
                    <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full" :class="selectedProfile ? 'bg-green-500 animate-pulse' : 'bg-slate-300'"></div>
                        <h4 class="font-black text-slate-800 uppercase tracking-widest text-[10px]">Detail Paket Terpilih</h4>
                    </div>
                    <div class="p-6">
                        <div x-show="!selectedProfile" class="text-center py-4">
                            <i class="fas fa-search text-slate-200 text-4xl mb-3"></i>
                            <p class="text-[10px] font-bold text-slate-400 italic">Pilih profil untuk melihat detail</p>
                        </div>

                        <div x-show="selectedProfile && currentProfile" x-cloak class="space-y-4">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Profile</p>
                                <h3 class="text-lg font-black text-indigo-600 truncate" x-text="currentProfile ? currentProfile.name : ''"></h3>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Speed</p>
                                    <p class="text-xs font-black text-slate-700" x-text="currentProfile ? (currentProfile['rate-limit'] || 'Unlimited') : 'Unlimited'"></p>
                                </div>
                                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Masa Aktif</p>
                                    <p class="text-xs font-black text-slate-700" x-text="(currentProfile && currentProfile.local_metadata) ? currentProfile.local_metadata.validity : (currentProfile ? (currentProfile['session-timeout'] || '1d') : '1d')"></p>
                                </div>
                            </div>

                            <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                                <p class="text-[9px] font-black text-green-400 uppercase tracking-widest mb-0.5">Harga Jual</p>
                                <div class="flex items-baseline gap-0.5">
                                    <span class="text-[10px] text-green-500 font-bold">Rp</span>
                                    <span class="text-xl font-black text-green-700" x-text="(currentProfile && currentProfile.local_metadata && currentProfile.local_metadata.selling_price) ? parseInt(currentProfile.local_metadata.selling_price).toLocaleString('id-ID') : '0'"></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                 <div class="px-2 py-1 bg-slate-100 rounded text-slate-600" x-show="currentProfile">
                                     <i class="fas fa-users mr-1"></i>
                                     <span x-text="currentProfile ? (currentProfile['shared-users'] || '1') + ' Device' : '1 Device'"></span>
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection

