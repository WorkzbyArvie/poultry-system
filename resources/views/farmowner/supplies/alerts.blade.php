@extends('farmowner.layouts.app')

@section('title', 'Supply Alerts')
@section('header', 'Supply Alerts')
@section('subheader', 'Low stock and expiring items that need attention')

@section('header-actions')
<a href="{{ route('supplies.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500">← Back to Supplies</a>
@endsection

@section('content')
<!-- Summary -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-yellow-600">
        <p class="text-gray-400 text-xs">Low Stock Items</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $lowStock->count() }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-orange-600">
        <p class="text-gray-400 text-xs">Expiring Within 30 Days</p>
        <p class="text-2xl font-bold text-orange-600">{{ $expiring->count() }}</p>
    </div>
</div>

<!-- Low Stock Items -->
<div class="bg-gray-800 border border-gray-700 rounded-lg mb-6">
    <div class="p-4 border-b border-gray-700">
        <h3 class="text-lg font-semibold text-yellow-400">⚠️ Low Stock Items</h3>
        <p class="text-sm text-gray-400">Items at or below their reorder point</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Current Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Reorder Point</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-600">
                @forelse($lowStock as $item)
                <tr class="hover:bg-gray-700">
                    <td class="px-6 py-4 font-medium text-white">{{ $item->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded-full">{{ ucfirst($item->category) }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-300">{{ $item->supplier?->company_name ?? '-' }}</td>
                    <td class="px-6 py-4 font-semibold text-red-400">
                        {{ number_format($item->quantity_on_hand, 2) }} {{ $item->unit ?? '' }}
                    </td>
                    <td class="px-6 py-4 text-gray-300">
                        {{ number_format($item->reorder_point ?? $item->minimum_stock ?? 0, 2) }} {{ $item->unit ?? '' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($item->quantity_on_hand <= 0) bg-red-900 text-red-300
                            @else bg-yellow-900 text-yellow-300 @endif">
                            {{ $item->quantity_on_hand <= 0 ? 'Out of Stock' : 'Low Stock' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('supplies.show', $item) }}" class="text-blue-400 hover:text-blue-300">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">No low stock items. Everything is well stocked! ✅</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Expiring Items -->
<div class="bg-gray-800 border border-gray-700 rounded-lg">
    <div class="p-4 border-b border-gray-700">
        <h3 class="text-lg font-semibold text-orange-400">🕐 Expiring Soon</h3>
        <p class="text-sm text-gray-400">Items expiring within the next 30 days</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Expiration Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Days Left</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-600">
                @forelse($expiring as $item)
                @php
                    $daysLeft = now()->diffInDays($item->expiration_date, false);
                @endphp
                <tr class="hover:bg-gray-700">
                    <td class="px-6 py-4 font-medium text-white">{{ $item->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded-full">{{ ucfirst($item->category) }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-300">
                        {{ number_format($item->quantity_on_hand, 2) }} {{ $item->unit ?? '' }}
                    </td>
                    <td class="px-6 py-4 font-semibold {{ $daysLeft <= 7 ? 'text-red-400' : 'text-orange-400' }}">
                        {{ $item->expiration_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($daysLeft <= 0) bg-red-900 text-red-300
                            @elseif($daysLeft <= 7) bg-red-900 text-red-300
                            @else bg-orange-900 text-orange-300 @endif">
                            @if($daysLeft <= 0) Expired
                            @else {{ $daysLeft }} days left @endif
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('supplies.show', $item) }}" class="text-blue-400 hover:text-blue-300">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">No items expiring soon. ✅</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
