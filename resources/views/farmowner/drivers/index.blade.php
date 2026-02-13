@extends('farmowner.layouts.app')

@section('title', 'Drivers')
@section('header', 'Driver Management')
@section('subheader', 'Manage delivery drivers and vehicles')

@section('header-actions')
<a href="{{ route('drivers.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">+ Add Driver</a>
@endsection

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-blue-600">
        <p class="text-gray-400 text-xs">Total Drivers</p>
        <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-green-600">
        <p class="text-gray-400 text-xs">Available</p>
        <p class="text-2xl font-bold text-green-600">{{ $stats['available'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-yellow-600">
        <p class="text-gray-400 text-xs">On Delivery</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['on_delivery'] ?? 0 }}</p>
    </div>
</div>

<!-- Table -->
<div class="bg-gray-800 border border-gray-700 rounded-lg">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Vehicle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Plate #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">License Expiry</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-600">
                @forelse($drivers as $driver)
                <tr class="hover:bg-gray-700">
                    <td class="px-6 py-4 font-medium text-white">{{ $driver->name }}</td>
                    <td class="px-6 py-4 text-gray-300">{{ $driver->phone }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs bg-blue-900 text-blue-300 rounded-full">{{ ucfirst($driver->vehicle_type) }}</span></td>
                    <td class="px-6 py-4 font-mono text-gray-300">{{ $driver->plate_number ?? '-' }}</td>
                    <td class="px-6 py-4 {{ $driver->license_expiry && $driver->license_expiry->isPast() ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                        {{ $driver->license_expiry?->format('M d, Y') ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-yellow-500">★</span> {{ number_format($driver->average_rating ?? 0, 1) }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($driver->status === 'available') bg-green-900 text-green-300
                            @elseif($driver->status === 'on_delivery') bg-blue-900 text-blue-300
                            @else bg-gray-700 text-gray-300 @endif">
                            {{ ucfirst(str_replace('_', ' ', $driver->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('drivers.show', $driver) }}" class="text-blue-400 hover:text-blue-300">View</a>
                            <a href="{{ route('drivers.edit', $driver) }}" class="text-green-400 hover:text-green-300">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">No drivers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($drivers->hasPages())
    <div class="p-6 border-t border-gray-600">{{ $drivers->links() }}</div>
    @endif
</div>
@endsection
