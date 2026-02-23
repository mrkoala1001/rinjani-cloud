<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerMember;

class CustomerController extends Controller
{
    public function list($type = 'all') {
        // Query automatically scoped by BelongsToTenant trait
        $query = CustomerMember::query();

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        if (request('search')) {
            $key = request('search');
            $query->where(function($q) use ($key) {
                $q->where('name', 'like', "%$key%")
                  ->orWhere('location', 'like', "%$key%")
                  ->orWhere('device_ip', 'like', "%$key%")
                  ->orWhere('notes', 'like', "%$key%");
            });
        }
        
        $query->orderBy('name', 'asc');
        $customers = $query->paginate(20);

        $titles = [
            'all' => 'Semua Pelanggan',
            'MEMBER' => 'Member (Hotspot)',
            'PERUMAHAN' => 'Perumahan (PPPoE)',
            'RESELLER' => 'Reseller'
        ];
        
        $title = $titles[$type] ?? 'Data Pelanggan';

        return view('customer.list', compact('customers', 'type', 'title'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
        ]);

        $data = $request->except(['_token', 'id']);
        
        if ($request->filled('id')) {
            CustomerMember::where('id', $request->id)->update($data);
            $msg = 'Data pelanggan diperbarui.';
        } else {
            CustomerMember::create($data);
            $msg = 'Pelanggan baru ditambahkan.';
        }

        return redirect()->back()->with('success', $msg);
    }

    public function delete($id) {
        CustomerMember::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Data pelanggan dihapus.');
    }
}
