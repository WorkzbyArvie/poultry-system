@extends('farmowner.layouts.app')

@section('title', 'Edit Product')
@section('header', 'Edit Product')
@section('subheader', $product->sku)

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('products.update', $product) }}" method="POST" class="bg-gray-800 border border-gray-700 rounded-lg p-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Info -->
        <h3 class="font-semibold text-lg mb-4 pb-2 border-b border-gray-600 text-white">Product Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">SKU *</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-green-500 @error('sku') border-red-500 @enderror">
                @error('sku')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Category *</label>
                <select name="category" required
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="live_stock" {{ old('category', $product->category) === 'live_stock' ? 'selected' : '' }}>Live Stock</option>
                    <option value="breeding" {{ old('category', $product->category) === 'breeding' ? 'selected' : '' }}>Breeding</option>
                    <option value="fighting_cock" {{ old('category', $product->category) === 'fighting_cock' ? 'selected' : '' }}>Fighting Cock</option>
                    <option value="eggs" {{ old('category', $product->category) === 'eggs' ? 'selected' : '' }}>Eggs</option>
                    <option value="feeds" {{ old('category', $product->category) === 'feeds' ? 'selected' : '' }}>Feeds</option>
                    <option value="equipment" {{ old('category', $product->category) === 'equipment' ? 'selected' : '' }}>Equipment</option>
                    <option value="other" {{ old('category', $product->category) === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1">Product Name *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-green-500 @error('name') border-red-500 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                <textarea name="description" rows="3"
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-green-500">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        <!-- Pricing -->
        <h3 class="font-semibold text-lg mb-4 pb-2 border-b border-gray-600 text-white">Pricing & Inventory</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Selling Price (₱) *</label>
                <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Cost Price (₱)</label>
                <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" min="0"
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Quantity Available *</label>
                <input type="number" name="quantity_available" value="{{ old('quantity_available', $product->quantity_available) }}" min="0" required
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Unit *</label>
                <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" required
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Minimum Order</label>
                <input type="number" name="minimum_order" value="{{ old('minimum_order', $product->minimum_order) }}" min="1"
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Discount (%)</label>
                <input type="number" name="discount_percentage" value="{{ old('discount_percentage', $product->discount_percentage) }}" step="0.01" min="0" max="100"
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
        </div>

        <!-- Status -->
        <h3 class="font-semibold text-lg mb-4 pb-2 border-b border-gray-600 text-white">Status</h3>
        <div class="mb-6">
            <select name="status"
                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-green-500">
                <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Active (Visible to customers)</option>
                <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactive (Hidden)</option>
                <option value="out_of_stock" {{ old('status', $product->status) === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>
        </div>

        <!-- Image -->
        <h3 class="font-semibold text-lg mb-4 pb-2 border-b border-gray-600 text-white">Product Image</h3>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-300 mb-1">Image URL</label>
            <input type="url" name="image_url" value="{{ old('image_url', $product->image_url) }}"
                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-green-500">
            @if($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="mt-2 w-24 h-24 object-cover rounded">
            @endif
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Update Product</button>
            <a href="{{ route('products.show', $product) }}" class="px-6 py-2 bg-gray-600 text-gray-200 rounded-lg hover:bg-gray-7000">Cancel</a>
        </div>
    </form>
</div>
@endsection
