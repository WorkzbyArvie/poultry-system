@extends('farmowner.layouts.app')

@section('title', 'Orders')
@section('header', 'Orders')
@section('subheader', 'Manage incoming orders from customers')

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-l-blue-600">
        <p class="text-gray-400 text-xs">Total Orders</p>
        <p class="text-2xl font-bold text-blue-600">{{ $orders->total() }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-l-yellow-600">
        <p class="text-gray-400 text-xs">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $orders->where('status', 'pending')->count() }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-l-green-600">
        <p class="text-gray-400 text-xs">Confirmed</p>
        <p class="text-2xl font-bold text-green-600">{{ $orders->where('status', 'confirmed')->count() }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-l-purple-600">
        <p class="text-gray-400 text-xs">Completed</p>
        <p class="text-2xl font-bold text-purple-600">{{ $orders->where('status', 'completed')->count() }}</p>
    </div>
</div>

<!-- Orders Table -->
<div class="bg-gray-800 border border-gray-700 rounded-lg">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Delivery</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-600">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-700">
                    <td class="px-6 py-4 font-mono text-sm text-white">{{ $order->order_number }}</td>
                    <td class="px-6 py-4 text-gray-300">{{ $order->consumer->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-gray-300">{{ $order->item_count ?? 0 }}</td>
                    <td class="px-6 py-4 font-medium text-green-400">₱{{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($order->payment_status === 'paid') bg-green-900 text-green-300
                            @elseif($order->payment_status === 'unpaid') bg-red-900 text-red-300
                            @else bg-gray-700 text-gray-300
                            @endif">
                            {{ ucfirst($order->payment_status ?? 'unknown') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-900 text-blue-300">
                            {{ ucfirst($order->delivery_type ?? 'N/A') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($order->status === 'completed') bg-green-900 text-green-300
                            @elseif($order->status === 'confirmed') bg-blue-900 text-blue-300
                            @elseif($order->status === 'pending') bg-yellow-900 text-yellow-300
                            @elseif($order->status === 'cancelled') bg-red-900 text-red-300
                            @else bg-gray-700 text-gray-300
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('orders.show', $order) }}" class="text-blue-400 hover:text-blue-300 text-sm">View</a>
                            @if($order->status === 'pending')
                            <form method="POST" action="{{ route('orders.confirm', $order) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-400 hover:text-green-300 text-sm">Confirm</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-8 text-center text-gray-400">
                        No orders yet. Orders will appear here when customers place them.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="p-6 border-t border-gray-600">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
