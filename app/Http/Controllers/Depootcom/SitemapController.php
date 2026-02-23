<?php

namespace App\Http\Controllers\Depootcom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $posts = \App\Models\Blog::where('is_published', true)->orderBy('updated_at', 'desc')->get();
        $categories = \App\Models\Category::all();

        $content = view('depootcom.sitemap', compact('posts', 'categories'))->render();

        return response($content)->header('Content-Type', 'text/xml');
    }}
