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
            ->select('id', 'tracking_number', 'order_id', 'driver_id', 'recipient_name', 'delivery_address', 'scheduled_date', 'status', 'cod_amount', 'cod_collected');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('scheduled_date', $request->date);
        }

        $deliveries = $query->latest('scheduled_date')->paginate(20);

        // Single query for COD pending
        $codPending = Delivery::byFarmOwner($farmOwner->id)
            ->where('cod_collected', false)
            ->where('cod_amount', '>', 0)
            ->sum('cod_amount');

        $stats = [
            'pending' => Delivery::byFarmOwner($farmOwner->id)->byStatus('pending')->count(),
            'dispatched' => Delivery::byFarmOwner($farmOwner->id)->byStatus('dispatched')->count(),
            'delivered_today' => Delivery::byFarmOwner($farmOwner->id)->byStatus('delivered')
                ->whereDate('delivered_at', today())->count(),
            'cod_pending' => $codPending,
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
            ->select('id', 'order_number')
            ->get();

        return view('farmowner.deliveries.create', compact('drivers', 'orders'));
    }

    public function store(Request $request)
    {
        $farmOwner = $this->getFarmOwner();

        $validated = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'scheduled_date' => 'required|date',
            'scheduled_time_from' => 'nullable|date_format:H:i',
            'scheduled_time_to' => 'nullable|date_format:H:i',
            'delivery_fee' => 'nullable|numeric|min:0',
            'cod_amount' => 'nullable|numeric|min:0',
            'special_instructions' => 'nullable|string',
            'delivery_notes' => 'nullable|string',
        ]);

        $validated['farm_owner_id'] = $farmOwner->id;
        $validated['assigned_by'] = Auth::id();

        $delivery = Delivery::create($validated);

        // If driver assigned, auto-assign
        if ($delivery->driver_id) {
            $delivery->assignDriver($delivery->driver_id, Auth::id());
        }

        return redirect()->route('deliveries.index')->with('success', 'Delivery created.');
    }

    public function show(Delivery $delivery)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($delivery->farm_owner_id !== $farmOwner->id, 403);

        $delivery->load(['order', 'driver', 'assignedBy']);

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
            'recipient_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string',
            'scheduled_date' => 'required|date',
            'scheduled_time_from' => 'nullable|date_format:H:i',
            'scheduled_time_to' => 'nullable|date_format:H:i',
            'delivery_fee' => 'nullable|numeric|min:0',
            'special_instructions' => 'nullable|string',
            'delivery_notes' => 'nullable|string',
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

        $delivery->assignDriver($validated['driver_id'], Auth::id());

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
            'cod_collected' => 'nullable|boolean',
            'proof_of_delivery' => 'nullable|string|max:255',
            'delivery_notes' => 'nullable|string',
        ]);

        $delivery->markDelivered($validated['proof_of_delivery'] ?? null);

        // Update COD collection status if applicable
        if (isset($validated['cod_collected'])) {
            $delivery->update(['cod_collected' => $validated['cod_collected']]);
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
            ->scheduledToday()
            ->with('driver:id,name')
            ->orderBy('scheduled_time_from')
            ->get();

        $tomorrow = Delivery::byFarmOwner($farmOwner->id)
            ->whereDate('scheduled_date', today()->addDay())
            ->with('driver:id,name')
            ->orderBy('scheduled_time_from')
            ->get();

        $unscheduled = Delivery::byFarmOwner($farmOwner->id)
            ->byStatus('pending')
            ->whereNull('driver_id')
            ->get();

        $drivers = Driver::byFarmOwner($farmOwner->id)->available()->get();

        return view('farmowner.deliveries.schedule', compact('today', 'tomorrow', 'unscheduled', 'drivers'));
    }
}
