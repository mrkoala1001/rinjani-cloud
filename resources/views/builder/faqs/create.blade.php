@extends('layouts.app')

@section('title', 'Create FAQ - Koala Builder')
@section('header_title', 'Create FAQ')

@section('content')
<div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 max-w-2xl mx-auto">
    <form action="{{ route('builder.faqs.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Question</label>
                <input type="text" name="question" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Answer</label>
                <textarea name="answer" rows="4" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked>
                <label class="text-sm font-medium text-slate-700">Active</label>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('builder.faqs.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-50 transition text-sm">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition text-sm shadow-md">
                    Create FAQ
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
