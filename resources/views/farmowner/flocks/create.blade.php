@extends('farmowner.layouts.app')

@section('title', 'Add Flock')
@section('header', 'Add New Flock')
@section('subheader', 'Register a new poultry batch')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('flocks.store') }}" method="POST" class="bg-gray-800 border border-gray-700 rounded-lg p-6 space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Batch Name *</label>
                <input type="text" name="batch_name" value="{{ old('batch_name') }}" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="e.g., Batch-2026-001">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Breed Type *</label>
                <input type="text" name="breed_type" value="{{ old('breed_type') }}" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="e.g., Ross 308, Lohmann Brown">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Flock Type *</label>
                <select name="flock_type" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Select Type</option>
                    <option value="broiler" {{ old('flock_type') === 'broiler' ? 'selected' : '' }}>Broiler</option>
                    <option value="layer" {{ old('flock_type') === 'layer' ? 'selected' : '' }}>Layer</option>
                    <option value="breeder" {{ old('flock_type') === 'breeder' ? 'selected' : '' }}>Breeder</option>
                    <option value="native" {{ old('flock_type') === 'native' ? 'selected' : '' }}>Native</option>
                    <option value="fighting_cock" {{ old('flock_type') === 'fighting_cock' ? 'selected' : '' }}>Fighting Cock</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Initial Count *</label>
                <input type="number" name="initial_count" value="{{ old('initial_count') }}" required min="1"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Number of birds">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Date Acquired *</label>
                <input type="date" name="date_acquired" value="{{ old('date_acquired', date('Y-m-d')) }}" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Age (weeks)</label>
                <input type="number" name="age_weeks" value="{{ old('age_weeks') }}" min="0"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Age at acquisition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Source/Supplier</label>
                <input type="text" name="source" value="{{ old('source') }}"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Where acquired from">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Acquisition Cost (₱)</label>
                <input type="number" name="acquisition_cost" value="{{ old('acquisition_cost') }}" step="0.01" min="0"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Total cost">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1">Housing Location</label>
                <input type="text" name="housing_location" value="{{ old('housing_location') }}"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="e.g., House A, Cage 1-10">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1">Notes</label>
                <textarea name="notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Additional notes...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex gap-4 pt-4">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Create Flock
            </button>
            <a href="{{ route('flocks.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
