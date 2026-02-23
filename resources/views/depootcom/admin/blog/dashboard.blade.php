@extends('depootcom.admin.blog.layout')

@section('title', 'Blog Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-end">
        <div>
            <h3 class="text-3xl font-black text-slate-800">Welcome Back, Chief!</h3>
            <p class="text-slate-500 mt-1">Status blog Anda saat ini.</p>
        </div>
        <a href="{{ route('depootcom.admin.blog.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all flex items-center">
            <i class="fas fa-plus mr-2"></i> Tulis Artikel
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 mb-4 text-xl">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="text-3xl font-black text-slate-800">{{ $postsCount }}</div>
            <div class="text-sm font-bold text-slate-400 uppercase tracking-wider mt-1">Total Postingan</div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600 mb-4 text-xl">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="text-3xl font-black text-slate-800">{{ $publishedCount }}</div>
            <div class="text-sm font-bold text-slate-400 uppercase tracking-wider mt-1">Terbit</div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 mb-4 text-xl">
                <i class="fas fa-clock"></i>
            </div>
            <div class="text-3xl font-black text-slate-800">{{ $postsCount - $publishedCount }}</div>
            <div class="text-sm font-bold text-slate-400 uppercase tracking-wider mt-1">Draft</div>
        </div>
    </div>

    <!-- Recent Posts Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden text-sm">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h4 class="font-bold text-slate-800">Postingan Terbaru</h4>
            <a href="{{ route('depootcom.admin.blog.index') }}" class="text-blue-600 font-bold text-xs uppercase tracking-widest">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 text-[10px] font-black uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Judul</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentPosts as $post)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-700">{{ $post->title }}</div>
                            <div class="text-[10px] text-slate-400">{{ $post->slug }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $post->is_published ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $post->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-slate-500">
                            {{ $post->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('depootcom.admin.blog.edit', $post) }}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400 font-bold uppercase tracking-widest text-xs">Belum ada aktivitas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
