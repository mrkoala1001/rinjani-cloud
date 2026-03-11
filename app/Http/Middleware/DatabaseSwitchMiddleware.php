<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DatabaseSwitchMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        if ($host === 'depootcom.site' || $host === 'www.depootcom.site') {
            // Set table suffix for Depootcom
            Config::set('app.table_suffix', '_blog');
            
            \Illuminate\Support\Facades\Log::debug('Applied _blog suffix for host: ' . $host);
        }

        return $next($request);
    }
}
