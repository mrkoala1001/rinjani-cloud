@extends('layouts.app')

@section('title', 'Generate Voucher')
@section('header_title', 'Generate Voucher')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ 
    selectedProfile: '',
    profiles: {{ json_encode($profiles) }},
    get currentProfile() {
        return this.profiles.find(p => p.name === this.selectedProfile) || null;
    }
}">
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Generator Form -->
        <div class="w-full md:w-3/5">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-t-4 border-blue-600">
                <div class="bg-gray-50 px-4 py-3 border-b flex items-center justify-between">
                    <h3 class="font-bold text-gray-700"><i class="fas fa-magic mr-2"></i>Voucher Generator</h3>
                </div>
                <form id="voucherForm" action="{{ route('voucher.store') }}" method="POST" class="p-4 space-y-4">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <div class="mt-2">
                                @foreach ($errors->all() as $error)
                                    <div class="text-sm shadow-sm">{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Profile Selection -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Pilih Profile Voucher <span class="text-red-500">*</span></label>
                        <select name="profile" x-model="selectedProfile" required 
                                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-blue-600 focus:border-blue-500 focus:outline-none bg-white transition-all">
                            <option value="">-- Pilih Paket / Profile --</option>
                            @foreach ($profiles as $prof)
                                <option value="{{ $prof['name'] }}" {{ old('profile') == $prof['name'] ? 'selected' : '' }}>{{ $prof['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Jumlah Voucher (Qty)</label>
                        <input type="number" name="qty" required min="1" max="1000" value="{{ old('qty', 10) }}" 
                               class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:border-blue-500 focus:outline-none transition-all"
                               placeholder="Contoh: 50">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <!-- User Mode -->
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Mode User</label>
                            <select name="user_mode" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none bg-white">
                                <option value="up" {{ old('user_mode') == 'up' ? 'selected' : '' }}>User=Pass</option>
                                <option value="u+p" {{ old('user_mode') == 'u+p' ? 'selected' : '' }}>User+Pass</option>
                            </select>
                        </div>

                        <!-- Name Length -->
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Panjang Kode</label>
                            <input type="number" name="user_length" value="{{ old('user_length', 6) }}" min="3" max="12"
                                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                         <!-- Character Set -->
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Karakter</label>
                            <select name="char_set" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none bg-white">
                                <option value="mixed">Mixed (a,b,1,2)</option>
                                <option value="uppercase">Uppercase (A,B,1,2)</option>
                                <option value="lowercase">Lowercase (a,b,1,2)</option>
                                <option value="numbers">Numbers (1,2)</option>
                            </select>
                        </div>
                        <!-- Template -->
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Template</label>
                            <select name="template_id" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-purple-600 focus:outline-none bg-white">
                                <option value="0">-- Default View --</option>
                                @foreach ($templates as $tpl)
                                    <option value="{{ $tpl->id }}" {{ old('template_id') == $tpl->id ? 'selected' : '' }}>{{ $tpl->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Hidden Advanced Fields -->
                    <div x-data="{ showAdvanced: false }">
                        <button type="button" @click="showAdvanced = !showAdvanced" class="text-xs text-blue-500 font-bold hover:underline mb-2">
                            <span x-text="showAdvanced ? '- Sembunyikan Opsi Lanjutan' : '+ Tampilkan Opsi Lanjutan'"></span>
                        </button>
                        
                        <div x-show="showAdvanced" x-cloak class="space-y-4 pt-2 border-t border-dashed">
                             <!-- Server -->
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Server Hotspot</label>
                                <select name="server" class="w-full border rounded px-3 py-2 text-sm focus:outline-none bg-white">
                                    <option value="all">all</option>
                                    @foreach($serverProfiles as $srv)
                                        <option value="{{ $srv['name'] ?? '' }}">{{ $srv['name'] ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Prefix Kode</label>
                                <input type="text" name="prefix" value="{{ old('prefix') }}" placeholder="VC-" 
                                       class="w-full border rounded px-3 py-2 text-sm focus:outline-none uppercase">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Limit Waktu</label>
                                    <input type="text" name="timelimit" value="{{ old('timelimit') }}" placeholder="e.g. 1h, 30m" 
                                           class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Limit Data</label>
                                    <input type="text" name="datalimit" value="{{ old('datalimit') }}" placeholder="e.g. 500M" 
                                           class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reseller (Only for Management Roles) -->
                    @if(in_array(auth()->user()->role, ['owner', 'mitra', 'mitra-reseller', 'isp', 'builder']))
                    <div class="pt-2">
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Ditujukan untuk Reseller</label>
                        <select name="reseller_id" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none bg-white">
                            <option value="0">Default (Admin)</option>
                            @foreach ($resellers as $res)
                                <option value="{{ $res->id }}" {{ old('reseller_id') == $res->id ? 'selected' : '' }}>{{ $res->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                        <input type="hidden" name="reseller_id" value="{{ auth()->id() }}">
                    @endif

                    <button type="submit" id="btnSubmit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl transition-all shadow-lg shadow-blue-600/30 mt-4 active:scale-95 disabled:opacity-50">
                        <span id="btnText" class="flex items-center justify-center gap-2">
                            <i class="fas fa-bolt text-yellow-300"></i> GENERATE VOUCHER
                        </span>
                        <span id="btnLoading" class="hidden items-center justify-center gap-2">
                            <i class="fas fa-spinner fa-spin mr-2"></i>SEDANG MEMPROSES...
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Info Card (Details) -->
        <div class="w-full md:w-2/5">
            <div class="sticky top-6 space-y-4">
                <!-- Summary Card -->
                <div class="bg-white rounded-xl shadow-lg border-2" :class="selectedProfile ? 'border-blue-500' : 'border-gray-100'">
                    <div class="p-4 border-b bg-gray-50 flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" :class="selectedProfile ? 'bg-blue-500 animate-pulse' : 'bg-gray-300'"></div>
                        <h4 class="font-bold text-gray-700 uppercase text-xs tracking-widest">Detail Paket Terpilih</h4>
                    </div>
                    
                    <div class="p-6">
                        <template x-if="!selectedProfile">
                            <div class="text-center py-8">
                                <i class="fas fa-info-circle text-gray-300 text-4xl mb-3"></i>
                                <p class="text-sm text-gray-400 font-bold italic">Pilih profil untuk melihat detail harga & kecepatan</p>
                            </div>
                        </template>

                        <template x-if="selectedProfile && currentProfile">
                            <div class="space-y-6">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nama Paket</p>
                                    <h2 class="text-2xl font-black text-slate-800" x-text="currentProfile.name"></h2>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-blue-50 p-3 rounded-xl border border-blue-100">
                                        <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Kecepatan</p>
                                        <p class="text-lg font-black text-blue-700" x-text="currentProfile['rate-limit'] || 'Unlimited'"></p>
                                    </div>
                                    <div class="bg-orange-50 p-3 rounded-xl border border-orange-100">
                                        <p class="text-[10px] font-black text-orange-400 uppercase tracking-widest mb-1">Masa Aktif</p>
                                        <p class="text-lg font-black text-orange-700" x-text="currentProfile.local_metadata ? currentProfile.local_metadata.validity : (currentProfile['session-timeout'] || '1d')"></p>
                                    </div>
                                </div>

                                <div class="bg-indigo-50 p-3 rounded-xl border border-indigo-100">
                                    <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Harga Modal (Potong Saldo)</p>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-indigo-600 font-bold text-xs">Rp</span>
                                        <span class="text-lg font-black text-indigo-700" x-text="currentProfile.local_metadata ? parseInt(currentProfile.local_metadata.price).toLocaleString('id-ID') : '0'"></span>
                                    </div>
                                </div>

                                <div class="bg-green-50 p-4 rounded-xl border-2 border-green-200">
                                    <p class="text-[10px] font-black text-green-500 uppercase tracking-widest mb-1">Harga Jual / Voucher</p>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-green-600 font-black">Rp</span>
                                        <span class="text-3xl font-black text-green-700" x-text="currentProfile.local_metadata ? parseInt(currentProfile.local_metadata.selling_price).toLocaleString('id-ID') : '0'"></span>
                                    </div>
                                </div>

                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="font-bold text-slate-500">Shared / Device:</span>
                                        <span class="font-black text-slate-700" x-text="currentProfile['shared-users'] || '1'"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Tips / Info -->
                <div class="bg-blue-600 rounded-xl p-5 text-white shadow-lg">
                    <h5 class="font-black text-xs uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i class="fas fa-lightbulb"></i> Tips Pembuatan
                    </h5>
                    <ul class="text-xs space-y-2 opacity-90 font-bold leading-relaxed">
                        <li class="flex gap-2"><i class="fas fa-check-circle mt-0.5"></i> Pastikan MikroTik dalam keadaan online.</li>
                        <li class="flex gap-2"><i class="fas fa-check-circle mt-0.5"></i> Gunakan 'Panjang Kode' minimal 6 karakter agar aman.</li>
                        <li class="flex gap-2"><i class="fas fa-check-circle mt-0.5"></i> Profil yang muncul hanya yang sudah disetting oleh ISP.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal / Overlay Loading -->
<div id="loadingOverlay" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[100] flex items-center justify-center p-4">
    <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-2xl text-center max-w-sm w-full mx-4 border border-blue-100 animate-in zoom-in duration-300">
        <div class="relative w-24 h-24 mx-auto mb-8">
            <div class="absolute inset-0 rounded-full border-4 border-blue-100"></div>
            <div class="absolute inset-0 rounded-full border-4 border-t-blue-600 animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-magic text-blue-600 text-3xl"></i>
            </div>
        </div>
        
        <h3 class="text-2xl font-black text-slate-800 mb-3">Memproses Voucher</h3>
        <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest leading-relaxed mb-8">
            Jangan tutup atau refresh halaman ini. Sistem sedang mendaftarkan voucher ke dalam Router MikroTik Anda.
        </p>
        
        <div class="pt-6 border-t border-slate-100">
            <div class="bg-blue-50 rounded-2xl p-4 flex items-center gap-4 text-left">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shrink-0 shadow-lg shadow-blue-200">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Peringatan Penting</p>
                    <p class="text-[10px] text-blue-400 font-bold leading-tight">Untuk jumlah besar (seperti 500+ voucher), proses ini dapat memakan waktu beberapa menit.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        let isSubmitting = false;
        const form = document.getElementById('voucherForm');
        
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
                    
                    // Get validity from profiles data (injected via Alpine/Blade)
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

