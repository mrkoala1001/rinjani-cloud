<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        $userRole = $request->user() ? $request->user()->role : null;
        $allowedRoles = [$role, 'builder'];
        
        if ($role === 'owner') {
            $allowedRoles[] = 'mitra';
            $allowedRoles[] = 'mitra-reseller';
            $allowedRoles[] = 'owner-member';
        }

        if (! $userRole || !in_array($userRole, $allowedRoles)) {
            // Builder is God Mode, allow access to ANY role route (except maybe owner specific ones if needed, but for now allow)
            // Actually, if role is 'owner', builder shouldn't necessarily access it unless impersonating.
            // But if role is 'isp', builder should definitely access it.
            
            if ($request->user()->role === 'builder' && $role === 'isp') {
                return $next($request);
            }

            // Redirect based on actual role if user is logged in
            if ($request->user()) {
                 if ($request->user()->role === 'builder') {
                     return redirect()->route('builder.dashboard');
                 }
                 if ($request->user()->role === 'isp') {
                     return redirect()->route('hotsupport.dashboard');
                 } else {
                     return redirect()->route('dashboard');
                 }
            }
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
