@extends('layouts.public')

@section('title', $doc->title . ' - Dokumentasi HOT POT')

@section('content')
<div class="pt-32 pb-20 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-4 gap-12">
            <!-- Sidebar -->
            <div class="lg:col-span-1 hidden lg:block">
                <div class="sticky top-32">
                    <h3 class="font-bold text-slate-900 mb-4 uppercase text-xs tracking-wider">Daftar Isi</h3>
                    <ul class="space-y-1">
                        @foreach($docs as $item)
                        <li>
                            <a href="{{ route('documentation.show', $item->slug) }}" class="block px-4 py-2 rounded-lg text-sm {{ $item->id == $doc->id ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                {{ $item->title }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    
                    <a href="{{ route('documentation.index') }}" class="mt-8 inline-flex items-center text-slate-500 hover:text-indigo-600 text-sm font-medium transition">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Menu
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="lg:col-span-3">
                <div class="prose prose-slate prose-lg max-w-none">
                    <h1>{{ $doc->title }}</h1>
                    <div class="text-sm text-slate-400 mb-8 border-b border-slate-100 pb-4">
                        Terakhir diperbarui: {{ $doc->updated_at->format('d F Y') }}
                    </div>
                    
                    {!! Str::markdown($doc->content) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
