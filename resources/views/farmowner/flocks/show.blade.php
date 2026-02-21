@extends('farmowner.layouts.app')

@section('title', $flock->batch_name)
@section('header', $flock->batch_name)
@section('subheader', $flock->breed_type . ' - ' . ucfirst($flock->flock_type))

@section('header-actions')
<div class="flex gap-2">
    <a href="{{ route('flocks.edit', $flock) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
    <a href="{{ route('flocks.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500">Back</a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Flock Info -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-4">Flock Details</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-300">Status</dt>
                    <dd><span class="px-2 py-1 text-xs rounded-full {{ $flock->status === 'active' ? 'bg-green-900 text-green-300' : 'bg-gray-700 text-gray-300' }}">{{ ucfirst($flock->status) }}</span></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-300">Current Count</dt>
                    <dd class="font-semibold">{{ number_format($flock->current_count) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-300">Initial Count</dt>
                    <dd>{{ number_format($flock->initial_count) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-300">Mortality</dt>
                    <dd class="text-red-600">{{ $flock->mortality_count }} ({{ number_format($flock->mortality_rate, 1) }}%)</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-300">Survival Rate</dt>
                    <dd class="text-green-600 font-semibold">{{ number_format($flock->survival_rate, 1) }}%</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-300">Age (weeks)</dt>
                    <dd>{{ $flock->age_weeks ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-300">Date Acquired</dt>
                    <dd>{{ $flock->date_acquired->format('M d, Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-300">Location</dt>
                    <dd>{{ $flock->housing_location ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Production Summary -->
        @if($recordStats)
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-4">Production Summary</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-300">Total Eggs</dt>
                    <dd class="font-semibold text-yellow-600">{{ number_format($recordStats->total_eggs ?? 0) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-300">Avg Weight (kg)</dt>
                    <dd>{{ number_format($recordStats->avg_weight ?? 0, 2) }}</dd>
                </div>
            </dl>
        </div>
        @endif
    </div>

    <!-- Daily Records & Add Record -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Add Daily Record -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-4">Log Daily Record</h3>
            <form action="{{ route('flocks.record', $flock) }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Date</label>
                        <input type="date" name="record_date" value="{{ date('Y-m-d') }}" required
                            class="w-full px-2 py-1.5 text-sm border border-gray-600 rounded focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Mortality</label>
                        <input type="number" name="mortality_today" value="0" min="0"
                            class="w-full px-2 py-1.5 text-sm border border-gray-600 rounded focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Eggs Collected</label>
                        <input type="number" name="eggs_collected" value="0" min="0"
                            class="w-full px-2 py-1.5 text-sm border border-gray-600 rounded focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Eggs Broken</label>
                        <input type="number" name="eggs_broken" value="0" min="0"
                            class="w-full px-2 py-1.5 text-sm border border-gray-600 rounded focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Feed (kg)</label>
                        <input type="number" name="feed_consumed_kg" step="0.01" min="0"
                            class="w-full px-2 py-1.5 text-sm border border-gray-600 rounded focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Water (L)</label>
                        <input type="number" name="water_consumed_liters" step="0.01" min="0"
                            class="w-full px-2 py-1.5 text-sm border border-gray-600 rounded focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Health Status</label>
                        <select name="health_status" required
                            class="w-full px-2 py-1.5 text-sm border border-gray-600 rounded focus:ring-2 focus:ring-green-500">
                            <option value="excellent">Excellent</option>
                            <option value="good" selected>Good</option>
                            <option value="fair">Fair</option>
                            <option value="poor">Poor</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                            Save Record
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Recent Records -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg">
            <div class="p-6 border-b border-gray-600">
                <h3 class="font-semibold text-lg">Recent Records</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Mortality</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Eggs</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Feed (kg)</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Health</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-600">
                        @forelse($flock->records as $record)
                        <tr>
                            <td class="px-4 py-2">{{ $record->record_date->format('M d') }}</td>
                            <td class="px-4 py-2 {{ $record->mortality_today > 0 ? 'text-red-600' : '' }}">{{ $record->mortality_today ?? 0 }}</td>
                            <td class="px-4 py-2 text-yellow-600">{{ $record->eggs_collected ?? 0 }}</td>
                            <td class="px-4 py-2">{{ $record->feed_consumed_kg ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-0.5 text-xs rounded-full 
                                    @if($record->health_status === 'excellent') bg-green-900 text-green-300
                                    @elseif($record->health_status === 'good') bg-blue-900 text-blue-300
                                    @elseif($record->health_status === 'fair') bg-yellow-900 text-yellow-300
                                    @else bg-red-900 text-red-300 @endif">
                                    {{ ucfirst($record->health_status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400">No records yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
