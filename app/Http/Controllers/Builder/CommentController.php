<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $comments = \App\Models\Comment::latest()->get();
        return view('builder.comments.index', compact('comments'));
    }

    public function approve($id)
    {
        $comment = \App\Models\Comment::findOrFail($id);
        $comment->update(['is_approved' => true]);
        return back()->with('success', 'Comment approved.');
    }

    public function destroy($id)
    {
        \App\Models\Comment::findOrFail($id)->delete();
        return back()->with('success', 'Comment deleted.');
    }
}
