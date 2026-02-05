<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ClientRequestController extends Controller
{
    /**
     * Store the farm owner's registration request.
     */
    public function store(Request $request)
    {
        // 1. Validate requirements including the new password fields
        $request->validate([
            'owner_name'      => 'required|string|max:255',
            'farm_name'       => 'required|string|max:255',
            'email'           => 'required|email|unique:client_requests,email', // Added email validation
            'farm_location'   => 'required|string',
            'valid_id'        => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'business_permit' => 'required|mimes:pdf,jpeg,png,jpg|max:2048',
            // 'confirmed' requires an input named 'password_confirmation' in your blade
            'password'        => 'required|string|min:8|confirmed', 
        ]);

        // 2. Handle File Uploads
        $idPath = $request->file('valid_id')->store('uploads/ids', 'public');
        $permitPath = $request->file('business_permit')->store('uploads/permits', 'public');

        // 3. Create the record in your 'client_requests' table
        ClientRequest::create([
            'owner_name'           => $request->owner_name,
            'farm_name'            => $request->farm_name,
            'email'                => $request->email,
            'farm_location'        => $request->farm_location,
            'valid_id_path'        => $idPath,
            'business_permit_path' => $permitPath,
            'password'             => Hash::make($request->password), // HASHED FOR SECURITY
            'status'               => 'pending', 
        ]);

        // 4. Send the user back
        return back()->with('message', 'Your farm application has been submitted! You can log in once the Super Admin approves your account.');
    }
}