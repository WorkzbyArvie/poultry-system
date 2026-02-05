<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\ClientRequest; 
use App\Models\User; // Added to handle user creation

class SuperAdminController extends Controller
{
    /**
     * Show the Super Admin Dashboard.
     */
    public function index(): View
    {
        return view('superadmin.dashboard');
    }

    /**
     * Fetch all pending farm registrations for the verification tab.
     */
    public function verifications(): View
    {
        // 1. Fetch all pending registrations from the database
       // Change 'pending_approval' to 'pending' to match your database
$requests = ClientRequest::where('status', 'pending')->get();
        
        // 2. Pass the $requests variable to the view
        return view('superadmin.verifications', compact('requests'));
    }

    /**
     * Approve a farm and automatically create a user account.
     */
    public function approveVerification($id)
{
    $clientRequest = \App\Models\ClientRequest::findOrFail($id);

    // Create the user using the credentials they provided
    $user = User::create([
        'full_name' => $clientRequest->owner_name,
        'username'  => strtolower(str_replace(' ', '', $clientRequest->owner_name)) . rand(10, 99),
        'email'     => $clientRequest->email,
        'password'  => $clientRequest->password, // This is already hashed from the request table!
        'role'      => 'client',
        'status'    => 'active',
    ]);

    $clientRequest->update(['status' => 'accepted']);

    return redirect()->back()->with('success', 'Farm Owner Approved!');
}

    /**
     * Reject a farm registration.
     */
    public function rejectVerification($id)
    {
        $request = ClientRequest::findOrFail($id);
        $request->update(['status' => 'rejected']);

        return back()->with('error', 'Farm application has been rejected.');
    }
}