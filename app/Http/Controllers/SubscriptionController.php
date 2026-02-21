<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Subscription;
use App\Models\FarmOwner;
use App\Services\PayMongoService;

class SubscriptionController extends Controller
{
    protected PayMongoService $paymongo;

    /**
     * Plan configuration: prices in centavos, limits, durations.
     */
    protected array $plans = [
        'starter' => [
            'amount'          => 3000,       // ₱30.00 in centavos
            'product_limit'   => 2,
            'order_limit'     => 50,
            'commission_rate' => 5.00,
            'monthly_cost'    => 30,
            'months'          => 1,
            'label'           => 'Starter Plan',
        ],
        'professional' => [
            'amount'          => 50000,      // ₱500.00
            'product_limit'   => 10,
            'order_limit'     => 200,
            'commission_rate' => 3.00,
            'monthly_cost'    => 500,
            'months'          => 1,
            'label'           => 'Professional Plan',
        ],
        'enterprise' => [
            'amount'          => 120000,     // ₱1,200.00
            'product_limit'   => null,       // unlimited
            'order_limit'     => null,       // unlimited
            'commission_rate' => 1.50,
            'monthly_cost'    => 1200,
            'months'          => 1,
            'label'           => 'Enterprise Plan',
        ],
    ];

    public function __construct(PayMongoService $paymongo)
    {
        $this->paymongo = $paymongo;
    }

    /**
     * Show the subscription selection page.
     */
    public function index()
    {
        return view('auth.subscription-select');
    }

