@extends('layouts.app')

@section('title', 'FAQs - Koala Builder')
@section('header_title', 'FAQ Management')

@section('content')
<div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-slate-800">Frequently Asked Questions</h3>
        <a href="{{ route('builder.faqs.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold transition">
            <i class="fas fa-plus mr-2"></i> Add FAQ
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="space-y-4">
        @foreach($faqs as $faq)
        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 flex justify-between items-start">
            <div>
                <h4 class="font-bold text-slate-800 mb-1">{{ $faq->question }}</h4>
                <p class="text-sm text-slate-600 mb-2">{{ $faq->answer }}</p>
                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $faq->is_active ? 'bg-green-100 text-green-600' : 'bg-slate-200 text-slate-500' }}">
                    {{ $faq->is_active ? 'Active' : 'Hidden' }}
                </span>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('builder.faqs.edit', $faq->id) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold">Edit</a>
                <form action="{{ route('builder.faqs.destroy', $faq->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this FAQ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-rose-600 hover:text-rose-900 text-xs font-bold">Delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
