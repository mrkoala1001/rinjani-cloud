<?php

namespace App\Http\Middleware\CustomerApp;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCustomerApp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('customer_id')) {
            // Prevent flashing error for background fetches/AJAX
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            
            return redirect()->route('customer_app.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
