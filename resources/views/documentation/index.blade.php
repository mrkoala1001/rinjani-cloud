@extends('layouts.public')

@section('title', 'Dokumentasi - HOT POT')

@section('content')
<div class="pt-32 pb-20 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-slate-900 mb-4">Dokumentasi Sistem</h1>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">Panduan lengkap penggunaan sistem HOT POT untuk memaksimalkan bisnis jaringan Anda.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($docs as $doc)
            <a href="{{ route('documentation.show', $doc->slug) }}" class="bg-white p-8 rounded-2xl shadow-md border border-slate-100 hover:shadow-xl transition transform hover:-translate-y-1 block group">
                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 mb-6 group-hover:bg-indigo-600 group-hover:text-white transition">
                    <i class="fas fa-book-open text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-indigo-600 transition">{{ $doc->title }}</h3>
                <p class="text-slate-500 text-sm line-clamp-3 mb-4">
                    {{ Str::limit(strip_tags(Str::markdown($doc->content)), 120) }}
                </p>
                <div class="flex items-center text-indigo-600 font-bold text-sm">
                    Baca Selengkapnya <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition"></i>
                </div>
            </a>
            @endforeach
        </div>

        @if($docs->isEmpty())
        <div class="text-center py-20">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                <i class="fas fa-book text-2xl"></i>
            </div>
            <h3 class="font-bold text-slate-800">Belum ada dokumentasi</h3>
            <p class="text-slate-500 text-sm mt-1">Silakan cek kembali nanti.</p>
        </div>
        @endif
    </div>
</div>
@endsection
