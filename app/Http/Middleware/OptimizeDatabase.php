<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class OptimizeDatabase
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Disable query logging in production
        if (app()->environment('production')) {
            DB::connection()->disableQueryLog();
        }

        return $next($request);
    }
}
