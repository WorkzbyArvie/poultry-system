@extends('farmowner.layouts.app')

@section('title', 'Flocks')
@section('header', 'Flock Management')
@section('subheader', 'Manage your poultry batches and monitor health')

@section('header-actions')
<a href="{{ route('flocks.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
    + Add Flock
</a>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-green-600">
        <p class="text-gray-400 text-xs">Active Flocks</p>
        <p class="text-2xl font-bold text-green-600">{{ $stats['total_flocks'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-blue-600">
        <p class="text-gray-400 text-xs">Total Birds</p>
        <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_birds'] ?? 0) }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-yellow-600">
        <p class="text-gray-400 text-xs">Layers</p>
        <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['layers'] ?? 0) }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-purple-600">
        <p class="text-gray-400 text-xs">Broilers</p>
        <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['broilers'] ?? 0) }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-red-600">
        <p class="text-gray-400 text-xs">Total Mortality</p>
        <p class="text-2xl font-bold text-red-600">{{ number_format($stats['total_mortality'] ?? 0) }}</p>
    </div>
</div>

<!-- Flocks Table -->
<div class="bg-gray-800 border border-gray-700 rounded-lg">
    <div class="p-6 border-b border-gray-600">
        <h3 class="text-lg font-semibold">All Flocks</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Batch Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Breed</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Count</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Mortality</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Age (weeks)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-600">
                @forelse($flocks as $flock)
                <tr class="hover:bg-gray-700">
                    <td class="px-6 py-4 font-medium text-white">{{ $flock->batch_name }}</td>
                    <td class="px-6 py-4 text-gray-300">{{ $flock->breed_type }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($flock->flock_type === 'layer') bg-yellow-900 text-yellow-300
                            @elseif($flock->flock_type === 'broiler') bg-blue-900 text-blue-300
                            @else bg-gray-700 text-gray-300 @endif">
                            {{ ucfirst($flock->flock_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-300">{{ number_format($flock->current_count) }}</td>
                    <td class="px-6 py-4 text-red-600">{{ $flock->mortality_count }}</td>
                    <td class="px-6 py-4 text-gray-300">{{ $flock->age_weeks ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($flock->status === 'active') bg-green-900 text-green-300
                            @elseif($flock->status === 'sold') bg-blue-900 text-blue-300
                            @else bg-gray-700 text-gray-300 @endif">
                            {{ ucfirst($flock->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('flocks.show', $flock) }}" class="text-blue-400 hover:text-blue-300">View</a>
                            <a href="{{ route('flocks.edit', $flock) }}" class="text-green-400 hover:text-green-300">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">No flocks found. Add your first flock!</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($flocks->hasPages())
    <div class="p-6 border-t border-gray-600">
        {{ $flocks->links() }}
    </div>
    @endif
</div>
@endsection
