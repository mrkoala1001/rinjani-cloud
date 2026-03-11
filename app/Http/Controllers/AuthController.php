<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (request()->getHost() === 'depootcom.site' || request()->getHost() === 'www.depootcom.site') {
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
        $request->validate([
            'email' => ['required'], // Field named 'email' but can be username
            'password' => ['required'],
        ]);

        $loginInput = $request->email;
        $password = $request->password;

        \Illuminate\Support\Facades\Log::info('Login attempt for: ' . $loginInput);

        // Try Email
        if (auth()->attempt(['email' => $loginInput, 'password' => $password])) {
            return $this->handleRedirect($request, $loginInput);
        }

        // Try Username as fallback
        if (auth()->attempt(['username' => $loginInput, 'password' => $password])) {
            return $this->handleRedirect($request, $loginInput);
        }

        \Illuminate\Support\Facades\Log::warning('Login FAILED for: ' . $loginInput);
        return back()->withErrors([
            'email' => 'Username/Email atau Password salah.',
        ])->onlyInput('email');
    }

    protected function handleRedirect(Request $request, $loginInput)
    {
        \Illuminate\Support\Facades\Log::info('Login SUCCESS for: ' . $loginInput);
        $request->session()->regenerate();

        // Handle Depootcom Admin Redirection
        if ($request->getHost() === 'depootcom.site' || $request->getHost() === 'www.depootcom.site') {
            if (auth()->user()->role === 'builder') {
                return redirect()->route('depootcom.admin.dashboard');
            }
            // If not builder, logout and error (security)
            auth()->logout();
            \Illuminate\Support\Facades\Log::warning('Access denied for domain: ' . $request->getHost() . ' for user: ' . $loginInput);
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
            if (in_array(auth()->user()->role, ['owner', 'owner-member'])) {
                return redirect()->route('p3pot.owner.dashboard'); 
            }
        }

        if (auth()->user()->role === 'reseller') {
            return redirect()->route('reseller.dashboard');
        }

        return redirect()->route('dashboard');
    }
    
    public function logout(Request $request) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
