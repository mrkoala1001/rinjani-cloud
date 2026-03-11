<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Determine origin or other fields, create as owner for now
                $user = User::create([
                    'name' => $googleUser->getName() ?? 'Google User',
                    'username' => 'user_' . Str::random(8),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'owner-member',
                    'plan' => 'basic', // Default plan
                    'is_active' => true,
                    // If google_id is not in fillable or DB, skip it for now or rely on email.
                ]);
            } else {
                // Retroactively update role for independent accounts created before the role split
                if ($user->role === 'owner' && is_null($user->created_by)) {
                    $user->update(['role' => 'owner-member']);
                }
            }

            Auth::login($user);

            // Instead of doing manual redirect logic, we can leverage AuthController's redirect
            // But since handleRedirect is protected, we can just redirect to dashboard:
            
            if ($user->role === 'builder') {
                return redirect()->route('builder.dashboard');
            }
            if ($user->role === 'isp') {
                return redirect()->route('hotsupport.dashboard');
            }
            if ($user->role === 'reseller') {
                return redirect()->route('reseller.dashboard');
            }
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Auth Failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Login with Google failed.');
        }
    }
}
