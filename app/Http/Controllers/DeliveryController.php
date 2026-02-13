<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Order;
use App\Models\FarmOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    private function getFarmOwner()
    {
        return FarmOwner::where('user_id', Auth::id())->firstOrFail();
    }

    public function index(Request $request)
    {
        $farmOwner = $this->getFarmOwner();
        
        $query = Delivery::byFarmOwner($farmOwner->id)
            ->with(['order:id,order_number', 'driver:id,name,phone'])
            ->select('id', 'delivery_number', 'order_id', 'driver_id', 'customer_name', 'delivery_address', 'scheduled_date', 'status', 'cod_amount', 'cod_collected');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('scheduled_date', $request->date);
        }

        $deliveries = $query->latest('scheduled_date')->paginate(20);

        $stats = [
            'pending' => Delivery::byFarmOwner($farmOwner->id)->byStatus('pending')->count(),
            'dispatched' => Delivery::byFarmOwner($farmOwner->id)->byStatus('dispatched')->count(),
            'delivered_today' => Delivery::byFarmOwner($farmOwner->id)->byStatus('delivered')
                ->whereDate('delivered_at', today())->count(),
            'cod_pending' => Delivery::byFarmOwner($farmOwner->id)
                ->whereColumn('cod_amount', '>', 'cod_collected')
                ->sum('cod_amount') - Delivery::byFarmOwner($farmOwner->id)
                ->whereColumn('cod_amount', '>', 'cod_collected')
                ->sum('cod_collected'),
        ];

        return view('farmowner.deliveries.index', compact('deliveries', 'stats'));
    }

    public function create()
    {
        $farmOwner = $this->getFarmOwner();
        
        $drivers = Driver::byFarmOwner($farmOwner->id)->available()->select('id', 'name', 'vehicle_type')->get();
        $orders = Order::where('farm_owner_id', $farmOwner->id)
            ->whereDoesntHave('delivery')
            ->where('status', 'confirmed')
            ->select('id', 'order_number', 'customer_name')
            ->get();

        return view('farmowner.deliveries.create', compact('drivers', 'orders'));
    }

    public function store(Request $request)
    {
        $farmOwner = $this->getFarmOwner();

        $validated = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string',
            'barangay' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|string|max:50',
            'total_weight_kg' => 'nullable|numeric|min:0',
            'number_of_items' => 'nullable|integer|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'cod_amount' => 'nullable|numeric|min:0',
            'special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['farm_owner_id'] = $farmOwner->id;
        $validated['created_by'] = Auth::id();

        // Generate delivery number
        $count = Delivery::byFarmOwner($farmOwner->id)->whereYear('created_at', now()->year)->count() + 1;
        $validated['delivery_number'] = 'DEL-' . now()->format('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $delivery = Delivery::create($validated);

        // If driver assigned, auto-assign
        if ($delivery->driver_id) {
            $delivery->assignDriver($delivery->driver_id);
        }

        return redirect()->route('deliveries.index')->with('success', 'Delivery created.');
    }

    public function show(Delivery $delivery)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($delivery->farm_owner_id !== $farmOwner->id, 403);

        $delivery->load(['order', 'driver', 'createdBy']);

        return view('farmowner.deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($delivery->farm_owner_id !== $farmOwner->id, 403);
        abort_if($delivery->status === 'delivered', 403, 'Cannot edit delivered orders.');

        $drivers = Driver::byFarmOwner($farmOwner->id)->available()->select('id', 'name', 'vehicle_type')->get();

        return view('farmowner.deliveries.edit', compact('delivery', 'drivers'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($delivery->farm_owner_id !== $farmOwner->id, 403);

        $validated = $request->validate([
            'driver_id' => 'nullable|exists:drivers,id',
            'customer_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|string|max:50',
            'delivery_fee' => 'nullable|numeric|min:0',
            'special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $delivery->update($validated);

        return redirect()->route('deliveries.show', $delivery)->with('success', 'Delivery updated.');
    }

    public function assignDriver(Request $request, Delivery $delivery)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($delivery->farm_owner_id !== $farmOwner->id, 403);

        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
        ]);

        $delivery->assignDriver($validated['driver_id']);

        return redirect()->route('deliveries.show', $delivery)->with('success', 'Driver assigned.');
    }

    public function dispatch(Delivery $delivery)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($delivery->farm_owner_id !== $farmOwner->id, 403);
        abort_unless($delivery->driver_id, 403, 'Assign driver first.');

        $delivery->dispatch();

        return redirect()->route('deliveries.show', $delivery)->with('success', 'Delivery dispatched.');
    }

    public function markDelivered(Request $request, Delivery $delivery)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($delivery->farm_owner_id !== $farmOwner->id, 403);

        $validated = $request->validate([
            'cod_collected' => 'nullable|numeric|min:0',
            'proof_of_delivery' => 'nullable|string|max:255',
            'receiver_name' => 'nullable|string|max:255',
            'delivery_notes' => 'nullable|string',
        ]);

        $delivery->markDelivered(
            $validated['cod_collected'] ?? 0,
            $validated['proof_of_delivery'] ?? null
        );

        if (isset($validated['receiver_name'])) {
            $delivery->update(['receiver_name' => $validated['receiver_name']]);
        }

        return redirect()->route('deliveries.show', $delivery)->with('success', 'Delivery completed.');
    }

    public function markFailed(Request $request, Delivery $delivery)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($delivery->farm_owner_id !== $farmOwner->id, 403);

        $validated = $request->validate([
            'failure_reason' => 'required|string|max:255',
        ]);

        $delivery->markFailed($validated['failure_reason']);

        return redirect()->route('deliveries.show', $delivery)->with('success', 'Delivery marked as failed.');
    }

    public function schedule()
    {
        $farmOwner = $this->getFarmOwner();

        $today = Delivery::byFarmOwner($farmOwner->id)
            ->scheduledFor(today())
            ->with('driver:id,name')
            ->orderBy('scheduled_time')
            ->get();

        $tomorrow = Delivery::byFarmOwner($farmOwner->id)
            ->scheduledFor(today()->addDay())
            ->with('driver:id,name')
            ->orderBy('scheduled_time')
            ->get();

        $unscheduled = Delivery::byFarmOwner($farmOwner->id)
            ->byStatus('pending')
            ->whereNull('driver_id')
            ->get();

        $drivers = Driver::byFarmOwner($farmOwner->id)->available()->get();

        return view('farmowner.deliveries.schedule', compact('today', 'tomorrow', 'unscheduled', 'drivers'));
    }
}
