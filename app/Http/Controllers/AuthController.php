<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (request()->getHost() === 'depootcom.com' || request()->getHost() === 'www.depootcom.com') {
            return view('depootcom.admin.blog.login');
        }
        return view('auth.login');
    }

    public function showBlogLogin()
    {
        return view('depootcom.admin.blog.login');
    }

    public function login(Request $request)
    {
        $input = $request->validate([
            'email' => ['required'], // Input name is 'email' but can be username
            'password' => ['required'],
        ]);

        $loginType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->email,
            'password' => $request->password
        ];

        \Illuminate\Support\Facades\Log::info('Login attempt for: ' . $request->email . ' (type: ' . $loginType . ')');

        if (auth()->attempt($credentials)) {
            \Illuminate\Support\Facades\Log::info('Login SUCCESS for: ' . $request->email);
            $request->session()->regenerate();

            // Handle Depootcom Admin Redirection
            if ($request->getHost() === 'depootcom.com' || $request->getHost() === 'www.depootcom.com') {
                if (auth()->user()->role === 'builder') {
                    return redirect()->route('depootcom.admin.dashboard');
                }
                // If not builder, logout and error (security)
                auth()->logout();
                \Illuminate\Support\Facades\Log::warning('Access denied for domain: ' . $request->getHost() . ' for user: ' . $request->email);
                return back()->withErrors(['email' => 'Access denied for this domain.']);
            }

            if (auth()->user()->role === 'builder') {
                return redirect()->route('builder.dashboard');
            }

            if (auth()->user()->role === 'isp') {
                return redirect()->route('hotsupport.dashboard');
            }

            // Origin-based redirection for owners/others
            if (auth()->user()->origin === 'p3pot') {
                if (auth()->user()->role === 'owner') {
                    return redirect()->route('p3pot.owner.dashboard'); 
                }
            }

            if (auth()->user()->role === 'reseller') {
                return redirect()->route('reseller.dashboard');
            }

            return redirect()->route('dashboard');
        }

        \Illuminate\Support\Facades\Log::warning('Login FAILED for: ' . $request->email);
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    
    public function logout(Request $request) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
