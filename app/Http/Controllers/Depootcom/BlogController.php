<?php

namespace App\Http\Controllers\Depootcom;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Blog::where('is_published', true)
            ->latest('published_at')
            ->paginate(9);
            
        return view('depootcom.blog.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = Blog::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();
            
        return view('depootcom.blog.show', compact('post'));
    }
}
