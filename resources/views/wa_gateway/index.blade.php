@extends('layouts.app')

@section('title', 'WhatsApp Gateway')
@section('header_title', 'WhatsApp Gateway')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
        <div class="p-8" x-data="{ 
            active: {{ ($config && $config->is_active) ? 'true' : 'false' }},
            activeTab: 'connection'
        }">
            <!-- Navigation Tabs -->
            <div class="flex items-center gap-4 mb-8 border-b border-slate-100 pb-1 overflow-x-auto sidebar-scroll">
                <button @click="activeTab = 'connection'" 
                    :class="activeTab === 'connection' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-400 hover:text-slate-600'"
                    class="pb-4 px-2 text-xs font-black uppercase tracking-widest transition-all whitespace-nowrap">
                    <i class="fas fa-plug mr-2"></i> API Connection
                </button>
                <button @click="activeTab = 'format'" 
                    :class="activeTab === 'format' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-400 hover:text-slate-600'"
                    class="pb-4 px-2 text-xs font-black uppercase tracking-widest transition-all whitespace-nowrap">
                    <i class="fas fa-envelope mr-2"></i> Format Tagihan
                </button>
                <button @click="activeTab = 'broadcast'" 
                    :class="activeTab === 'broadcast' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-400 hover:text-slate-600'"
                    class="pb-4 px-2 text-xs font-black uppercase tracking-widest transition-all whitespace-nowrap">
                    <i class="fas fa-bullhorn mr-2"></i> Broadcast WA
                </button>
            </div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                        <div class="p-3 bg-green-500 rounded-2xl shadow-lg shadow-green-200">
                            <i class="fab fa-whatsapp text-white"></i>
                        </div>
                        <span x-text="activeTab === 'connection' ? 'WhatsApp API Configuration' : (activeTab === 'format' ? 'Billing Message Format' : 'WhatsApp Broadcast')"></span>
                    </h2>
                    <p class="text-slate-500 font-medium mt-2" x-text="activeTab === 'connection' ? 'Hubungkan sistem Hotpot Anda dengan WhatsApp Gateway pihak ketiga.' : (activeTab === 'format' ? 'Sesuaikan format pesan tagihan yang akan dikirim ke pelanggan.' : 'Kirim pesan massal ke banyak nomor sekaligus menggunakan file CSV.')"></p>
                </div>
                
                @if($config)
                <div x-show="active" class="flex items-center gap-2 px-4 py-2 bg-green-50 rounded-full border border-green-100">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="text-xs font-bold text-green-700 uppercase tracking-wider">Konfigurasi Aktif</span>
                </div>
                <div x-show="!active" class="flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-full border border-slate-200">
                    <span class="relative flex h-3 w-3">
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-slate-400"></span>
                    </span>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Gateway Non-Aktif</span>
                </div>
                @endif
            </div>

            <form action="{{ route('wa_gateway.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Tab: Connection Settings -->
                <div x-show="activeTab === 'connection'" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Provider Selection -->
                        <div class="space-y-4">
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                                <i class="fas fa-server mr-2 text-indigo-500"></i> API Provider
                            </label>
                            <select name="provider" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-slate-800 appearance-none">
                                <option value="fonnte" {{ ($config->provider ?? '') == 'fonnte' ? 'selected' : '' }}>Fonnte (Recommended)</option>
                                <option value="wablas" {{ ($config->provider ?? '') == 'wablas' ? 'selected' : '' }}>Wablas</option>
                                <option value="starsender" {{ ($config->provider ?? '') == 'starsender' ? 'selected' : '' }}>StarSender</option>
                            </select>
                        </div>

                        <!-- API Key -->
                        <div class="space-y-4">
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                                <i class="fas fa-key mr-2 text-indigo-500"></i> API Token / Key
                            </label>
                            <input type="text" name="api_key" value="{{ $config->api_key ?? '' }}" required
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-slate-800">
                        </div>

                        <!-- Sender Number -->
                        <div class="space-y-4">
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                                <i class="fas fa-phone mr-2 text-indigo-500"></i> Nomor Pengirim
                            </label>
                            <input type="text" name="sender_number" value="{{ $config->sender_number ?? '' }}"
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-slate-800"
                                placeholder="628123xxx">
                        </div>

                        <!-- Status Toggle -->
                        <div class="space-y-4">
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                                <i class="fas fa-toggle-on mr-2 text-indigo-500"></i> Status Gateway
                            </label>
                            <div @click="active = !active" 
                                 :class="active ? 'bg-green-50 border-green-200' : 'bg-slate-50 border-slate-100'"
                                 class="flex items-center gap-5 py-4 px-6 rounded-2xl border-2 cursor-pointer hover:scale-[1.01] transition-all select-none shadow-sm h-[60px]">
                                <div :class="active ? 'bg-green-500 shadow-green-200' : 'bg-slate-300 shadow-slate-100'"
                                     class="w-8 h-8 rounded-lg flex items-center justify-center text-white shadow-lg transition-all duration-300">
                                    <i class="fas fa-sm" :class="active ? 'fa-check' : 'fa-times'"></i>
                                </div>
                                <div class="flex flex-col">
                                    <input type="checkbox" name="is_active" value="1" :checked="active" class="sr-only">
                                    <span class="text-[10px] font-black uppercase tracking-widest" :class="active ? 'text-green-700' : 'text-slate-400'" x-text="active ? 'ACTIVE' : 'INACTIVE'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Message Format -->
                <div x-show="activeTab === 'format'" x-transition>
                    <div class="space-y-6">
                        <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100">
                            <h4 class="text-indigo-900 font-black text-sm uppercase tracking-widest mb-4 flex items-center">
                                <i class="fas fa-info-circle mr-2"></i> Petunjuk Penggunaan Placeholder
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white p-3 rounded-xl border border-indigo-100">
                                    <code class="text-indigo-600 font-bold">{name}</code>
                                    <p class="text-[10px] text-slate-500 mt-1">Nama Pelanggan</p>
                                </div>
                                <div class="bg-white p-3 rounded-xl border border-indigo-100">
                                    <code class="text-indigo-600 font-bold">{amount}</code>
                                    <p class="text-[10px] text-slate-500 mt-1">Jumlah Tagihan (Rp)</p>
                                </div>
                                <div class="bg-white p-3 rounded-xl border border-indigo-100">
                                    <code class="text-indigo-600 font-bold">{due_date}</code>
                                    <p class="text-[10px] text-slate-500 mt-1">Tanggal Jatuh Tempo</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                                <i class="fas fa-comment-alt mr-2 text-indigo-500"></i> Template Pesan Tagihan
                            </label>
                            <textarea name="billing_template" rows="10" 
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-medium text-slate-800 leading-relaxed"
                                placeholder="Tulis format pesan di sini...">{{ $config->billing_template ?? "Halo *{name}*,\n\nIni adalah pengingat tagihan internet Anda.\n\n*Detail Tagihan:*\n• Jumlah: *Rp {amount}*\n• Jatuh Tempo: *{due_date}*\n\nMohon segera melakukan pembayaran agar layanan tetap aktif. Terima kasih." }}</textarea>
                            <p class="text-xs text-slate-400 font-medium italic">*Anda boleh menggunakan format WhatsApp (seperti *tebal*, _miring_, atau ~coret~).</p>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'broadcast'" x-transition>
                    <div class="space-y-6">
                        <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 flex items-start gap-4">
                            <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white shrink-0 shadow-lg">
                                <i class="fas fa-file-csv"></i>
                            </div>
                            <div>
                                <h4 class="text-amber-900 font-black text-xs uppercase tracking-widest mb-1">Penting: Format File CSV</h4>
                                <p class="text-xs text-amber-700 font-medium leading-relaxed">Pastikan file CSV Anda memiliki **nomor WhatsApp di kolom pertama**. Contoh isi file:</p>
                                <div class="mt-2 bg-white/50 p-2 rounded-lg font-mono text-[10px] text-amber-800 border border-amber-200/50">
                                    628123456789,Budi<br>
                                    628987654321,Ani
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- CSV Upload -->
                            <div class="space-y-4">
                                <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                                    <i class="fas fa-upload mr-2 text-indigo-500"></i> Upload List (CSV)
                                </label>
                                <div class="relative group">
                                    <input type="file" name="csv_file" accept=".csv" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-slate-800 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                            </div>

                            <!-- Broadcast Message -->
                            <div class="space-y-4">
                                <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                                    <i class="fas fa-comment-dots mr-2 text-indigo-500"></i> Pesan Broadcast
                                </label>
                                <textarea name="broadcast_message" rows="5" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-medium text-slate-800" placeholder="Tulis pesan pengumuman di sini..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-slate-100 flex flex-col sm:flex-row gap-4">
                    <!-- Standard Save Button (For Connection & Format) -->
                    <button type="submit" x-show="activeTab !== 'broadcast'" 
                        class="flex-1 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                        <i class="fas fa-save"></i>
                        SIMPAN SEMUA PERUBAHAN
                    </button>

                    <!-- Broadcast Send Button -->
                    <button type="submit" x-show="activeTab === 'broadcast'" formaction="{{ route('wa_gateway.send_broadcast') }}"
                        class="flex-1 py-4 bg-orange-600 hover:bg-orange-700 text-white font-black rounded-2xl shadow-lg shadow-orange-100 transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                        <i class="fas fa-paper-plane"></i>
                        MULAI KIRIM BROADCAST
                    </button>

                    <button type="submit" x-show="activeTab === 'connection'" formaction="{{ route('wa_gateway.test') }}" class="flex-1 py-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black rounded-2xl transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                        <i class="fas fa-vial"></i>
                        TEST KONEKSI API
                    </button>
                </div>
            </form>

            <!-- Guide Section -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 bg-blue-50 rounded-3xl border border-blue-100 space-y-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h4 class="font-black text-blue-900 uppercase text-xs tracking-widest">Apa itu Fonnte?</h4>
                    <p class="text-xs text-blue-700 font-medium leading-relaxed">Fonnte adalah API Gateway WhatsApp yang memungkinkan Anda mengirim pesan melalui kode program. Sangat stabil dan mudah diatur.</p>
                </div>

                <div class="p-6 bg-purple-50 rounded-3xl border border-purple-100 space-y-3">
                    <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="font-black text-purple-900 uppercase text-xs tracking-widest">Keamanan Data</h4>
                    <p class="text-xs text-purple-700 font-medium leading-relaxed">API Token Anda disimpan dengan aman di database kami. Pastikan untuk tidak membagikan token ini kepada siapa pun.</p>
                </div>

                <div class="p-6 bg-orange-50 rounded-3xl border border-orange-100 space-y-3">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h4 class="font-black text-orange-900 uppercase text-xs tracking-widest">Cara Pakai</h4>
                    <p class="text-xs text-orange-700 font-medium leading-relaxed">Setelah disimpan, sistem akan dapat mengirimkan pesan otomatis untuk tagihan pelanggan, laporan harian, dan notifikasi lainnya.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
