@extends('layouts.app')

@section('title', 'Documentation - Koala Builder')
@section('header_title', 'Documentation Management')

@section('content')
<div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-slate-800">Documentation List</h3>
        <a href="{{ route('builder.documentation.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold transition">
            <i class="fas fa-plus mr-2"></i> Add Documentation
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 uppercase text-xs font-bold text-slate-500 tracking-wider">
                    <th class="px-6 py-4 border-b border-slate-100">Order</th>
                    <th class="px-6 py-4 border-b border-slate-100">Title</th>
                    <th class="px-6 py-4 border-b border-slate-100">Slug</th>
                    <th class="px-6 py-4 border-b border-slate-100">Status</th>
                    <th class="px-6 py-4 border-b border-slate-100 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($docs as $doc)
                <tr class="hover:bg-slate-50/80 transition border-b border-slate-50">
                    <td class="px-6 py-4 text-sm font-mono">{{ $doc->order }}</td>
                    <td class="px-6 py-4 font-bold text-slate-800 text-sm">{{ $doc->title }}</td>
                    <td class="px-6 py-4 text-xs text-slate-500 font-mono">{{ $doc->slug }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] font-bold uppercase rounded {{ $doc->is_published ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-500' }}">
                            {{ $doc->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('builder.documentation.edit', $doc->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 text-sm font-bold">Edit</a>
                        <form action="{{ route('builder.documentation.destroy', $doc->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this documentation?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-900 text-sm font-bold">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
