@extends('depootcom.admin.blog.layout')

@section('title', 'Edit Kategori')
@section('page_title', 'Edit Category')

@section('content')
<div class="max-w-2xl mx-auto space-y-8">
    <div class="flex items-center space-x-4">
        <a href="{{ route('depootcom.admin.categories.index') }}" class="w-10 h-10 rounded-full bg-white shadow-sm border border-slate-200 flex items-center justify-center text-slate-500 hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h3 class="text-3xl font-black text-slate-800">Edit Kategori</h3>
    </div>

    <form action="{{ route('depootcom.admin.categories.update', $category) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-6">
            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-2">Nama Kategori</label>
                <input type="text" name="name" value="{{ $category->name }}" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-blue-500 transition-colors font-bold text-lg">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-2">Deskripsi (Optional)</label>
                <textarea name="description" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-blue-500 transition-colors font-medium">{{ $category->description }}</textarea>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-5 rounded-2xl font-black uppercase text-sm tracking-widest shadow-xl shadow-blue-500/20 transition-all">
                Update Kategori
            </button>
        </div>
    </form>
</div>
@endsection
