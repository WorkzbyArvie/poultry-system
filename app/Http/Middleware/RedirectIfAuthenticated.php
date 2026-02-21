<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                // Logic to redirect users based on their assigned role
                if ($user->role === 'superadmin') {
                    return redirect('/super-admin/dashboard');
                } 
                
                if ($user->role === 'client') {
                    return redirect('/client/dashboard');
                }

                // Default fallback
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}