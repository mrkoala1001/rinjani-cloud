@extends('layouts.app')

@section('title', 'Instruksi Pembayaran')
@section('header_title', 'Instruksi Pembayaran')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
        <div class="p-8 md:p-12">
            <div class="flex flex-col md:flex-row justify-between items-start gap-8">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-6">
                        @if($topup->status === 'PAID')
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-wider border border-emerald-100">Pembayaran Sukses</span>
                        @else
                            <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-black uppercase tracking-wider border border-amber-100">Menunggu Pembayaran</span>
                        @endif
                        <span class="text-[10px] text-slate-400 font-bold tracking-widest uppercase">Ref: {{ $topup->merchant_ref }}</span>
                    </div>
                    
                    <h2 class="text-3xl font-black text-slate-900 leading-tight mb-2">
                        {{ $topup->status === 'PAID' ? 'Topup Berhasil' : 'Selesaikan Pembayaran Anda' }}
                    </h2>
                    <p class="text-slate-500 font-medium">
                        {{ $topup->status === 'PAID' ? 'Saldo Anda telah berhasil ditambahkan.' : 'Silakan lakukan pembayaran sesuai dengan rincian di bawah ini.' }}
                    </p>
                </div>

                <div class="w-full md:w-auto p-6 bg-slate-50 rounded-3xl border border-slate-100 text-center">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 text-center">Total Bayar</p>
                    <h3 class="text-3xl font-black text-indigo-600 tracking-tighter">Rp {{ number_format($topup->total, 0, ',', '.') }}</h3>
                </div>
            </div>

            @if($topup->status !== 'PAID')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mt-12 pt-12 border-t border-slate-50">
                <!-- Payment Info -->
                <div class="space-y-8">
                    <div>
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Metode Pembayaran</h4>
                        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 uppercase font-black text-slate-800 text-sm">
                             <i class="fas fa-wallet text-indigo-500"></i>
                             {{ str_replace('_', ' ', $topup->payment_method) }}
                        </div>
                    </div>

                    @php
                        $isQris = str_contains(strtolower($topup->payment_method), 'qris');
                    @endphp

                    <div>
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">{{ $isQris ? 'Scan QR Code' : 'Nomor Virtual Account' }}</h4>
                        @if($isQris)
                            <div class="flex flex-col items-center">
                                <div class="p-6 bg-white rounded-3xl border-4 border-slate-100 shadow-sm inline-block">
                                    <div id="qrisContainer" data-qr="{{ $detail['payment_number'] }}"></div>
                                </div>
                                <p class="mt-4 text-[10px] text-slate-400 font-bold uppercase tracking-tight italic">Scan menggunakan aplikasi pembayaran Anda</p>
                            </div>
                        @else
                            <div class="p-6 bg-slate-900 rounded-3xl text-center relative overflow-hidden group">
                                <div class="absolute inset-0 bg-gradient-to-r from-indigo-600/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 relative z-10">KODE PEMBAYARAN</p>
                                <h3 class="text-2xl font-black text-white tracking-[0.1em] relative z-10" id="payCode">{{ $detail['payment_number'] }}</h3>
                                <button onclick="copyCode()" class="mt-4 px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-[10px] font-black rounded-xl transition-all relative z-10 flex items-center gap-2 mx-auto">
                                    <i class="fas fa-copy"></i> SALIN KODE
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Steps -->
                <div>
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Cara Pembayaran</h4>
                    <div class="space-y-4">
                        <div class="flex gap-4 p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                             <span class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-black text-xs shrink-0">1</span>
                             <p class="text-xs text-indigo-900 font-bold leading-relaxed">Pilih menu pembayaran atau scan pada aplikasi m-banking / e-wallet Anda.</p>
                        </div>
                        <div class="flex gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                             <span class="w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-black text-xs shrink-0">2</span>
                             <p class="text-xs text-slate-600 font-bold leading-relaxed">Masukkan atau pastikan nominal pembayaran tepat <b>Rp {{ number_format($topup->total, 0, ',', '.') }}</b>.</p>
                        </div>
                        <div class="flex gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                             <span class="w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-black text-xs shrink-0">3</span>
                             <p class="text-xs text-slate-600 font-bold leading-relaxed">Selesaikan transaksi. Saldo akan otomatis bertambah dalam hitungan detik setelah sukses.</p>
                        </div>
                    </div>

                    <div class="mt-12 p-6 bg-slate-50 rounded-3xl border border-dashed border-slate-300">
                         <div class="flex items-center gap-3 text-slate-400 uppercase italic font-black text-[9px] mb-2">
                             <i class="fas fa-info-circle"></i>
                             Catatan Penting
                         </div>
                         <p class="text-[10px] text-slate-500 font-medium leading-relaxed">Harap selesaikan pembayaran sebelum waktu kedaluwarsa. Jika dana sudah terpotong namun saldo belum masuk, hubungi admin dengan melampirkan bukti bayar.</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="mt-12 p-8 bg-indigo-600 rounded-3xl text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl shadow-indigo-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-xl">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h4 class="font-black uppercase tracking-tight">Sistem Aman & Otomatis</h4>
                        <p class="text-xs font-medium opacity-80">Didukung oleh Pakasir Payment Gateway.</p>
                    </div>
                </div>
                <a href="{{ route('owner.reseller.balance') }}" class="px-8 py-3 bg-white text-indigo-600 font-black rounded-xl text-xs hover:bg-slate-50 transition-all uppercase shadow-lg">Cek Saldo Saya</a>
            </div>
        </div>
    </div>
</div>

@if(!$isQris && $topup->status !== 'PAID')
<script>
    function copyCode() {
        const text = document.getElementById('payCode').innerText;
        navigator.clipboard.writeText(text);
        alert('Kode berhasil disalin!');
    }
</script>
@endif

@if($isQris && $topup->status !== 'PAID')
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script>
    window.onload = function() {
        const qrData = document.getElementById('qrisContainer').getAttribute('data-qr');
        if (qrData) {
            const qr = qrcode(0, 'M');
            qr.addData(qrData);
            qr.make();
            document.getElementById('qrisContainer').innerHTML = qr.createImgTag(6, 4);
        }
    }
</script>
@endif

<style>
    #qrisContainer img {
        display: block;
        width: 100%;
        height: auto;
    }
</style>
@endsection
