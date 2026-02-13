<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Subscription;

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
        try {
            $plan = $request->query('plan');
            
            // Define prices in centavos (PHP amount * 100)
            $prices = [
                '1_month'  => 3500,   // ₱35.00
                '6_month'  => 20500,  // ₱205.00 (approx ₱34/month)
                '12_month' => 41000   // ₱410.00 (approx ₱34/month)
            ];

            if (!array_key_exists($plan, $prices)) {
                return back()->withErrors(['plan' => 'Invalid plan selected.']);
            }

            // Create PayMongo Payment Link
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode(config('services.paymongo.secret_key') . ':'),
            ])->post('https://api.paymongo.com/v1/links', [
                'data' => [
                    'attributes' => [
                        'amount'      => $prices[$plan],
                        'description' => "Poultry System - " . strtoupper(str_replace('_', ' ', $plan)),
                        'remarks'     => "USER_ID:" . Auth::id() . "|PLAN:" . $plan,
                        'success_url' => route('payment.success', ['plan' => $plan]),
                        'client_key'  => config('services.paymongo.public_key'),
                    ]
                ]
            ]);

            if (!$response->successful()) {
                Log::error('PayMongo Error: ' . $response->body());
                return back()->withErrors(['payment' => 'Could not generate payment link. Please try again.']);
            }

            $linkData = $response->json()['data']['attributes'];
            return redirect($linkData['checkout_url']);
        } catch (\Exception $e) {
            Log::error('Subscription pay error: ' . $e->getMessage());
            return back()->withErrors(['payment' => 'An unexpected error occurred.']);
        }
    }

    /**
     * Handle the PayMongo Webhook to automate subscription activation.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();

        // Verify webhook signature (implement this for security)
        // $this->verifyPayMongoSignature($request);

        $attributes = $payload['data']['attributes'] ?? null;
        if (!$attributes) {
            Log::warning('Invalid webhook payload: missing attributes');
            return response()->json(['status' => 'error'], 400);
        }

        $remarks = $attributes['remarks'] ?? '';
        $amount  = $attributes['amount'] ?? 0;
        $status  = $attributes['status'] ?? null;

        // Only process successful payments
        if ($status !== 'paid') {
            return response()->json(['status' => 'ignored'], 200);
        }

        // Extract user ID and plan from remarks
        if (!str_contains($remarks, 'USER_ID:')) {
            Log::warning('Invalid webhook remarks: ' . $remarks);
            return response()->json(['status' => 'error'], 400);
        }

        $parts = explode('|', $remarks);
        $userId = explode(':', $parts[0])[1] ?? null;
        $plan = isset($parts[1]) ? explode(':', $parts[1])[1] : '1_month';

        if (!$userId) {
            Log::warning('Could not extract user ID from remarks');
            return response()->json(['status' => 'error'], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            Log::warning('User not found: ' . $userId);
            return response()->json(['status' => 'error'], 404);
        }

        // Determine subscription duration based on amount
        $planDurations = [
            3500   => 1,   // 1 month
            20500  => 6,   // 6 months
            41000  => 12,  // 12 months
        ];

        $months = $planDurations[$amount] ?? 1;

        // Create or update subscription
        $subscription = Subscription::create([
            'user_id' => $userId,
            'plan' => $plan,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addMonths($months),
            'payment_reference' => $attributes['id'] ?? null,
        ]);

        // Ensure user has client role
        if ($user->role !== 'client') {
            $user->update(['role' => 'client', 'status' => 'active']);
        }

        Log::info("Subscription activated for User ID: $userId, Plan: $plan, Duration: $months months");
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Success page shown after payment completion.
     */
    public function success(Request $request)
    {
        $user = Auth::user();
        $activeSubscription = $user->activeSubscription;
        
        return view('auth.payment-success', [
            'subscription' => $activeSubscription,
            'daysRemaining' => $activeSubscription?->daysRemaining() ?? 0,
        ]);
    }
}