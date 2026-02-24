<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    /**
     * Routes that are still accessible without an active subscription.
     */
    private array $allowedRouteNames = [
        'farmowner.dashboard',
        'farmowner.profile',
        'farmowner.update_profile',
        'farmowner.subscriptions',
        'farmowner.logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'farm_owner') {
            return $next($request);
        }

        $routeName = (string) $request->route()?->getName();

        if (in_array($routeName, $this->allowedRouteNames, true) || str_starts_with($routeName, 'farmowner.support.')) {
            return $next($request);
        }

        $farmOwner = $user->farmOwner;

        if (!$farmOwner) {
            return redirect()->route('farmowner.register')
                ->with('error', 'Farm owner profile not found. Please complete registration.');
        }

        $hasActiveSubscription = $farmOwner->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->exists();

        if (!$hasActiveSubscription) {
            return redirect()->route('farmowner.subscriptions')
                ->with('error', 'Active subscription required to access that module.');
        }

        return $next($request);
    }
}
