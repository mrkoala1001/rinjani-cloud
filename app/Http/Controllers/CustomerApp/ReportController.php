<?php

namespace App\Http\Controllers\CustomerApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\CustomerMember;

class ReportController extends Controller
{
    public function index()
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);
        
        $tickets = Report::where('sender_id', $customerId)
            ->where('sender_type', CustomerMember::class)
            ->latest()
            ->paginate(10);
            
        return view('customer_app.tickets.index', compact('customer', 'tickets'));
    }

    public function create()
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);
        return view('customer_app.tickets.create', compact('customer'));
    }

    public function store(Request $request)
    {
        $customerId = session('customer_id');
        $customer = CustomerMember::findOrFail($customerId);

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Report::create([
            'sender_id' => $customer->id,
            'sender_type' => CustomerMember::class,
            'sender_name' => $customer->name,
            'sender_role' => 'customer',
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
            'user_unread' => false,
        ]);

        return redirect()->route('customer_app.tickets')
            ->with('success', 'Tiket laporan berhasil dibuat. Mohon tunggu respon dari admin.');
    }
}
