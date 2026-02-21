<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\FarmOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'farm_owner') {
            $farm_owner = $user->farmOwner;
            $orders = Order::where('farm_owner_id', $farm_owner->id)
                ->with('consumer')
                ->latest('created_at')
                ->paginate(20);
            return view('orders.farm-owner-list', compact('orders'));
        }

        $orders = Order::where('consumer_id', $user->id)
            ->with(['farmOwner.user', 'items.product'])
            ->latest('created_at')
            ->paginate(20);

        return view('orders.consumer-list', compact('orders'));
    }

    public function show(Order $order)
    {
        $user = Auth::user();

        if ($user->role === 'farm_owner') {
            if ($order->farm_owner_id !== $user->farmOwner->id) {
                abort(403);
            }
        } else {
            if ($order->consumer_id !== $user->id) {
                abort(403);
            }
        }

        return view('orders.show', compact('order'));
    }

    public function cart_add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->quantity_available < $validated['quantity']) {
            return response()->json(['error' => 'Insufficient stock'], 400);
        }

        $cart = session()->get('cart', []);
        $cart_key = 'product_' . $product->id;

        if (isset($cart[$cart_key])) {
            $cart[$cart_key]['quantity'] += $validated['quantity'];
        } else {
            $cart[$cart_key] = [
                'product_id' => $product->id,
                'farm_owner_id' => $product->farm_owner_id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $validated['quantity'],
            ];
        }

        session()->put('cart', $cart);

        return response()->json(['success' => true, 'cart_count' => count($cart)]);
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Cart is empty');
        }

        $total_amount = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('orders.checkout', compact('cart', 'total_amount'));
    }

    public function place_order(Request $request)
    {
        $user = Auth::user();
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Cart is empty');
        }

        $validated = $request->validate([
            'delivery_type' => 'required|in:delivery,pickup',
            'delivery_address' => 'required_if:delivery_type,delivery|string|max:500',
            'delivery_city' => 'required_if:delivery_type,delivery|string|max:255',
            'delivery_province' => 'required_if:delivery_type,delivery|string|max:255',
            'delivery_postal_code' => 'nullable|string|max:10',
        ]);

        try {
            DB::transaction(function () use ($user, $cart, $validated) {
                $farm_owner_id = null;
                
                // Re-verify prices from database to prevent manipulation
                $productIds = collect($cart)->pluck('product_id');
                $products = Product::whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($cart as $key => $item) {
                    $product = $products->get($item['product_id']);
                    
                    if (!$product) {
                        throw new \Exception("Product '{$item['name']}' is no longer available.");
                    }

                    if ($product->quantity_available < $item['quantity']) {
                        throw new \Exception("Insufficient stock for '{$product->name}'. Available: {$product->quantity_available}");
                    }

                    if ($farm_owner_id === null) {
                        $farm_owner_id = $product->farm_owner_id;
                    }

                    if ($product->farm_owner_id !== $farm_owner_id) {
                        throw new \Exception('All products must be from the same farm owner');
                    }

                    // Use verified price from DB
                    $cart[$key]['verified_price'] = $product->price;
                }

                $subtotal = collect($cart)->sum(fn($item) => $item['verified_price'] * $item['quantity']);
                $tax = $subtotal * 0.12;
                $total_amount = $subtotal + ($validated['delivery_type'] === 'delivery' ? 100 : 0) + $tax;

                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'consumer_id' => $user->id,
                    'farm_owner_id' => $farm_owner_id,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $validated['delivery_type'] === 'delivery' ? 100 : 0,
                    'tax' => $tax,
                    'discount' => 0,
                    'total_amount' => $total_amount,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'delivery_type' => $validated['delivery_type'],
                    'delivery_address' => $validated['delivery_address'] ?? null,
                    'delivery_city' => $validated['delivery_city'] ?? null,
                    'delivery_province' => $validated['delivery_province'] ?? null,
                    'delivery_postal_code' => $validated['delivery_postal_code'] ?? null,
                    'item_count' => collect($cart)->sum(fn($item) => $item['quantity']),
                ]);

                foreach ($cart as $item) {
                    $product = $products->get($item['product_id']);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['verified_price'],
                        'total_price' => $item['verified_price'] * $item['quantity'],
                        'product_attributes' => json_encode(['name' => $product->name]),
                    ]);

                    // Decrement stock
                    $product->decrement('quantity_available', $item['quantity']);
                    $product->increment('quantity_sold', $item['quantity']);
                }

                Log::info('Order created', ['order_id' => $order->id, 'consumer_id' => $user->id, 'total' => $total_amount]);
            });

            session()->forget('cart');
            return redirect()->route('orders.index')->with('success', 'Order placed successfully');

        } catch (\Exception $e) {
            Log::error('Order creation failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    public function confirm_order(Order $order, Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'farm_owner' || $order->farm_owner_id !== $user->farmOwner->id) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Order cannot be confirmed');
        }

        $order->update(['status' => 'confirmed']);

        return redirect()->back()->with('success', 'Order confirmed');
    }
}
