@extends('layouts.docs')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <span class="text-3xl">🐨</span> Ruang Koala Bertanya
        </h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Daftar Dokumentasi</h2>
            
            @if(count($files) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($files as $file)
                        <a href="{{ $file['type'] === 'folder' ? route('koala.index', ['path' => $file['path']]) : route('koala.show', ['path' => $file['path']]) }}" 
                           class="group block p-4 border rounded-lg hover:border-blue-500 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $file['type'] === 'folder' ? 'bg-yellow-100 text-yellow-600' : 'bg-blue-100 text-blue-600' }}">
                                    @if($file['type'] === 'folder')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {{ $file['label'] }}
                                    </h3>
                                    <p class="text-xs text-gray-500 capitalize">{{ $file['type'] }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <p>Belum ada dokumentasi di folder ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
