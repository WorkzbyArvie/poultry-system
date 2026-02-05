<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ConsumerRegistrationController;
use App\Http\Controllers\ClientRequestController;
use App\Http\Controllers\EggController;
use App\Http\Controllers\ChickenController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| 1. Public / Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', function () {
    return view('auth.register-select');
})->name('register');

Route::get('/client/register', function () {
    return view('auth.client-register');
})->name('client.register');

Route::get('/consumer/register', function () {
    return view('auth.consumer-register');
})->name('consumer.register');

Route::post('/client/register', [ClientRequestController::class, 'store'])->name('client.request.store');
Route::post('/consumer/register', [ConsumerRegistrationController::class, 'store'])->name('consumer.store');

/*
|--------------------------------------------------------------------------
| 2. Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 1. THIS IS THE ONLY DASHBOARD ROUTE YOU NEED
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        if ($user->role === 'superadmin') {
            // This sends you to the correct orange/black portal
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->role === 'client') {
            return redirect()->route('client.dashboard');
        }

        // Fallback for others (like consumers)
        return view('dashboard'); 
    })->name('dashboard');

    // 2. YOUR POULTRY ADMIN PORTAL
    Route::get('/super-admin/dashboard', [SuperAdminController::class, 'index'])->name('superadmin.dashboard');
    
    // ... rest of your routes ...
});

    // --- Super Admin Portal Routes ---
    Route::get('/super-admin/dashboard', [SuperAdminController::class, 'index'])->name('superadmin.dashboard');
    Route::get('/admin/verifications', [SuperAdminController::class, 'verifications'])->name('admin.verifications');
    Route::post('/admin/verifications/{id}/approve', [SuperAdminController::class, 'approveVerification'])->name('admin.verifications.approve');
    Route::post('/admin/verifications/{id}/reject', [SuperAdminController::class, 'rejectVerification'])->name('admin.verifications.reject');
    Route::get('/super-admin/eggs', [EggController::class, 'index'])->name('eggs.index');
    Route::get('/super-admin/chickens', [ChickenController::class, 'index'])->name('chickens.index');
    Route::get('/super-admin/staff/create', function () {
        return view('superadmin.add-staff');
    })->name('staff.create');

    // --- Subscription & Payment System ---
    Route::get('/subscribe', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscribe/pay', [SubscriptionController::class, 'pay'])->name('subscription.pay');
    Route::get('/payment/success', [SubscriptionController::class, 'success'])->name('payment.success');

    // --- Profile Management ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Client Dashboard ---
    Route::get('/client/dashboard', function () {
        $user = Auth::user();
        // Fallback for days remaining
        $daysRemaining = $user->user_subscription_end ? now()->diffInDays($user->user_subscription_end, false) : 30;
        $daysRemaining = $daysRemaining > 0 ? (int)$daysRemaining : 0;
        
        return view('client.dashboard', compact('daysRemaining'));
    })->name('client.dashboard');



// Logout Route (Must be OUTSIDE the auth group to be accessible)
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

require __DIR__.'/auth.php';