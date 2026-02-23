@extends('p3pot.layouts.app')

@section('title', 'Settings')
@section('header_title', 'Pengaturan P3POT')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-cog text-xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">General Settings</h3>
                <p class="text-sm text-slate-400 font-medium">Konfigurasi dasar aplikasi P3POT</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="p-4 rounded-xl border border-blue-100 bg-blue-50/50">
                <p class="text-sm font-bold text-blue-700 mb-1">Coming Soon</p>
                <p class="text-xs text-blue-600 leading-relaxed">Fitur pengaturan profil dan sistem akan tersedia segera di sini.</p>
            </div>
        </div>
    </div>
</div>
@endsection
