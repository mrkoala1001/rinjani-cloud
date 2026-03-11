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

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'whatsapp' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'username' => $request->username,
            'whatsapp' => $request->whatsapp,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'owner-member',
            'plan' => 'basic',
            'is_active' => true,
        ]);

        auth()->login($user);

        return redirect()->route('dashboard')->with('success', 'Selamat datang! Akun Anda telah berhasil dibuat.');
    }

    public function showForgotPassword()
    {
        return view('auth.passwords.forgot');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['identifier' => 'required']);
        $identifier = $request->identifier;

        $user = \App\Models\User::where('username', $identifier)
            ->orWhere('whatsapp', $identifier)
            ->first();

        if (!$user || !$user->whatsapp) {
            return back()->with('error', 'Akun atau nomor WhatsApp tidak ditemukan.');
        }

        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(15);

        \Illuminate\Support\Facades\DB::table('password_reset_was')->updateOrInsert(
            ['whatsapp' => $user->whatsapp],
            [
                'token' => $otp,
                'expires_at' => $expiresAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Send via Fonnte
        $message = "Halo *{$user->name}*,\n\nKode reset password HotPot Anda adalah: *{$otp}*\n\nKode ini berlaku selama 15 menit. Jangan berikan kode ini kepada siapapun.";
        
        try {
            \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN')
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $user->whatsapp,
                'message' => $message,
            ]);
            
            return redirect()->route('password.otp.view', ['wa' => $user->whatsapp])
                ->with('success', 'Kode reset telah dikirim ke WhatsApp Anda.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim pesan WhatsApp. Silakan coba lagi.');
        }
    }

    public function showOtpForm(Request $request)
    {
        $whatsapp = $request->wa;
        return view('auth.passwords.otp', compact('whatsapp'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'whatsapp' => 'required',
            'otp' => 'required|digits:6',
        ]);

        $reset = \Illuminate\Support\Facades\DB::table('password_reset_was')
            ->where('whatsapp', $request->whatsapp)
            ->where('token', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$reset) {
            return back()->with('error', 'Kode OTP salah atau sudah kadaluarsa.');
        }

        return view('auth.passwords.reset', ['whatsapp' => $request->whatsapp, 'token' => $request->otp]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'whatsapp' => 'required',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = \Illuminate\Support\Facades\DB::table('password_reset_was')
            ->where('whatsapp', $request->whatsapp)
            ->where('token', $request->token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$reset) {
            return redirect()->route('password.request')->with('error', 'Sesi reset sudah kadaluarsa.');
        }

        $user = \App\Models\User::where('whatsapp', $request->whatsapp)->first();
        if ($user) {
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->password)
            ]);

            \Illuminate\Support\Facades\DB::table('password_reset_was')->where('whatsapp', $request->whatsapp)->delete();

            return redirect()->route('login')->with('success', 'Password berhasil diubah! Silakan login dengan password baru.');
        }

        return back()->with('error', 'Gagal mengubah password.');
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
