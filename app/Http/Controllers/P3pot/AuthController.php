<?php

namespace App\Http\Controllers\P3pot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('p3pot.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::guard('p3pot')->attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::guard('p3pot')->user();
            
            \Illuminate\Support\Facades\Log::info('Login SUCCESS for P3POT user via guard: ' . $user->username);

            if ($user->role === 'admin') {
                return redirect()->intended(route('p3pot.owner.dashboard'));
            }
            return redirect()->intended(route('p3pot.customer.dashboard'));
        }

        \Illuminate\Support\Facades\Log::warning('Login FAILED for P3POT user: ' . $credentials['username']);

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('p3pot')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('p3pot.landing');
    }
}
