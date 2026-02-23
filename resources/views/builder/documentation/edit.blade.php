@extends('layouts.app')

@section('title', 'Edit Documentation - Koala Builder')
@section('header_title', 'Edit Documentation')

@section('content')
<div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 max-w-4xl mx-auto">
    <form action="{{ route('builder.documentation.update', $documentation->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Title</label>
                <input type="text" name="title" value="{{ $documentation->title }}" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
            </div>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ $documentation->slug }}" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Order</label>
                    <input type="number" name="order" value="{{ $documentation->order }}" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Content (Markdown supported)</label>
                <textarea name="content" rows="15" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono">{{ $documentation->content }}</textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ $documentation->is_published ? 'checked' : '' }}>
                <label class="text-sm font-medium text-slate-700">Published</label>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('builder.documentation.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-50 transition text-sm">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition text-sm shadow-md">
                    Update Documentation
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
