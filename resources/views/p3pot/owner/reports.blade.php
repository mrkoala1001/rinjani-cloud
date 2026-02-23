@extends('p3pot.layouts.app')

@section('title', 'My Reports')
@section('header_title', 'History Laporan Saya')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
        <h3 class="font-black text-slate-800 uppercase tracking-tight text-sm">Daftar Laporan Terkirim</h3>
    </div>
    <div class="p-12 text-center">
        <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-history text-2xl"></i>
        </div>
        <p class="text-slate-400 italic">Belum ada laporan yang dikirimkan.</p>
    </div>
</div>
@endsection
