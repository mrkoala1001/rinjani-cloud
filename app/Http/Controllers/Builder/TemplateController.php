<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = \App\Models\VoucherTemplate::where('is_system', true)->get();
        return view('builder.templates.index', compact('templates'));
    }

    public function create()
    {
        $u = [
            'username' => 'user123',
            'password' => 'pass123',
            'price' => 'Rp 5.000',
            'validity' => '1 Hari',
            'server_name' => 'Hotspot-1',
            'dns_name' => 'hotspot.mikhmon'
        ];
        return view('builder.templates.create', compact('u'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'html_content' => 'required',
        ]);

        \App\Models\VoucherTemplate::create([
            'user_id' => auth()->id(), // Builder ID
            'is_system' => true,
            'name' => $request->name,
            'html_content' => $request->html_content,
            'css_content' => $request->css_content,
        ]);
        return redirect()->route('builder.templates.index')->with('success', 'Template created.');
    }

    public function edit(\App\Models\VoucherTemplate $template)
    {
        $u = [
            'username' => 'user123',
            'password' => 'pass123',
            'price' => 'Rp 5.000',
            'validity' => '1 Hari',
            'server_name' => 'Hotspot-1',
            'dns_name' => 'hotspot.mikhmon'
        ];
        return view('builder.templates.edit', compact('template', 'u'));
    }

    public function update(Request $request, \App\Models\VoucherTemplate $template)
    {
        $request->validate([
            'name' => 'required',
            'html_content' => 'required',
        ]);

        $template->update($request->all());
        return redirect()->route('builder.templates.index')->with('success', 'Template updated.');
    }

    public function destroy(\App\Models\VoucherTemplate $template)
    {
        $template->delete();
        return redirect()->route('builder.templates.index')->with('success', 'Template deleted.');
    }
}
