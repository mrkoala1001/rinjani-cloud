@extends('layouts.app')

@section('title', 'Templates - Koala Builder')
@section('header_title', 'Template Management')

@section('content')
<div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-slate-800">System Templates</h3>
        <a href="{{ route('builder.templates.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold transition">
            <i class="fas fa-plus mr-2"></i> Add Template
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($templates as $template)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition overflow-hidden group">
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h4 class="font-bold text-slate-800 text-sm">{{ $template->name }}</h4>
                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                    <a href="{{ route('builder.templates.edit', $template->id) }}" class="text-indigo-600 hover:text-indigo-800"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('builder.templates.destroy', $template->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this template?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-600 hover:text-rose-800"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </div>
            </div>
            <div class="p-4 h-32 overflow-hidden text-xs text-slate-500 font-mono bg-slate-50/50">
                {{ Str::limit($template->html_content, 150) }}
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
