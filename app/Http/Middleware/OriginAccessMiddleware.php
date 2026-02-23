<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OriginAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        // Builder is God Mode
        if ($user->role === 'builder') {
            return $next($request);
        }

        $origin = $user->origin ?? 'hotpot'; // Default to hotpot for legacy/unspecified
        $host = $request->getHost();

        // If origin is "semua", allow everything
        if ($origin === 'semua') {
            return $next($request);
        }

        // Hotpot App Restriction
        if (str_contains($host, 'hotpot.depootcom.com')) {
            if (!in_array($origin, ['hotpot', 'semua'])) {
                abort(403, 'Akses ditolak. Akun Anda tidak memiliki akses ke aplikasi Hotpot.');
            }
        }

        // P3POT App Restriction
        if (str_contains($host, 'p3pot.depootcom.com')) {
            if (!in_array($origin, ['p3pot', 'semua'])) {
                abort(403, 'Akses ditolak. Akun Anda tidak memiliki akses ke aplikasi P3POT.');
            }
        }

        // Blog / Landing Restriction (if applicable)
        if ($host === 'depootcom.com' || $host === 'www.depootcom.com') {
            // Usually blog is public, but if there's admin area:
            if ($request->is('blog/admin*') || $request->is('admin*')) {
                if (!in_array($origin, ['blog', 'semua'])) {
                    abort(403, 'Akses ditolak. Akun Anda tidak memiliki akses ke manajemen Blog.');
                }
            }
        }

        return $next($request);
    }
}
