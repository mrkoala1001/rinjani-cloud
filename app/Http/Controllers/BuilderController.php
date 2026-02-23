<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BuilderController extends Controller
{
    public function index()
    {
        // Builder Dashboard Logic
        
        // 1. System Health (Simulation)
        $load = sys_getloadavg();
        $serverLoad = $load[0] . ' (1m), ' . $load[1] . ' (5m), ' . $load[2] . ' (15m)';
        $uptime = shell_exec('uptime -p');
        $diskFree = disk_free_space('/');
        $diskTotal = disk_total_space('/');
        $diskUsage = round(($diskTotal - $diskFree) / $diskTotal * 100, 2);
        
        // 2. Database Stats
        $usersCount = User::count();
        $ispCount = User::where('role', 'isp')->count();
        $ownerCount = User::where('role', 'owner')->count();
        $billingCount = DB::table('billing_history')->count();
        
        // 3. Recent Logs (Mockup, or could read log file)
        $logs = []; 
        
        // 4. Broadcast Messages
        $broadcasts = \App\Models\Broadcast::latest()->get();

        // 5. AIO DB: Fetch ALL Users
        $managedUsers = User::latest()->get();

        return view('builder.dashboard', compact(
            'serverLoad', 'uptime', 'diskUsage',
            'usersCount', 'ispCount', 'ownerCount', 'billingCount',
            'broadcasts', 'managedUsers'
        ));
    }

    public function impersonateP3pot($id)
    {
        $user = User::where('origin', 'p3pot')->findOrFail($id);
        
        // Store original ID
        session(['p3pot_impersonated_by' => auth()->id()]);
        
        // Login as User using p3pot guard
        auth()->guard('p3pot')->login($user);
        
        $targetRoute = ($user->role === 'admin') ? 'p3pot.owner.dashboard' : 'p3pot.customer.dashboard';
        return redirect()->route($targetRoute)->with('success', "Logged in as P3POT {$user->role}: {$user->username}");
    }

    public function impersonate($id)
    {
        $user = User::whereIn('role', ['isp', 'owner'])->findOrFail($id);
        
        // Store original ID
        session(['impersonated_by' => auth()->id()]);
        
        // Login as User
        auth()->login($user);
        
        // Origin-based redirection
        $origin = $user->origin ?? 'hotpot';
        
        if ($origin === 'p3pot') {
            return redirect()->away('http://p3pot.depootcom.com/owner/dashboard')->with('success', "Logged in as P3POT {$user->role}: {$user->name}");
        }
        
        // Default Hotspot redirection
        $targetRoute = ($user->role === 'isp') ? 'hotsupport.dashboard' : 'dashboard';
        return redirect()->route($targetRoute)->with('success', "Logged in as {$user->role}: {$user->name}");
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('builder.edit_user', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8',
            'is_active' => 'required|boolean',
            'origin' => 'required|in:hotpot,blog,p3pot,semua',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->is_active,
            'origin' => $request->origin,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('builder.dashboard')->with('success', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        \DB::transaction(function () use ($user) {
            $userId = $user->id;

            if ($user->role === 'owner') {
                // Delete all related data (same as HotSupportController)
                \App\Models\Income::withoutGlobalScopes()->where('user_id', $userId)->delete();
                \App\Models\BillingHistory::withoutGlobalScopes()->where('user_id', $userId)->delete();
                \App\Models\Expense::withoutGlobalScopes()->where('user_id', $userId)->delete();
                \App\Models\Debt::withoutGlobalScopes()->where('user_id', $userId)->delete();
                \App\Models\CustomerMember::withoutGlobalScopes()->where('user_id', $userId)->delete();
                \App\Models\Reseller::withoutGlobalScopes()->where('user_id', $userId)->delete();
                \App\Models\MikrotikConfig::withoutGlobalScopes()->where('user_id', $userId)->delete();
                \App\Models\VoucherTemplate::withoutGlobalScopes()->where('user_id', $userId)->delete();
                \App\Models\Report::withoutGlobalScopes()
                    ->where('sender_id', $userId)
                    ->where('sender_type', \App\Models\User::class)
                    ->delete();
                \App\Models\HotspotProfileMetadata::withoutGlobalScopes()->where('user_id', $userId)->delete();
            }

            // Finally delete the user
            $user->delete();
        });

        return redirect()->route('builder.dashboard')->with('success', 'Account and its associated data (if any) have been permanently deleted.');
    }

    public function deleteP3potUser($id)
    {
        $user = User::where('origin', 'p3pot')->findOrFail($id);
        
        // Delete user
        $user->delete();

        return redirect()->route('builder.dashboard')->with('success', "P3POT Account {$user->username} has been deleted.");
    }

    public function storeBroadcast(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'message' => 'required',
            'type' => 'required|in:info,warning,danger,success',
        ]);

        \App\Models\Broadcast::create([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'is_active' => true,
        ]);

        return back()->with('success', 'Broadcast message sent.');
    }

    public function deleteBroadcast($id)
    {
        \App\Models\Broadcast::destroy($id);
        return back()->with('success', 'Broadcast message deleted.');
    }
    public function createUser()
    {
        return view('builder.create_user');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:isp,builder,owner',
            'location' => 'nullable|string|max:255',
            'origin' => 'required|in:hotpot,blog,p3pot,semua',
            'notes' => 'nullable|string',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'created_by' => auth()->id(), // Ownership for builder
            'is_active' => true,
            'location' => $request->location,
            'origin' => $request->origin,
            'notes' => $request->notes,
        ]);

        return redirect()->route('builder.dashboard')->with('success', 'User Account (' . ucfirst($request->role) . ') created successfully.');
    }

    public function reports()
    {
        $reports = Report::with('sender')->latest()->paginate(15);
        return view('builder.reports', compact('reports'));
    }

    public function viewReport($id)
    {
        $report = Report::with('sender')->findOrFail($id);
        
        if ($report->status === 'unread') {
            $report->update(['status' => 'read']);
        }

        return view('builder.view_report', compact('report'));
    }

    public function markAsRead($id)
    {
        $report = Report::findOrFail($id);
        $report->update(['status' => 'read']);

        return back()->with('success', 'Report marked as read.');
    }

    public function replyTicket(Request $request, $id)
    {
        $request->validate([
            'admin_reply' => 'required|string',
            'status' => 'required|in:pending,processed,resolved,closed',
        ]);

        $report = Report::findOrFail($id);
        $report->update([
            'admin_reply' => $request->admin_reply,
            'reply_at' => now(),
            'status' => $request->status,
            'user_unread' => true,
        ]);

        return back()->with('success', 'Reply sent and ticket updated.');
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processed,resolved,closed,read',
        ]);

        $report = Report::findOrFail($id);
        $report->update([
            'status' => $request->status,
            'user_unread' => true
        ]);

        return back()->with('success', 'Report status updated.');
    }

    public function deleteReport($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return back()->with('success', 'Report deleted successfully.');
    }
}
