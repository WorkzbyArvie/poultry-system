@extends('farmowner.layouts.app')

@section('title', 'Supplies')
@section('header', 'Supply Inventory')
@section('subheader', 'Manage feeds, vitamins, medications, and equipment')

@section('header-actions')
<div class="flex gap-2">
    <a href="{{ route('supplies.alerts') }}" class="px-4 py-2 bg-yellow-900/300 text-white rounded-lg hover:bg-yellow-600">⚠️ Alerts</a>
    <a href="{{ route('supplies.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">+ Add Item</a>
</div>
@endsection

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-blue-600">
        <p class="text-gray-400 text-xs">Total Items</p>
        <p class="text-2xl font-bold text-blue-600">{{ $stats['total_items'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-yellow-600">
        <p class="text-gray-400 text-xs">Low Stock</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['low_stock'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-red-600">
        <p class="text-gray-400 text-xs">Out of Stock</p>
        <p class="text-2xl font-bold text-red-600">{{ $stats['out_of_stock'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-orange-600">
        <p class="text-gray-400 text-xs">Expiring Soon</p>
        <p class="text-2xl font-bold text-orange-600">{{ $stats['expiring_soon'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-green-600">
        <p class="text-gray-400 text-xs">Total Value</p>
        <p class="text-2xl font-bold text-green-600">₱{{ number_format($stats['total_value'] ?? 0, 2) }}</p>
    </div>
</div>

<!-- Filter -->
<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <select name="category" class="px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            <option value="">All Categories</option>
            @foreach(['feeds', 'vitamins', 'vaccines', 'medications', 'equipment', 'supplements', 'cleaning', 'packaging', 'other'] as $cat)
            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
            @endforeach
        </select>
        <select name="status" class="px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            <option value="">All Status</option>
            <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
            <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
            <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-200 text-gray-200 rounded-lg hover:bg-gray-300">Filter</button>
        <a href="{{ route('supplies.index') }}" class="px-4 py-2 text-gray-600 hover:text-white">Reset</a>
    </form>
</div>

<!-- Table -->
<div class="bg-gray-800 border border-gray-700 rounded-lg">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Unit Cost</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Expiry</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-600">
                @forelse($items as $item)
                <tr class="hover:bg-gray-700">
                    <td class="px-6 py-4 font-medium text-white">{{ $item->name }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded-full">{{ ucfirst($item->category) }}</span></td>
                    <td class="px-6 py-4 text-gray-300">{{ $item->supplier?->company_name ?? '-' }}</td>
                    <td class="px-6 py-4 font-semibold {{ $item->quantity_on_hand <= ($item->minimum_stock ?? 0) ? 'text-red-600' : 'text-gray-900' }}">
                        {{ number_format($item->quantity_on_hand, 2) }} {{ $item->unit }}
                    </td>
                    <td class="px-6 py-4 text-gray-300">₱{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="px-6 py-4 {{ $item->expiration_date && $item->expiration_date->diffInDays(now()) <= 30 ? 'text-orange-600' : 'text-gray-600' }}">
                        {{ $item->expiration_date?->format('M d, Y') ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($item->status === 'in_stock') bg-green-900 text-green-300
                            @elseif($item->status === 'low_stock') bg-yellow-900 text-yellow-300
                            @else bg-red-900 text-red-300 @endif">
                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('supplies.show', $item) }}" class="text-blue-400 hover:text-blue-300">View</a>
                            <a href="{{ route('supplies.edit', $item) }}" class="text-green-400 hover:text-green-300">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">No supply items found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="p-6 border-t border-gray-600">{{ $items->links() }}</div>
    @endif
</div>
@endsection
