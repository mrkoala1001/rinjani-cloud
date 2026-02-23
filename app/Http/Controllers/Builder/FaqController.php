<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = \App\Models\Faq::all();
        return view('builder.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('builder.faqs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'answer' => 'required',
        ]);

        \App\Models\Faq::create($request->all());
        return redirect()->route('builder.faqs.index')->with('success', 'FAQ created.');
    }

    public function edit(\App\Models\Faq $faq)
    {
        return view('builder.faqs.edit', compact('faq'));
    }

    public function update(Request $request, \App\Models\Faq $faq)
    {
        $request->validate([
            'question' => 'required',
            'answer' => 'required',
        ]);

        $faq->update($request->all());
        return redirect()->route('builder.faqs.index')->with('success', 'FAQ updated.');
    }

    public function destroy(\App\Models\Faq $faq)
    {
        $faq->delete();
        return redirect()->route('builder.faqs.index')->with('success', 'FAQ deleted.');
    }
}
