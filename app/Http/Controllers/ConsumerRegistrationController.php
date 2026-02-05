<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ConsumerRegistrationController extends Controller
{
   public function store(Request $request)
{
    $request->validate([
        'full_name'    => 'required|string|max:255',
        'email'        => 'required|string|email|max:255|unique:laravel.app_users,email',
        'phone_number' => 'required|string|max:20',
        'address'      => 'required|string',
        'password'     => 'required|string|min:8|confirmed',
    ]);

    $user = \App\Models\User::create([
        'full_name'    => $request->full_name,
        'username'     => \Illuminate\Support\Str::slug($request->full_name) . rand(10, 99),
        'email'        => $request->email,
        'phone_number' => $request->phone_number,
        'address'      => $request->address,
        'password'     => \Illuminate\Support\Facades\Hash::make($request->password),
        'role'         => 'consumer',
        'status'       => 'active',
    ]);

    \Illuminate\Support\Facades\Auth::login($user);

    return redirect()->route('dashboard'); // Redirect to their new consumer dashboard
}
}