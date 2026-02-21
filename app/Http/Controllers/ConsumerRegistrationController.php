<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\ConsumerRegistrationRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConsumerRegistrationController extends Controller
{
    public function store(ConsumerRegistrationRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::create([
                'name'         => $validated['full_name'],
                'email'        => $validated['email'],
                'phone'        => $validated['phone_number'],
                'password'     => Hash::make($validated['password']),
                'role'         => 'consumer',
                'status'       => 'active',
            ]);

            Log::info('Consumer registered successfully', [
                'user_id' => $user->id,
                'email' => $validated['email'],
            ]);

            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Welcome! Your consumer account has been created.');
        } catch (\Exception $e) {
            Log::error('Consumer registration failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to create account. Please try again.']);
        }
    }
}