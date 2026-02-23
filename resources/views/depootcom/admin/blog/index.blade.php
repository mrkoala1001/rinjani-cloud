@extends('depootcom.admin.blog.layout')

@section('title', 'Daftar Postingan')
@section('page_title', 'All Posts')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-end">
        <div>
            <h3 class="text-3xl font-black text-slate-800">Manajemen Blog</h3>
            <p class="text-slate-500 mt-1">Semua artikel yang telah Anda buat.</p>
        </div>
        <a href="{{ route('depootcom.admin.blog.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all flex items-center">
            <i class="fas fa-plus mr-2"></i> Tulis Artikel Baru
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden text-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 text-[10px] font-black uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Informasi Artikel</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Dipublish</th>
                        <th class="px-6 py-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($posts as $post)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-700 text-base">{{ $post->title }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $post->slug }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-lg">
                                {{ $post->category ? $post->category->name : 'Uncategorized' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($post->is_published)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Published</span>
                            @else
                                <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-500">
                            {{ $post->published_at ? $post->published_at->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('depootcom.admin.blog.edit', $post) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('depootcom.admin.blog.destroy', $post) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all" onclick="return confirm('Hapus artikel ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="text-slate-300 text-5xl mb-4"><i class="fas fa-ghost"></i></div>
                            <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Belum ada postingan blog.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($posts->hasPages())
        <div class="p-6 bg-slate-50 border-t border-slate-100">
            {{ $posts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
