@extends('depootcom.admin.blog.layout')

@section('title', 'Manajemen Kategori')
@section('page_title', 'Categories')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <h3 class="text-3xl font-black text-slate-800">Kategori Artikel</h3>
        <a href="{{ route('depootcom.admin.categories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20 transition-all">
            <i class="fas fa-plus mr-2"></i> Tambah Kategori
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Nama Kategori</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Slug</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Deskripsi</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($categories as $category)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5">
                        <span class="font-bold text-slate-700 text-sm">{{ $category->name }}</span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-xs font-mono text-slate-400 bg-slate-100 px-2 py-1 rounded-lg">{{ $category->slug }}</span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-xs text-slate-500">{{ Str::limit($category->description, 50) ?: '-' }}</span>
                    </td>
                    <td class="px-8 py-5 text-right space-x-2">
                        <a href="{{ route('depootcom.admin.categories.edit', $category) }}" class="text-slate-400 hover:text-blue-600 transition-colors">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('depootcom.admin.categories.destroy', $category) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors" onclick="return confirm('Hapus kategori ini? Artikel terkait akan kehilangan kaitan kategori.')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($categories->isEmpty())
        <div class="p-12 text-center">
            <p class="text-slate-400 italic">Belum ada kategori. Silakan buat satu.</p>
        </div>
        @endif
    </div>
</div>
@endsection
