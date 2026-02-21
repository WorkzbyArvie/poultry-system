@extends('farmowner.layouts.app')

@section('title', 'Deliveries')
@section('header', 'Delivery Management')
@section('subheader', 'Track and manage order deliveries')

@section('header-actions')
<div class="flex gap-2">
    <a href="{{ route('deliveries.schedule') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">📅 Schedule</a>
    <a href="{{ route('deliveries.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">+ New Delivery</a>
</div>
@endsection

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-yellow-600">
        <p class="text-gray-400 text-xs">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-blue-600">
        <p class="text-gray-400 text-xs">Dispatched</p>
        <p class="text-2xl font-bold text-blue-600">{{ $stats['dispatched'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-green-600">
        <p class="text-gray-400 text-xs">Delivered Today</p>
        <p class="text-2xl font-bold text-green-600">{{ $stats['delivered_today'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-red-600">
        <p class="text-gray-400 text-xs">COD Pending</p>
        <p class="text-2xl font-bold text-red-600">₱{{ number_format($stats['cod_pending'] ?? 0, 2) }}</p>
    </div>
</div>

<!-- Filter -->
<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <select name="status" class="px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
            <option value="dispatched" {{ request('status') === 'dispatched' ? 'selected' : '' }}>Dispatched</option>
            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
        </select>
        <input type="date" name="date" value="{{ request('date') }}"
            class="px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500">Filter</button>
    </form>
</div>

<!-- Table -->
<div class="bg-gray-800 border border-gray-700 rounded-lg">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Delivery #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Scheduled</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">COD</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-600">
                @forelse($deliveries as $delivery)
                <tr class="hover:bg-gray-700">
                    <td class="px-6 py-4 font-mono text-sm">{{ $delivery->delivery_number }}</td>
                    <td class="px-6 py-4 text-gray-300">{{ $delivery->order?->order_number ?? '-' }}</td>
                    <td class="px-6 py-4 font-medium text-white">{{ $delivery->customer_name }}</td>
                    <td class="px-6 py-4 text-gray-300 text-sm">{{ Str::limit($delivery->delivery_address, 25) }}</td>
                    <td class="px-6 py-4 text-gray-300">{{ $delivery->scheduled_date->format('M d') }}</td>
                    <td class="px-6 py-4">
                        @if($delivery->driver)
                        <span class="text-blue-600">{{ $delivery->driver->name }}</span>
                        @else
                        <span class="text-gray-400">Unassigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($delivery->cod_amount > 0)
                        <span class="{{ $delivery->cod_collected >= $delivery->cod_amount ? 'text-green-600' : 'text-yellow-600' }}">
                            ₱{{ number_format($delivery->cod_amount, 2) }}
                        </span>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($delivery->status === 'delivered') bg-green-900 text-green-300
                            @elseif($delivery->status === 'dispatched') bg-blue-900 text-blue-300
                            @elseif($delivery->status === 'assigned') bg-purple-900 text-purple-300
                            @elseif($delivery->status === 'failed') bg-red-900 text-red-300
                            @else bg-yellow-900 text-yellow-300 @endif">
                            {{ ucfirst($delivery->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('deliveries.show', $delivery) }}" class="text-blue-400 hover:text-blue-300">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-8 text-center text-gray-400">No deliveries found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($deliveries->hasPages())
    <div class="p-6 border-t border-gray-600">{{ $deliveries->links() }}</div>
    @endif
</div>
@endsection
