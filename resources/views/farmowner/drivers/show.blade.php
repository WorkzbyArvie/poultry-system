@extends('farmowner.layouts.app')

@section('title', $driver->name)
@section('header', $driver->name)
@section('subheader', ucfirst($driver->vehicle_type) . ' • ' . ($driver->plate_number ?? 'No Plate'))

@section('header-actions')
<div class="flex gap-2">
    <a href="{{ route('drivers.edit', $driver) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
    @if($driver->status === 'available')
    <span class="px-4 py-2 bg-green-900 text-green-300 rounded-lg">✓ Available</span>
    @elseif($driver->status === 'on_delivery')
    <span class="px-4 py-2 bg-blue-900 text-blue-300 rounded-lg">🚚 On Delivery</span>
    @else
    <span class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg">{{ ucfirst($driver->status) }}</span>
    @endif
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Driver Info -->
    <div class="lg:col-span-1 space-y-4">
        <!-- Contact Card -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-4">Contact Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-400">Phone</p>
                    <p class="font-medium text-white">{{ $driver->phone }}</p>
                </div>
                @if($driver->email)
                <div>
                    <p class="text-xs text-gray-400">Email</p>
                    <p class="font-medium text-white">{{ $driver->email }}</p>
                </div>
                @endif
                @if($driver->address)
                <div>
                    <p class="text-xs text-gray-400">Address</p>
                    <p class="text-sm text-gray-300">{{ $driver->address }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Vehicle & License -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-4">Vehicle & License</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-400">Vehicle Type</p>
                    <span class="px-2 py-1 text-xs bg-blue-900 text-blue-300 rounded-full">{{ ucfirst($driver->vehicle_type) }}</span>
                </div>
                @if($driver->plate_number)
                <div>
                    <p class="text-xs text-gray-400">Plate Number</p>
                    <p class="font-mono font-medium text-white">{{ $driver->plate_number }}</p>
                </div>
                @endif
                @if($driver->license_number)
                <div>
                    <p class="text-xs text-gray-400">License #</p>
                    <p class="font-mono text-sm">{{ $driver->license_number }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-gray-400">License Expiry</p>
                    <p class="{{ $driver->license_expiry?->isPast() ? 'text-red-600 font-semibold' : '' }}">
                        {{ $driver->license_expiry?->format('M d, Y') ?? '-' }}
                        @if($driver->license_expiry?->isPast())
                        <span class="text-xs">EXPIRED</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-4">Performance</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $driver->total_deliveries }}</p>
                    <p class="text-xs text-gray-400">Total Deliveries</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-yellow-600">
                        <span class="text-yellow-500">★</span> {{ number_format($driver->average_rating ?? 0, 1) }}
                    </p>
                    <p class="text-xs text-gray-400">Rating</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Deliveries -->
    <div class="lg:col-span-2">
        <div class="bg-gray-800 border border-gray-700 rounded-lg">
            <div class="p-6 border-b border-gray-600">
                <h3 class="font-semibold text-lg">Recent Deliveries</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">Delivery #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-600">
                        @forelse($driver->deliveries()->latest()->take(10)->get() as $delivery)
                        <tr>
                            <td class="px-6 py-4 font-mono text-sm">
                                <a href="{{ route('deliveries.show', $delivery) }}" class="text-blue-600 hover:underline">
                                    {{ $delivery->delivery_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">{{ $delivery->customer_name }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $delivery->scheduled_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($delivery->status === 'delivered') bg-green-900 text-green-300
                                    @elseif($delivery->status === 'dispatched') bg-blue-900 text-blue-300
                                    @else bg-yellow-900 text-yellow-300 @endif">
                                    {{ ucfirst($delivery->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">No deliveries assigned yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
