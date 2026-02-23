<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $faqs = \App\Models\Faq::where('is_active', true)->get();
        $comments = \App\Models\Comment::where('is_approved', true)->latest()->take(10)->get();
        return view('landing', compact('faqs', 'comments'));
    }

    public function documentation()
    {
        $docs = \App\Models\Documentation::where('is_published', true)->orderBy('order')->get();
        return view('documentation.index', compact('docs'));
    }

    public function showDocumentation($slug)
    {
        $doc = \App\Models\Documentation::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $docs = \App\Models\Documentation::where('is_published', true)->orderBy('order')->get();
        return view('documentation.show', compact('doc', 'docs'));
    }

    public function storeComment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'content' => 'required|string',
        ]);

        \App\Models\Comment::create([
            'name' => $request->name,
            'email' => $request->email,
            'content' => $request->content,
            'is_approved' => false,
        ]);

        return back()->with('success', 'Komentar berhasil dikirim dan menunggu moderasi.')->withFragment('comments');
    }
}
