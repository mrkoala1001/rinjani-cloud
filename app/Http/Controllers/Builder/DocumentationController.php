<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function index()
    {
        $docs = \App\Models\Documentation::orderBy('order')->get();
        return view('builder.documentation.index', compact('docs'));
    }

    public function create()
    {
        return view('builder.documentation.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:documentations',
            'content' => 'required',
            'order' => 'integer',
        ]);

        \App\Models\Documentation::create($request->all());
        return redirect()->route('builder.documentation.index')->with('success', 'Documentation created.');
    }

    public function edit(\App\Models\Documentation $documentation)
    {
        return view('builder.documentation.edit', compact('documentation'));
    }

    public function update(Request $request, \App\Models\Documentation $documentation)
    {
        $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:documentations,slug,' . $documentation->id,
            'content' => 'required',
            'order' => 'integer',
        ]);

        $documentation->update($request->all());
        return redirect()->route('builder.documentation.index')->with('success', 'Documentation updated.');
    }

    public function destroy(\App\Models\Documentation $documentation)
    {
        $documentation->delete();
        return redirect()->route('builder.documentation.index')->with('success', 'Documentation deleted.');
    }
}
