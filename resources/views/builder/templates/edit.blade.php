@extends('layouts.app')

@section('title', 'Edit Template - Koala Builder')
@section('header_title', 'Edit Template')

@section('content')
<div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 max-w-4xl mx-auto">
    <form action="{{ route('builder.templates.update', $template->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Template Name</label>
                <input type="text" name="name" value="{{ $template->name }}" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">HTML Content</label>
                    <textarea name="html_content" rows="15" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono" required>{{ $template->html_content }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">CSS Content</label>
                    <textarea name="css_content" rows="15" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono">{{ $template->css_content }}</textarea>
                </div>
            </div>

            <div class="bg-blue-50 text-blue-800 p-4 rounded-xl text-xs">
                <strong>Available Variables:</strong> {{ $u['username'] }}, {{ $u['password'] }}, {{ $u['price'] }}, {{ $u['validity'] }}, {{ $u['server_name'] }}, {{ $u['dns_name'] }}
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('builder.templates.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-50 transition text-sm">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition text-sm shadow-md">
                    Update Template
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
