<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncRegionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for cookie or header from Next.js frontend
        $countryId = $request->cookie('NEXT_COUNTRY_ID') 
                     ?? $request->header('x-clicktrendz-country-id');

        if ($countryId) {
            session(['current_country_id' => $countryId]);
        }

        return $next($request);
    }
}
