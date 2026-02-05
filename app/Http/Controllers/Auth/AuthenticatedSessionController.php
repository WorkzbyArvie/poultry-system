<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    $user = Auth::user();

    // Ensure Super Admins go to their specific Poultry Admin portal
    if ($user->role === 'superadmin') {
        return redirect()->route('superadmin.dashboard');
    }

    if ($user->role === 'client') {
        return redirect()->route('client.dashboard');
    }

    // Only fallback to home if no role matches
    return redirect('/');
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. Get the role BEFORE logging out so we know where to send them
        $role = Auth::user() ? Auth::user()->role : null;

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 2. Redirect based on the captured role
        if ($role === 'superadmin') {
            // This sends Super Admins back to the main portal page
            return redirect('/'); 
        }

        // This sends Clients/Consumers back to the specific Login Page
        return redirect('/login');
    }
}