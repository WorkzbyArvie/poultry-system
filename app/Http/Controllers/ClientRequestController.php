<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ClientRegistrationRequest;
use App\Models\ClientRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ClientRequestController extends Controller
{
    /**
     * Store the farm owner's registration request.
     */
    public function store(ClientRegistrationRequest $request)
    {
        try {
            $validated = $request->validated();

            // Handle File Uploads
            $idPath = $request->file('valid_id')->store('uploads/ids', 'public');
            $permitPath = $request->file('business_permit')->store('uploads/permits', 'public');

            // Create the record
            ClientRequest::create([
                'owner_name'           => $validated['owner_name'],
                'farm_name'            => $validated['farm_name'],
                'email'                => $validated['email'],
                'farm_location'        => $validated['farm_location'],
                'valid_id_path'        => $idPath,
                'business_permit_path' => $permitPath,
                'password'             => Hash::make($validated['password']),
                'status'               => 'pending', 
            ]);

            Log::info('Client registration request created', [
                'farm_name' => $validated['farm_name'],
                'email' => $validated['email'],
            ]);

            return back()->with('success', 'Your farm application has been submitted! You can log in once the Super Admin approves your account.');
        } catch (\Exception $e) {
            Log::error('Client registration failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to submit registration. Please try again.']);
        }
    }
}