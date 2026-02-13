<?php

namespace App\Http\Controllers;

use App\Models\FarmOwner;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FarmOwnerController extends Controller
{
    public function show_registration_form()
    {
        return view('farmowner.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'farm_name' => 'required|string|max:255|unique:farm_owners',
            'farm_address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'business_registration_number' => 'required|string|unique:farm_owners',
        ]);

        $user = Auth::user();

        if ($user->farmOwner) {
            return redirect()->back()->with('error', 'You already have a farm registered');
        }

        $farm_owner = FarmOwner::create([
            'user_id' => $user->id,
            ...$validated,
            'permit_status' => 'pending',
            'subscription_status' => 'inactive',
        ]);

        Log::info('Farm owner registration submitted', ['user_id' => $user->id, 'farm_id' => $farm_owner->id]);

        return redirect()->route('farmowner.dashboard')->with('success', 'Farm registered. Awaiting admin approval.');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $farm_owner = $user->farmOwner;

        if (!$farm_owner) {
            return redirect()->route('farmowner.login');
        }

        $stats = cache()->remember("farm_{$farm_owner->id}_stats", 300, function () use ($farm_owner) {
            return [
                'total_products' => $farm_owner->products()->count(),
                'total_orders' => $farm_owner->orders()->count(),
                'active_subscription' => $farm_owner->subscriptions()->where('status', 'active')->exists(),
                'permit_status' => $farm_owner->permit_status,
            ];
        });

        $products = $farm_owner->products()
            ->select('id', 'farm_owner_id', 'name', 'price', 'quantity_available', 'created_at')
            ->latest('created_at')
            ->limit(5)
            ->get();

        $recent_orders = $farm_owner->orders()
            ->select('id', 'farm_owner_id', 'consumer_id', 'total_amount', 'status', 'created_at')
            ->with('consumer:id,name')
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('farmowner.dashboard', compact('farm_owner', 'stats', 'products', 'recent_orders'));
    }

    public function profile()
    {
        $farm_owner = Auth::user()->farmOwner;
        
        if (!$farm_owner) {
            return redirect()->route('farmowner.register');
        }

        return view('farmowner.profile', compact('farm_owner'));
    }

    public function update_profile(Request $request)
    {
        $farm_owner = Auth::user()->farmOwner;

        if (!$farm_owner) {
            return redirect()->route('farmowner.register');
        }

        $validated = $request->validate([
            'farm_address' => 'string|max:500',
            'city' => 'string|max:255',
            'province' => 'string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $farm_owner->update($validated);

        return redirect()->back()->with('success', 'Farm profile updated');
    }

    public function subscriptions()
    {
        $farm_owner = Auth::user()->farmOwner;

        if (!$farm_owner) {
            return redirect()->route('farmowner.register');
        }

        $subscriptions = $farm_owner->subscriptions()
            ->select('id', 'farm_owner_id', 'plan_type', 'status', 'started_at', 'expires_at', 'created_at')
            ->latest('created_at')
            ->paginate(10);

        return view('farmowner.subscriptions', compact('subscriptions'));
    }
}
