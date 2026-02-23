@extends('layouts.app')

@section('title', 'Comments - Koala Builder')
@section('header_title', 'Comment Management')

@section('content')
<div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-slate-800">Latest Comments</h3>
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
                    <th class="px-6 py-4 border-b border-slate-100">Date</th>
                    <th class="px-6 py-4 border-b border-slate-100">User</th>
                    <th class="px-6 py-4 border-b border-slate-100">Content</th>
                    <th class="px-6 py-4 border-b border-slate-100">Status</th>
                    <th class="px-6 py-4 border-b border-slate-100 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comments as $comment)
                <tr class="hover:bg-slate-50/80 transition border-b border-slate-50">
                    <td class="px-6 py-4 text-xs text-slate-500">{{ $comment->created_at->format('d M Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800 text-sm">{{ $comment->name }}</div>
                        <div class="text-xs text-slate-400">{{ $comment->email }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate">{{ $comment->content }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] font-bold uppercase rounded {{ $comment->is_approved ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                            {{ $comment->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if(!$comment->is_approved)
                        <form action="{{ route('builder.comments.approve', $comment->id) }}" method="POST" class="inline mr-2">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900 text-sm font-bold">Approve</button>
                        </form>
                        @endif
                        <form action="{{ route('builder.comments.destroy', $comment->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this comment?')">
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
