<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerMember;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport;
use Barryvdh\DomPDF\Facade\Pdf;

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
            // QUOTA CHECK
            $user = auth()->user();
            $plan = $user->plan ?? 'basic';
            if ($plan !== 'basic' && (!$user->plan_expires_at || $user->plan_expires_at->isPast())) {
                $plan = 'basic';
            }
            $planConfig = \App\Helpers\PlanHelper::getPlanConfig($plan);
            $maxCustomers = $planConfig['quotas']['customer_max'] ?? 0;

            if ($maxCustomers != -1) {
                $currentCount = CustomerMember::where('user_id', auth()->id())->count();
                if ($currentCount >= $maxCustomers) {
                    return redirect()->back()->with('error', 'Batas maksimal pelanggan untuk paket ' . $planConfig['name'] . ' adalah ' . $maxCustomers . '. Silakan upgrade.');
                }
            }

            CustomerMember::create($data);
            $msg = 'Pelanggan baru ditambahkan.';
        }

        return redirect()->back()->with('success', $msg);
    }

    public function delete($id) {
        CustomerMember::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Data pelanggan dihapus.');
    }

    public function exportExcel($type = 'all') {
        $filename = 'pelanggan_' . strtolower($type) . '_' . date('Y-m-d') . '.xlsx';
        return Excel::download(new CustomersExport($type), $filename);
    }

    public function exportPdf($type = 'all') {
        $query = CustomerMember::query();
        if ($type !== 'all') {
             $query->where('type', $type);
        }
        $customers = $query->get();
        
        $titles = [
            'all' => 'Semua Pelanggan',
            'MEMBER' => 'Member (Hotspot)',
            'PERUMAHAN' => 'Perumahan (PPPoE)',
            'RESELLER' => 'Reseller'
        ];
        $title = $titles[$type] ?? 'Data Pelanggan';

        $pdf = Pdf::loadView('customer.pdf', compact('customers', 'title'));
        return $pdf->download('pelanggan_' . strtolower($type) . '_' . date('Y-m-d') . '.pdf');
    }

    public function exportWaCsv($type = 'all') {
        $query = CustomerMember::query();
        if ($type !== 'all') {
            $query->where('type', $type);
        }
        $customers = $query->whereNotNull('whatsapp')->where('whatsapp', '!=', '')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="wa_numbers_' . strtolower($type) . '_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            foreach ($customers as $customer) {
                fputcsv($file, [$customer->whatsapp, $customer->name]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
