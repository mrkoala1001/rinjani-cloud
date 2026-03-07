<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Debt;
use App\Models\BillingHistory;
use App\Models\CustomerMember;
use App\Models\MikrotikConfig;
use App\Models\HotspotProfileMetadata;
use RouterOS\Client;
use RouterOS\Query;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class BillingController extends Controller
{
    private function getClient()
    {
        $mkConfig = MikrotikConfig::where('user_id', auth()->id())->first();
        if (!$mkConfig) return null;

        try {
            return new Client([
                'host' => $mkConfig->host,
                'user' => $mkConfig->user,
                'pass' => $mkConfig->pass,
                'port' => (int)($mkConfig->port ?? 8728),
                'timeout' => 2,
            ]);
        } catch (Exception $e) {
            return null;
        }
    }

    public function index() {
        return redirect()->route('billing.monitor');
    }

    public function sync() {
        $client = $this->getClient();
        if (!$client) {
            return redirect()->route('billing.income')->with('error', 'RouterOS Connection Failed');
        }

        try {
            // 1. Get All Users
            $users = $client->query('/ip/hotspot/user/print')->read();
            
            // 2. Get Metadata for pricing
            $profilesMeta = HotspotProfileMetadata::all()->keyBy('profile_name');
            
            $count = 0;
            
            foreach ($users as $user) {
                $comment = $user['comment'] ?? '';
                $username = $user['name'] ?? '';
                $profile = $user['profile'] ?? '';
                
                // Only process users with comment starting with "VC" (Mikhmon standard)
                if (!str_starts_with($comment, 'VC')) {
                    continue;
                }
                
                // Check if already exists
                if (BillingHistory::where('voucher_code', $username)->exists()) {
                    continue;
                }
                
                // Parse Data
                // Format: VC d/m/Y [ResellerName]
                // Example: VC 13/02/2026 [Admin]
                
                $parts = explode(' ', $comment);
                $dateStr = $parts[1] ?? date('d/m/Y');
                
                // Parse Date
                try {
                    $dateSold = Carbon::createFromFormat('d/m/Y', $dateStr)->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    $dateSold = now();
                }
                
                // Reseller Logic
                $resellerName = 'Admin';
                if (preg_match('/\[(.*?)\]/', $comment, $matches)) {
                    $resellerName = $matches[1];
                }
                
                $category = 'Voucher';
                if (strtolower($resellerName) !== 'admin') {
                    $category = 'Reseller';
                }
                // Determine if Member? Legacy didn't strictly separate Member from Voucher in comments, 
                // but usually Members are manual add. We'll stick to Voucher/Reseller for auto-sync of "VC" comments.
                
                // Price
                $price = 0;
                if (isset($profilesMeta[$profile])) {
                    $price = $profilesMeta[$profile]->selling_price > 0 ? $profilesMeta[$profile]->selling_price : $profilesMeta[$profile]->price;
                }

                BillingHistory::create([
                    'voucher_code' => $username,
                    'profile' => $profile,
                    'server' => $user['server'] ?? 'all',
                    'price' => $price,
                    'date_sold' => $dateSold,
                    'category' => $category,
                    'payment_method' => 'Cash', // Default
                    'bill_amount' => $price,
                    'paid_amount' => $price,
                    'notes' => "Synced from RouterOS ($comment)"
                ]);
                
                $count++;
            }
            
            return redirect()->route('billing.income')->with('success', "Synced $count new transactions.");
            
        } catch (Exception $e) {
            return redirect()->route('billing.income')->with('error', 'Sync Failed: ' . $e->getMessage());
        }
    }


    public function monitor() {
        $month = date('m');
        $year = date('Y');

        // Manual Income (from incomes table)
        $income_voucher = Income::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('category', 'Voucher')
            ->sum('amount');
            
        $income_member = Income::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('category', 'Member')
            ->sum('amount');
            
        $income_reseller = Income::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('category', 'Reseller')
            ->sum('amount');

        $income = $income_voucher + $income_member + $income_reseller;

        $expense = Expense::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');
            
        $debt = Debt::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');

        $profit = $income - $expense;

        return view('billing.monitor', compact(
            'income', 'expense', 'debt', 'profit', 
            'income_voucher', 'income_member', 'income_reseller'
        ));
    }
    
    public function exportMonitorPdf(Request $request) {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        if (!$startDate || !$endDate) {
            return redirect()->back()->with('error', 'Pilih rentang tanggal terlebih dahulu.');
        }

        // Fetch Incomes
        $incomes = Income::whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get();
        $totalIncome = $incomes->sum('amount');

        // Fetch Expenses
        $expenses = Expense::with('debt')->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get();
        $totalExpense = $expenses->sum('amount');

        // Fetch Debts active during period (or all active debts for simple summary)
        $debts = Debt::orderBy('date', 'asc')->get();
        $totalDebt = $debts->sum('amount');
        
        $totalPaidDebt = Expense::where('category', 'Bayar Hutang')->sum('amount');
        
        $profit = $totalIncome - $totalExpense;

        $pdf = Pdf::loadView('billing.pdf_report', compact(
            'startDate', 'endDate', 
            'incomes', 'totalIncome', 
            'expenses', 'totalExpense', 
            'debts', 'totalDebt', 'totalPaidDebt',
            'profit'
        ));
        
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Laporan_Keuangan_Hotpot_{$startDate}_sampai_{$endDate}.pdf");
    }

    public function closePeriod(Request $request) {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $password = $request->get('confirm_password');

        // Optional: Verifikasi password untuk keamanan ekstra jika diinginkan
        // Di sini saya asumsikan user mengonfirmasi lewat form.
        
        if (!$startDate || !$endDate) {
            return redirect()->back()->with('error', 'Pilih rentang tanggal periode yang akan ditutup.');
        }

        try {
            DB::beginTransaction();

            // 1. Hapus Pemasukan
            Income::whereBetween('date', [$startDate, $endDate])->delete();

            // 2. Hapus Pengeluaran
            Expense::whereBetween('date', [$startDate, $endDate])->delete();

            // 3. BillingHistory (Voucher Sales) TIIDAK DIHAPUS sesuai permintaan
            // Agar stok voucher distribusi reseller tetap aman.

            DB::commit();

            return redirect()->route('billing.monitor')->with('success', "Periode $startDate s/d $endDate telah berhasil ditutup. Seluruh riwayat transaksi telah dibersihkan.");

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menutup buku: ' . $e->getMessage());
        }
    }

    public function income(Request $request) {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');
        
        $query = Income::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
        
        $incomes = $query->orderBy('date', 'desc')->paginate(20);
            
        // Summary Calculations
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        
        $summaryToday = Income::whereDate('date', $today)
            ->selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();
            
        $summaryMonth = Income::whereYear('date', $startOfMonth->year)
            ->whereMonth('date', $startOfMonth->month)
            ->selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();
            
        $summaryTotal = Income::selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();
            
        // For Manual Input Form
        $customers = CustomerMember::orderBy('name')->get();
            
        return view('billing.income', compact(
            'incomes', 
            'customers',
            'summaryToday',
            'summaryMonth',
            'summaryTotal',
            'startDate',
            'endDate',
            'search'
        ));
    }
    
    public function exportIncome(Request $request) {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');
        
        if (!$startDate || !$endDate) {
            return redirect()->back()->with('error', 'Silakan pilih rentang tanggal.');
        }
        
        $query = Income::whereBetween('date', [$startDate, $endDate]);
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
        
        $incomes = $query->orderBy('date', 'asc')->get();
        
        $filename = "rekap_pemasukan_" . $startDate . "_to_" . $endDate . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = ['Tanggal', 'Kategori', 'Pelanggan', 'Keterangan', 'Metode', 'Jumlah (Rp)'];
        
        $callback = function() use($incomes, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $total = 0;
            foreach ($incomes as $inc) {
                fputcsv($file, [
                    $inc->date,
                    $inc->category,
                    $inc->customer_name,
                    $inc->description,
                    $inc->payment_method,
                    $inc->amount
                ]);
                $total += $inc->amount;
            }
            
            fputcsv($file, ['', '', '', '', 'TOTAL', $total]);
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    public function storeIncome(Request $request) {
        $data = $request->validate([
            'source_mode' => 'required|in:existing,new',
            'category' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'payment_method' => 'required',
            'description' => 'nullable|string',
            'proof_image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Handle Customer
        $customer_name = '';
        $customer_id = null;
        
        if ($request->source_mode === 'existing') {
            $cust = CustomerMember::find($request->customer_id);
            if ($cust) {
                $customer_id = $cust->id;
                $customer_name = $cust->name;
            }
        } else {
             $customer_name = $request->new_customer_name;
        }
        
        // Handle File Upload
        $proof_path = null;
        if ($request->hasFile('proof_image')) {
            $proof_path = $request->file('proof_image')->store('proofs', 'public');
        }

        Income::create([
            'date' => $request->date,
            'category' => $request->category,
            'description' => $request->description ?? $customer_name,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'customer_id' => $customer_id,
            'customer_name' => $customer_name,
            'proof_image' => $proof_path,
            'notes' => $request->notes
        ]);

        return redirect()->route('billing.income')->with('success', 'Pemasukan berhasil dicatat.');
    }
    
    public function updateIncome(Request $request) {
        $data = $request->validate([
            'id' => 'required|exists:incomes,id',
            'category' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'payment_method' => 'required',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $income = Income::findOrFail($request->id);

        $income->update([
            'category' => $request->category,
            'date' => $request->date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'description' => $request->description,
            'notes' => $request->notes,
        ]);

        return redirect()->route('billing.income')->with('success', 'Pemasukan berhasil diperbarui.');
    }
    
    public function deleteIncome($id) {
        Income::destroy($id);
        return redirect()->back()->with('success', 'Pemasukan berhasil dihapus.');
    }
    
    public function printIncome($id) {
        $transaction = Income::findOrFail($id);
        return view('billing.print_income', compact('transaction'));
    }

    // Expenses
    // Expenses
    public function expenses(Request $request) {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');
        
        $query = Expense::with('debt');
        
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
        
        $expenses = $query->orderBy('date', 'desc')->paginate(20);
        
        // Summary Calculations
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        
        $summaryToday = Expense::whereDate('date', $today)
            ->selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();
            
        $summaryMonth = Expense::whereYear('date', $startOfMonth->year)
            ->whereMonth('date', $startOfMonth->month)
            ->selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();
            
        $summaryTotal = Expense::selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();
        
        // Only show debts with remaining amount
        $debts = Debt::where('amount', '>', 0)->orderBy('description')->get();
        
        return view('billing.expense', compact(
            'expenses', 
            'debts',
            'summaryToday',
            'summaryMonth',
            'summaryTotal',
            'startDate',
            'endDate',
            'search'
        ));
    }

    public function exportExpense(Request $request) {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');
        
        if (!$startDate || !$endDate) {
            return redirect()->back()->with('error', 'Silakan pilih rentang tanggal.');
        }
        
        $query = Expense::with('debt')->whereBetween('date', [$startDate, $endDate]);
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
        
        $expenses = $query->orderBy('date', 'asc')->get();
        
        $filename = "rekap_pengeluaran_" . $startDate . "_to_" . $endDate . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = ['Tanggal', 'Kategori', 'Keterangan', 'Hutang Terkait', 'Jumlah (Rp)'];
        
        $callback = function() use($expenses, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $total = 0;
            foreach ($expenses as $inc) {
                fputcsv($file, [
                    $inc->date,
                    $inc->category,
                    $inc->description,
                    $inc->debt ? $inc->debt->description : '-',
                    $inc->amount
                ]);
                $total += $inc->amount;
            }
            
            fputcsv($file, ['', '', '', 'TOTAL', $total]);
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function storeExpense(Request $request) {
        $validated = $request->validate([
            'description' => 'required',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'category' => 'required',
            'debt_id' => 'nullable|exists:debts,id',
        ]);
        
        // Handle Debt Payment
        if ($request->category === 'Bayar Hutang') {
            if (!$request->debt_id) {
                return redirect()->back()->with('error', 'Silakan pilih Hutang yang akan dibayar.');
            }
            
            $debt = Debt::find($request->debt_id);
            if ($debt) {
                if ($request->amount > $debt->amount) {
                    return redirect()->back()->with('error', 'Jumlah pembayaran melebihi sisa hutang (Rp ' . number_format($debt->amount,0,',','.') . ').');
                }
                
                // Reduce Debt
                $debt->decrement('amount', $request->amount);
                
                // Add Debt Info to Description if not present
                $validated['description'] .= " (Bayar Hutang: {$debt->description})";
            }
        } else {
             $validated['debt_id'] = null;
        }

        if ($request->filled('id')) {
            // Edit logic might be tricky with debt linking, for now simplify to update basics
            // If editing a 'Bayar Hutang' transaction, we'd need to revert active debt change... 
            // For simplicity in this iteration, just update expense record.
            Expense::where('id', $request->id)->update($validated);
            $msg = 'Pengeluaran diperbarui.';
        } else {
            Expense::create($validated);
            $msg = 'Pengeluaran ditambahkan.';
        }
        return redirect()->back()->with('success', $msg);
    }
    
    public function deleteExpense($id) {
        Expense::destroy($id);
        return redirect()->back()->with('success', 'Pengeluaran dihapus.');
    }

    // Debts
    public function debts(Request $request) {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');
        
        $query = Debt::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%");
            });
        }
        
        $debts = $query->orderBy('date', 'desc')->paginate(20);
        
        // Summary Calculations
        $totalDebt = Debt::sum('amount');
        $countDebt = Debt::count();
        
        // Calculate paid amount from expenses with category 'Bayar Hutang'
        $totalPaid = Expense::where('category', 'Bayar Hutang')->sum('amount');
        
        return view('billing.debt', compact(
            'debts',
            'totalDebt',
            'countDebt',
            'totalPaid',
            'startDate',
            'endDate',
            'search'
        ));
    }

    public function exportDebt(Request $request) {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');
        
        if (!$startDate || !$endDate) {
            return redirect()->back()->with('error', 'Silakan pilih rentang tanggal.');
        }
        
        $query = Debt::whereBetween('date', [$startDate, $endDate]);
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%");
            });
        }
        
        $debts = $query->orderBy('date', 'asc')->get();
        
        $filename = "rekap_hutang_" . $startDate . "_to_" . $endDate . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = ['Tanggal', 'Keterangan Hutang', 'Sisa Jumlah (Rp)'];
        
        $callback = function() use($debts, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $total = 0;
            foreach ($debts as $inc) {
                fputcsv($file, [
                    $inc->date,
                    $inc->description,
                    $inc->amount
                ]);
                $total += $inc->amount;
            }
            
            fputcsv($file, ['', 'TOTAL SISA HUTANG', $total]);
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function storeDebt(Request $request) {
         $validated = $request->validate([
            'description' => 'required',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);
        
        if ($request->filled('id')) {
            Debt::where('id', $request->id)->update($validated);
            $msg = 'Hutang diperbarui.';
        } else {
            Debt::create($validated);
            $msg = 'Hutang ditambahkan.';
        }
        return redirect()->back()->with('success', $msg);
    }
    
    public function deleteDebt($id) {
        Debt::destroy($id);
        return redirect()->back()->with('success', 'Hutang dihapus.');
    }
}
