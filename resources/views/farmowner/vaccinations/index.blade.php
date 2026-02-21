@extends('farmowner.layouts.app')

@section('title', 'Vaccinations')
@section('header', 'Vaccination & Medication')
@section('subheader', 'Track vaccinations and health treatments')

@section('header-actions')
<a href="{{ route('vaccinations.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
    + Add Vaccination
</a>
@endsection

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-blue-600">
        <p class="text-gray-400 text-xs">Total Records</p>
        <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-yellow-600">
        <p class="text-gray-400 text-xs">Upcoming (14 days)</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['upcoming'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-red-600">
        <p class="text-gray-400 text-xs">Overdue</p>
        <p class="text-2xl font-bold text-red-600">{{ $stats['overdue'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-green-600">
        <p class="text-gray-400 text-xs">Total Cost</p>
        <p class="text-2xl font-bold text-green-600">₱{{ number_format($stats['total_cost'] ?? 0, 2) }}</p>
    </div>
</div>

@if($stats['overdue'] > 0)
<div class="mb-6 p-4 bg-red-900/30 border border-red-700 rounded-lg flex items-center justify-between">
    <p class="text-red-400">⚠️ You have {{ $stats['overdue'] }} overdue vaccinations!</p>
    <a href="{{ route('vaccinations.upcoming') }}" class="text-red-400 hover:text-red-300 font-medium text-white">View All →</a>
</div>
@endif

<!-- Filter -->
<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <select name="type" class="px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            <option value="">All Types</option>
            <option value="vaccine" {{ request('type') === 'vaccine' ? 'selected' : '' }}>Vaccine</option>
            <option value="medication" {{ request('type') === 'medication' ? 'selected' : '' }}>Medication</option>
            <option value="supplement" {{ request('type') === 'supplement' ? 'selected' : '' }}>Supplement</option>
            <option value="dewormer" {{ request('type') === 'dewormer' ? 'selected' : '' }}>Dewormer</option>
        </select>
        <select name="status" class="px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            <option value="">All Status</option>
            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="missed" {{ request('status') === 'missed' ? 'selected' : '' }}>Missed</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500">Filter</button>
        <a href="{{ route('vaccinations.index') }}" class="px-4 py-2 text-gray-600 hover:text-white">Reset</a>
    </form>
</div>

<!-- Table -->
<div class="bg-gray-800 border border-gray-700 rounded-lg">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Flock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Next Due</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Cost</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-600">
                @forelse($vaccinations as $vax)
                <tr class="hover:bg-gray-700">
                    <td class="px-6 py-4 text-white">{{ $vax->flock?->batch_name ?? 'All Flocks' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($vax->type === 'vaccine') bg-blue-900 text-blue-300
                            @elseif($vax->type === 'medication') bg-purple-900 text-purple-300
                            @else bg-gray-700 text-gray-300 @endif">
                            {{ ucfirst($vax->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-medium text-white">{{ $vax->name }}</td>
                    <td class="px-6 py-4 text-gray-300">{{ $vax->date_administered->format('M d, Y') }}</td>
                    <td class="px-6 py-4 {{ $vax->next_due_date && $vax->next_due_date->isPast() ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                        {{ $vax->next_due_date?->format('M d, Y') ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-gray-300">₱{{ number_format($vax->cost ?? 0, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($vax->status === 'completed') bg-green-900 text-green-300
                            @elseif($vax->status === 'scheduled') bg-yellow-900 text-yellow-300
                            @else bg-red-900 text-red-300 @endif">
                            {{ ucfirst($vax->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('vaccinations.show', $vax) }}" class="text-blue-400 hover:text-blue-300">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">No vaccination records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($vaccinations->hasPages())
    <div class="p-6 border-t border-gray-600">{{ $vaccinations->links() }}</div>
    @endif
</div>
@endsection
