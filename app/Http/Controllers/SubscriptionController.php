<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class SubscriptionController extends Controller
{
    /**
     * Show the subscription selection page.
     */
    public function index()
    {
        return view('auth.subscription-select');
    }

    /**
     * Generate a PayMongo Payment Link based on the selected plan.
     */
    public function pay(Request $request)
    {
        $plan = $request->query('plan');
        
        // Define discounted prices in centavos (PHP amount * 100)
        $prices = [
            '1_month'  => 3500,  // ₱35.00
            '6_month'  => 20500, // ₱205.00
            '12_month' => 41000  // ₱410.00
        ];

        if (!array_key_exists($plan, $prices)) {
            return back()->with('error', 'Invalid plan selected.');
        }

        // Create the PayMongo Payment Link with the success_url included
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode(config('services.paymongo.secret_key') . ':'),
        ])->post('https://api.paymongo.com/v1/links', [
            'data' => [
                'attributes' => [
                    'amount'      => $prices[$plan],
                    'description' => "Poultry System Subscription: " . strtoupper(str_replace('_', ' ', $plan)),
                    'remarks'     => "USER_ID:" . Auth::id(),
                    'success_url' => route('payment.success') // Redirects here after payment!
                ]
            ]
        ]);

        if ($response->successful()) {
            $linkData = $response->json()['data']['attributes'];
            return redirect($linkData['checkout_url']);
        }

        return back()->with('error', 'PayMongo Error: Could not generate payment link.');
    }

    /**
     * Handle the PayMongo Webhook to automate account activation.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();

        // Extract data from the webhook payload
        $attributes = $payload['data']['attributes'] ?? null;
        if (!$attributes) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Payload'], 400);
        }

        $remarks = $attributes['remarks'] ?? '';
        $amount  = $attributes['amount'] ?? 0;

        // Identify the User from the remarks
        if (str_contains($remarks, 'USER_ID:')) {
            $userId = explode(':', $remarks)[1];
            $user = User::find($userId);

            if ($user) {
                // Determine subscription duration
                $months = 1;
                if ($amount == 20500) $months = 6;
                if ($amount == 41000) $months = 12;

                // Upgrade user role and set expiration date
                $user->update([
                    'role'             => 'client',
                    'subscription_end' => now()->addMonths($months),
                    'status'           => 'active'
                ]);

                Log::info("Subscription activated for User ID: $userId for $months month(s).");
                return response()->json(['status' => 'success'], 200);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
    }

    /**
     * Success page shown after payment.
     */
    public function success()
    {
        return view('auth.payment-success');
    }
}