<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userRole = $this->normalizeRole((string) Auth::user()->role);
        $allowedRoles = array_map(fn(string $role) => $this->normalizeRole($role), $roles);

        if (!in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }

    private function normalizeRole(string $role): string
    {
        $normalized = str_replace([' ', '-'], '_', strtolower(trim($role)));

        return match ($normalized) {
            'super_admin' => 'superadmin',
            'farmowner' => 'farm_owner',
            'farm_operations' => 'farm_operations',
            default => $normalized,
        };
    }
}
