<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FarmOwner;
use App\Models\Order;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SuperAdminController extends Controller
{

    public function index()
    {
        try {
            $stats = cache()->remember('admin_dashboard_stats', 300, function () {
                return [
                    'total_farm_owners' => FarmOwner::count(),
                    'pending_verifications' => FarmOwner::where('permit_status', 'pending')->count(),
                    'active_subscriptions' => Subscription::where('status', 'active')->count(),
                    'total_orders' => Order::count(),
                    'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount') ?? 0,
                    'total_users' => User::count(),
                ];
            });

            $recent_farm_owners = FarmOwner::with('user:id,name,email')
                ->select('id', 'user_id', 'farm_name', 'permit_status', 'created_at')
                ->latest('created_at')
                ->limit(10)
                ->get();

            $pending_farm_owners = FarmOwner::with('user:id,name,email')
                ->select('id', 'user_id', 'farm_name', 'permit_status', 'created_at')
                ->where('permit_status', 'pending')
                ->latest('created_at')
                ->limit(5)
                ->get();

            return view('superadmin.dashboard', compact('stats', 'recent_farm_owners', 'pending_farm_owners'));
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            return view('superadmin.dashboard', [
                'stats' => ['total_users' => 0, 'total_farm_owners' => 0, 'pending_verifications' => 0, 'active_subscriptions' => 0, 'total_orders' => 0, 'total_revenue' => 0],
                'recent_farm_owners' => collect([]),
                'pending_farm_owners' => collect([]),
            ]);
        }
    }

    public function farm_owners()
    {
        $farm_owners = FarmOwner::with('user:id,name,email')
            ->select('id', 'user_id', 'farm_name', 'permit_status', 'created_at')
            ->withCount('products', 'orders')
            ->latest('created_at')
            ->paginate(20);

        return view('superadmin.farm-owners', compact('farm_owners'));
    }

    public function approve_farm_owner($id)
    {
        $farm_owner = FarmOwner::findOrFail($id);
        
        if ($farm_owner->permit_status !== 'pending') {
            return redirect()->back()->with('error', 'Farm owner is not pending approval');
        }

        $farm_owner->update(['permit_status' => 'approved']);
        
        Log::info('Farm owner approved', ['farm_owner_id' => $id, 'user_id' => $farm_owner->user_id]);
        
        return redirect()->back()->with('success', 'Farm owner approved successfully');
    }

    public function reject_farm_owner($id, Request $request)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        
        $farm_owner = FarmOwner::findOrFail($id);
        $farm_owner->update(['permit_status' => 'rejected']);
        
        Log::info('Farm owner rejected', ['farm_owner_id' => $id, 'reason' => $request->reason]);
        
        return redirect()->back()->with('success', 'Farm owner rejected');
    }

    public function orders()
    {
        $orders = Order::with([
            'consumer:id,name,email',
            'farmOwner:id,farm_name,user_id'
        ])
        ->select('id', 'consumer_id', 'farm_owner_id', 'total_amount', 'status', 'payment_status', 'created_at')
        ->latest('created_at')
        ->paginate(20);

        $stats = cache()->remember('admin_orders_stats', 300, function () {
            return [
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'paid_orders' => Order::where('payment_status', 'paid')->count(),
                'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount') ?? 0,
            ];
        });

        return view('superadmin.orders', compact('orders', 'stats'));
    }

    public function subscriptions()
    {
        $subscriptions = Subscription::with([
            'farmOwner:id,farm_name,user_id',
            'farmOwner.user:id,name,email'
        ])
        ->select('id', 'farm_owner_id', 'plan_type', 'status', 'started_at', 'expires_at', 'payment_reference')
        ->latest('created_at')
        ->paginate(20);

        return view('superadmin.subscriptions', compact('subscriptions'));
    }

    public function users()
    {
        $users = User::select('id', 'name', 'email', 'role', 'status', 'email_verified_at', 'created_at')
            ->latest('created_at')
            ->paginate(30);
        
        return view('superadmin.users', compact('users'));
    }
}