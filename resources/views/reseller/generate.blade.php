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
                
                <form id="resellerVoucherForm" action="{{ route('voucher.store') }}" method="POST" class="p-8">
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
                        <button type="submit" id="btnSubmit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl shadow-indigo-600/20 transition-all active:scale-95 flex items-center justify-center gap-3 text-lg">
                            <span id="btnText" class="flex items-center justify-center gap-3">
                                <i class="fas fa-play"></i>
                                GENERATE SEKARANG
                            </span>
                            <span id="btnLoading" class="hidden items-center justify-center gap-3">
                                <i class="fas fa-spinner fa-spin"></i>
                                SEDANG MEMPROSES...
                            </span>
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

<!-- Loading Overlay -->
<div id="loadingOverlay" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[100] flex items-center justify-center p-4">
    <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-2xl text-center max-w-sm w-full mx-4 border border-indigo-100 animate-in zoom-in duration-300">
        <div class="relative w-24 h-24 mx-auto mb-8">
            <div class="absolute inset-0 rounded-full border-4 border-indigo-100"></div>
            <div class="absolute inset-0 rounded-full border-4 border-t-indigo-600 animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-bolt text-indigo-600 text-3xl"></i>
            </div>
        </div>
        
        <h3 class="text-2xl font-black text-slate-900 mb-3">Membuat Voucher</h3>
        <p class="text-xs text-slate-500 font-bold uppercase tracking-widest leading-relaxed mb-8">
            Jangan tutup atau refresh halaman ini. Sistem sedang mendaftarkan voucher ke MikroTik Anda.
        </p>
        
        <div class="pt-6 border-t border-slate-100">
            <div class="bg-indigo-50 rounded-2xl p-4 flex items-center gap-4 text-left">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Informasi</p>
                    <p class="text-[10px] text-indigo-400 font-bold leading-tight">Untuk jumlah banyak, proses ini mungkin memakan waktu hingga 1 menit.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        let isSubmitting = false;
        const form = document.getElementById('resellerVoucherForm');
        
        // Helper to parse RouterOS time to seconds (JS version)
        function parseToSeconds(time) {
            if (!time || time === '-') return 0;
            time = time.toLowerCase().trim();
            
            // Colon format hh:mm:ss
            if (time.includes(':')) {
                const parts = time.split(':');
                if (parts.length === 3) return (parseInt(parts[0]) * 3600) + (parseInt(parts[1]) * 60) + parseInt(parts[2]);
                if (parts.length === 2) return (parseInt(parts[0]) * 60) + parseInt(parts[1]);
            }

            let seconds = 0;
            const matches = time.matchAll(/(\d+)([wdhms])/g);
            let hasMatch = false;
            for (const match of matches) {
                hasMatch = true;
                const val = parseInt(match[1]);
                const unit = match[2];
                switch (unit) {
                    case 'w': seconds += val * 604800; break;
                    case 'd': seconds += val * 86400; break;
                    case 'h': seconds += val * 3600; break;
                    case 'm': seconds += val * 60; break;
                    case 's': seconds += val; break;
                }
            }
            return hasMatch ? seconds : (parseInt(time) || 0);
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                const qtyInput = form.querySelector('input[name=qty]');
                const profileSelect = form.querySelector('select[name=profile]');
                const timeLimitInput = form.querySelector('input[name=timelimit]');
                
                // Basic validation
                if (!profileSelect.value || parseInt(qtyInput.value) <= 0) {
                    return;
                }

                // CHECK TIMELIMIT VS VALIDITY
                if (timeLimitInput && timeLimitInput.value) {
                    const timeLimitSec = parseToSeconds(timeLimitInput.value);
                    
                    // Get validity from profiles data
                    const profiles = {{ json_encode($profiles) }};
                    const selectedProf = profiles.find(p => p.name === profileSelect.value);
                    const validity = selectedProf && selectedProf.local_metadata ? selectedProf.local_metadata.validity : '';
                    
                    if (validity) {
                        const validitySec = parseToSeconds(validity);
                        if (timeLimitSec > validitySec && validitySec > 0) {
                            alert('⚠️ Limit Waktu (' + timeLimitInput.value + ') tidak boleh melebihi Masa Aktif Profil (' + validity + ')!');
                            e.preventDefault();
                            return false;
                        }
                    }
                }

                isSubmitting = true;
                
                // Disable everything
                const btn = document.getElementById('btnSubmit');
                const btnText = document.getElementById('btnText');
                const btnLoading = document.getElementById('btnLoading');
                
                btn.disabled = true;
                btn.classList.add('opacity-50', 'pointer-events-none');
                btnText.classList.add('hidden');
                btnLoading.classList.remove('hidden');
                btnLoading.classList.add('flex');

                // Show unclosable overlay
                const overlay = document.getElementById('loadingOverlay');
                overlay.classList.remove('hidden');
                overlay.style.pointerEvents = 'auto'; // Block clicks through overlay
                
                // Extra safety: block all pointer events on body
                document.body.style.pointerEvents = 'none';
                overlay.style.pointerEvents = 'auto'; // except the overlay (though not needed)
                
                return true;
            });
        }
    })();
</script>
@endsection

