<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\FarmOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'farm_owner') {
            $farm_owner = $user->farmOwner;
            if (!$farm_owner) {
                return redirect()->route('farmowner.register');
            }
            $products = $farm_owner->products()->latest()->paginate(20);
            return view('farmowner.products.index', compact('products'));
        }

        $products = Product::with('farmOwner.user')
            ->where('status', 'active')
            ->latest('published_at')
            ->paginate(20);

        return view('products.browse', compact('products'));
    }

    public function create()
    {
        $this->authorize_farm_owner();

        $user = Auth::user();
        $farm_owner = $user->farmOwner;

        // Check for active subscription
        $active_sub = $farm_owner->subscriptions()->active()->first();
        if (!$active_sub) {
            return redirect()->route('farmowner.subscriptions')
                ->with('error', 'You need an active subscription to add products. Please subscribe to a plan first.');
        }

        // Check product limit
        if ($active_sub->product_limit) {
            $current_count = $farm_owner->products()->count();
            if ($current_count >= $active_sub->product_limit) {
                return redirect()->route('products.index')
                    ->with('error', "You've reached the maximum of {$active_sub->product_limit} products for your " . ucfirst($active_sub->plan_type) . " plan. Please upgrade your subscription to add more products.");
            }
        }

        return view('farmowner.products.create');
    }

    public function store(Request $request)
    {
        $this->authorize_farm_owner();

        $user = Auth::user();
        $farm_owner = $user->farmOwner;

        // Check for active subscription
        $active_sub = $farm_owner->subscriptions()->active()->first();
        if (!$active_sub) {
            return redirect()->route('farmowner.subscriptions')
                ->with('error', 'You need an active subscription to add products. Please subscribe to a plan first.');
        }

        // Check product limit
        if ($active_sub->product_limit) {
            $current_count = $farm_owner->products()->count();
            if ($current_count >= $active_sub->product_limit) {
                return redirect()->route('products.index')
                    ->with('error', "You've reached the maximum of {$active_sub->product_limit} products for your " . ucfirst($active_sub->plan_type) . " plan. Please upgrade your subscription to add more products.");
            }
        }

        $validated = $request->validate([
            'sku' => 'required|string|unique:products|max:100',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:live_stock,breeding,fighting_cock,eggs,feeds,equipment,other',
            'quantity_available' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:50',
            'minimum_order' => 'integer|min:1',
            'discount_percentage' => 'numeric|min:0|max:100',
            'image_url' => 'nullable|url',
        ]);

        $product = Product::create([
            'farm_owner_id' => $farm_owner->id,
            ...$validated,
            'status' => 'active',
            'published_at' => now(),
        ]);

        Log::info('Product created', ['product_id' => $product->id, 'farm_owner_id' => $farm_owner->id]);

        return redirect()->route('products.show', $product)->with('success', 'Product created successfully');
    }

    public function show(Product $product)
    {
        $user = Auth::user();
        if ($user && $user->role === 'farm_owner' && $product->farm_owner_id === $user->farmOwner?->id) {
            return view('farmowner.products.show', compact('product'));
        }
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize_farm_owner();
        
        if ($product->farm_owner_id !== Auth::user()->farmOwner->id) {
            abort(403);
        }

        return view('farmowner.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize_farm_owner();

        if ($product->farm_owner_id !== Auth::user()->farmOwner->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'quantity_available' => 'integer|min:0',
            'price' => 'numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'unit' => 'string|max:50',
            'minimum_order' => 'integer|min:1',
            'discount_percentage' => 'numeric|min:0|max:100',
        ]);

        $product->update($validated);

        return redirect()->back()->with('success', 'Product updated');
    }

    public function delete(Product $product)
    {
        $this->authorize_farm_owner();

        if ($product->farm_owner_id !== Auth::user()->farmOwner->id) {
            abort(403);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted');
    }

    public function update_stock(Request $request, Product $product)
    {
        $this->authorize_farm_owner();

        if ($product->farm_owner_id !== Auth::user()->farmOwner->id) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'action' => 'required|in:add,subtract',
        ]);

        $quantity = (int) $validated['quantity'];

        if ($validated['action'] === 'add') {
            $product->increment('quantity_available', $quantity);
            $message = "Added {$quantity} units to stock. New stock: {$product->fresh()->quantity_available}";
        } else {
            if ($product->quantity_available < $quantity) {
                return redirect()->back()->with('error', "Cannot subtract {$quantity} units. Current stock is only {$product->quantity_available}.");
            }
            $product->decrement('quantity_available', $quantity);
            $message = "Subtracted {$quantity} units from stock. New stock: {$product->fresh()->quantity_available}";
        }

        Log::info('Stock updated', [
            'product_id' => $product->id,
            'action' => $validated['action'],
            'quantity' => $quantity,
            'new_stock' => $product->fresh()->quantity_available,
            'farm_owner_id' => Auth::user()->farmOwner->id,
        ]);

        return redirect()->back()->with('success', $message);
    }

    private function authorize_farm_owner()
    {
        $user = Auth::user();
        if ($user->role !== 'farm_owner' || !$user->farmOwner) {
            abort(403);
        }
    }
}