    /**
     * Create a PayMongo Checkout Session and redirect user.
     */
    public function pay(Request $request)
    {
        try {
            $plan = $request->query('plan');

            if (!array_key_exists($plan, $this->plans)) {
                return back()->withErrors(['plan' => 'Invalid plan selected.']);
            }

            $planConfig = $this->plans[$plan];
            $user = Auth::user();
            $farmOwner = FarmOwner::where('user_id', $user->id)->first();

            if (!$farmOwner) {
                return back()->withErrors(['payment' => 'Farm owner profile not found.']);
            }

            // Check if already has active subscription for this plan
            $existingActive = $farmOwner->subscriptions()
                ->where('status', 'active')
                ->where('ends_at', '>', now())
                ->where('plan_type', $plan)
                ->first();

            if ($existingActive) {
                return redirect()->route('farmowner.subscriptions')
                    ->with('info', 'You already have an active ' . ucfirst($plan) . ' subscription.');
            }

            // Create PayMongo Checkout Session
            $checkoutData = $this->paymongo->createCheckoutSession([
                'amount'        => $planConfig['amount'],
                'plan_name'     => "Poultry System - {$planConfig['label']}",
                'description'   => "{$planConfig['label']} - ₱" . number_format($planConfig['monthly_cost']) . "/month",
                'plan'          => $plan,
                'user_id'       => (string) $user->id,
                'farm_owner_id' => (string) $farmOwner->id,
                'success_url'   => route('payment.success', ['plan' => $plan, 'session_id' => '{id}']),
                'cancel_url'    => route('farmowner.subscriptions'),
            ]);

            if (!$checkoutData) {
                // Fallback to Payment Link if checkout session fails
                return $this->payViaLink($plan, $planConfig, $user, $farmOwner);
            }

            $checkoutUrl = $checkoutData['attributes']['checkout_url'] ?? null;

            if (!$checkoutUrl) {
                Log::error('PayMongo: No checkout_url in response', ['data' => $checkoutData]);
                return back()->withErrors(['payment' => 'Could not create checkout session. Please try again.']);
            }

            // Store checkout session ID for verification on success
            Cache::put(
                "checkout_session_{$user->id}_{$plan}",
                $checkoutData['id'],
                now()->addHours(2)
            );

            return redirect($checkoutUrl);

        } catch (\Exception $e) {
            Log::error('Subscription pay error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['payment' => 'An unexpected error occurred. Please try again.']);
        }
    }

    /**
     * Fallback: Create a Payment Link if Checkout Session fails.
     */
    protected function payViaLink(string $plan, array $planConfig, $user, FarmOwner $farmOwner)
    {
        $remarks = "USER_ID:{$user->id}|FARM_OWNER_ID:{$farmOwner->id}|PLAN:{$plan}";

        $linkData = $this->paymongo->createPaymentLink([
            'amount'      => $planConfig['amount'],
            'description' => "Poultry System - {$planConfig['label']}",
            'remarks'     => $remarks,
        ]);

        if (!$linkData) {
            return back()->withErrors(['payment' => 'Could not generate payment link. Please try again.']);
        }

        $checkoutUrl = $linkData['attributes']['checkout_url'] ?? null;

        if (!$checkoutUrl) {
            return back()->withErrors(['payment' => 'Payment link was created but checkout URL is missing.']);
        }

        return redirect($checkoutUrl);
    }

    /**
     * Handle PayMongo webhook events.
     * This is called by PayMongo when a payment event occurs.
     */
    public function handleWebhook(Request $request)
    {
        $rawPayload = $request->getContent();
        $signatureHeader = $request->header('Paymongo-Signature', '');

        // Verify webhook signature
        if (!$this->paymongo->verifyWebhookSignature($rawPayload, $signatureHeader)) {
            Log::warning('PayMongo webhook: Invalid signature');
            return response()->json(['status' => 'invalid_signature'], 403);
        }

        $payload = $request->all();
        $eventType = $payload['data']['attributes']['type'] ?? null;

        Log::info('PayMongo webhook received', ['type' => $eventType]);

        // Handle checkout session payment completed
        if ($eventType === 'checkout_session.payment.paid') {
            return $this->handleCheckoutPayment($payload);
        }

        // Handle payment link paid (fallback method)
        if ($eventType === 'link.payment.paid') {
            return $this->handleLinkPayment($payload);
        }

        return response()->json(['status' => 'event_not_handled'], 200);
    }

    /**
     * Process a checkout session payment event.
     */
    protected function handleCheckoutPayment(array $payload): \Illuminate\Http\JsonResponse
    {
        $checkoutData = $payload['data']['attributes']['data'] ?? null;

        if (!$checkoutData) {
            Log::warning('PayMongo webhook: missing checkout data');
            return response()->json(['status' => 'error'], 400);
        }

        $attributes = $checkoutData['attributes'] ?? [];
        $metadata = $attributes['metadata'] ?? [];
        $payments = $attributes['payments'] ?? [];

        $userId = $metadata['user_id'] ?? null;
        $farmOwnerId = $metadata['farm_owner_id'] ?? null;
        $plan = $metadata['plan'] ?? 'starter';
        $paymentId = !empty($payments) ? ($payments[0]['id'] ?? null) : null;

        if (!$userId || !$farmOwnerId) {
            Log::warning('PayMongo webhook: missing user/farm_owner metadata', $metadata);
            return response()->json(['status' => 'error'], 400);
        }

        return $this->activateSubscription($userId, $farmOwnerId, $plan, $checkoutData['id'] ?? null, $paymentId);
    }

    /**
     * Process a payment link paid event.
     */
    protected function handleLinkPayment(array $payload): \Illuminate\Http\JsonResponse
    {
        $linkData = $payload['data']['attributes']['data'] ?? null;
        $attributes = $linkData['attributes'] ?? [];

        $remarks = $attributes['remarks'] ?? '';
        if (!str_contains($remarks, 'USER_ID:')) {
            Log::warning('PayMongo webhook: invalid remarks', ['remarks' => $remarks]);
            return response()->json(['status' => 'error'], 400);
        }

        // Parse remarks: USER_ID:x|FARM_OWNER_ID:y|PLAN:z
        $parts = collect(explode('|', $remarks))->mapWithKeys(function ($part) {
            $segments = explode(':', $part, 2);
            return [($segments[0] ?? '') => ($segments[1] ?? '')];
        });

        $userId = $parts['USER_ID'] ?? null;
        $farmOwnerId = $parts['FARM_OWNER_ID'] ?? null;
        $plan = $parts['PLAN'] ?? 'starter';
        $paymentId = $linkData['id'] ?? null;

        if (!$userId) {
            Log::warning('PayMongo webhook: no USER_ID in remarks');
            return response()->json(['status' => 'error'], 400);
        }

        // If farm_owner_id not in remarks, look it up
        if (!$farmOwnerId) {
            $farmOwner = FarmOwner::where('user_id', $userId)->first();
            $farmOwnerId = $farmOwner?->id;
        }

        return $this->activateSubscription($userId, $farmOwnerId, $plan, $paymentId);
    }

    /**
     * Activate a subscription after successful payment.
     */
    protected function activateSubscription(
        string $userId,
        ?string $farmOwnerId,
        string $plan,
        ?string $paymongoId = null,
        ?string $paymentMethodId = null
    ): \Illuminate\Http\JsonResponse {
        $user = User::find($userId);
        if (!$user) {
            Log::warning('PayMongo webhook: user not found', ['user_id' => $userId]);
            return response()->json(['status' => 'error'], 404);
        }

        $farmOwner = $farmOwnerId
            ? FarmOwner::find($farmOwnerId)
            : FarmOwner::where('user_id', $userId)->first();

        if (!$farmOwner) {
            Log::warning('PayMongo webhook: farm owner not found', ['user_id' => $userId]);
            return response()->json(['status' => 'error'], 404);
        }

        // Validate plan type - only accept valid plans
        if (!array_key_exists($plan, $this->plans)) {
            $plan = 'starter';
        }

        $planConfig = $this->plans[$plan];

        // Check for duplicate payment (idempotency)
        if ($paymongoId) {
            $existing = Subscription::where('paymongo_subscription_id', $paymongoId)->first();
            if ($existing) {
                Log::info('PayMongo webhook: duplicate payment ignored', ['paymongo_id' => $paymongoId]);
                return response()->json(['status' => 'already_processed'], 200);
            }
        }

        DB::transaction(function () use ($farmOwner, $plan, $planConfig, $paymongoId, $paymentMethodId) {
            // Expire existing active subscriptions
            $farmOwner->subscriptions()
                ->where('status', 'active')
                ->update([
                    'status' => 'expired',
                    'ends_at' => now(),
                ]);

            // Create new subscription
            Subscription::create([
                'farm_owner_id'              => $farmOwner->id,
                'plan_type'                  => $plan,
                'monthly_cost'               => $planConfig['monthly_cost'],
                'product_limit'              => $planConfig['product_limit'],
                'order_limit'                => $planConfig['order_limit'],
                'commission_rate'            => $planConfig['commission_rate'],
                'status'                     => 'active',
                'started_at'                 => now(),
                'ends_at'                    => now()->addMonths($planConfig['months']),
                'renewal_at'                 => now()->addMonths($planConfig['months'])->subDays(3),
                'paymongo_subscription_id'   => $paymongoId,
                'paymongo_payment_method_id' => $paymentMethodId,
            ]);

            // Update farm owner status
            $farmOwner->update(['subscription_status' => 'active']);
        });

        Log::info("Subscription activated", [
            'user_id' => $userId,
            'farm_owner_id' => $farmOwner->id,
            'plan' => $plan,
            'paymongo_id' => $paymongoId,
        ]);

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Success page after payment — verify with PayMongo and activate if webhook hasn't fired yet.
     */
    public function success(Request $request)
    {
        $user = Auth::user();
        $plan = $request->query('plan', 'starter');
        $farmOwner = FarmOwner::where('user_id', $user->id)->first();

        // Try to verify payment via cached checkout session
        if ($farmOwner && array_key_exists($plan, $this->plans)) {
            $cacheKey = "checkout_session_{$user->id}_{$plan}";
            $checkoutSessionId = Cache::get($cacheKey);

            if ($checkoutSessionId) {
                // Retrieve checkout session from PayMongo to verify payment
                $sessionData = $this->paymongo->retrieveCheckoutSession($checkoutSessionId);

                if ($sessionData) {
                    $status = $sessionData['attributes']['payment_intent']['attributes']['status'] ?? null;
                    $payments = $sessionData['attributes']['payments'] ?? [];

                    // If payment succeeded and no active subscription yet, activate it
                    if (($status === 'succeeded' || !empty($payments)) && 
                        !$farmOwner->subscriptions()->where('status', 'active')->where('paymongo_subscription_id', $checkoutSessionId)->exists()) {
                        
                        $paymentId = !empty($payments) ? ($payments[0]['id'] ?? null) : null;
                        $this->activateSubscription(
                            (string) $user->id,
                            (string) $farmOwner->id,
                            $plan,
                            $checkoutSessionId,
                            $paymentId
                        );
                    }
                }

                Cache::forget($cacheKey);
            }
        }

        // Get the current active subscription to display
        $activeSubscription = $farmOwner?->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest()
            ->first();

        return view('auth.payment-success', [
            'subscription' => $activeSubscription,
            'plan' => $plan,
        ]);
    }
}