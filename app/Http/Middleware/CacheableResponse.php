<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheableResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only cache GET requests
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            // Cache for 5 minutes
            $response->header('Cache-Control', 'public, max-age=300, must-revalidate');
            $response->header('Pragma', 'public');
        } else {
            // No cache for non-GET or error responses
            $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->header('Pragma', 'no-cache');
        }

        return $response;
    }
}
