@extends('farmowner.layouts.app')

@section('title', 'Add Vaccination')
@section('header', 'Add Vaccination/Medication')
@section('subheader', 'Record new vaccination or treatment')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('vaccinations.store') }}" method="POST" class="bg-gray-800 border border-gray-700 rounded-lg p-6 space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Flock (optional)</label>
                <select name="flock_id" class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">All Flocks / Farm-wide</option>
                    @foreach($flocks as $flock)
                    <option value="{{ $flock->id }}" {{ old('flock_id') == $flock->id ? 'selected' : '' }}>
                        {{ $flock->batch_name }} ({{ $flock->current_count }} birds)
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Type *</label>
                <select name="type" required class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">Select Type</option>
                    <option value="vaccine" {{ old('type') === 'vaccine' ? 'selected' : '' }}>Vaccine</option>
                    <option value="medication" {{ old('type') === 'medication' ? 'selected' : '' }}>Medication</option>
                    <option value="supplement" {{ old('type') === 'supplement' ? 'selected' : '' }}>Supplement</option>
                    <option value="dewormer" {{ old('type') === 'dewormer' ? 'selected' : '' }}>Dewormer</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500"
                    placeholder="e.g., Newcastle Disease Vaccine">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Brand</label>
                <input type="text" name="brand" value="{{ old('brand') }}"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Dosage *</label>
                <div class="flex gap-2">
                    <input type="number" name="dosage" value="{{ old('dosage') }}" step="0.01" required
                        class="flex-1 px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    <input type="text" name="dosage_unit" value="{{ old('dosage_unit', 'ml') }}" required
                        class="w-20 px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500"
                        placeholder="unit">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Method *</label>
                <select name="administration_method" required class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="drinking_water" {{ old('administration_method') === 'drinking_water' ? 'selected' : '' }}>Drinking Water</option>
                    <option value="injection" {{ old('administration_method') === 'injection' ? 'selected' : '' }}>Injection</option>
                    <option value="spray" {{ old('administration_method') === 'spray' ? 'selected' : '' }}>Spray</option>
                    <option value="eye_drop" {{ old('administration_method') === 'eye_drop' ? 'selected' : '' }}>Eye Drop</option>
                    <option value="feed_mix" {{ old('administration_method') === 'feed_mix' ? 'selected' : '' }}>Feed Mix</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Date Administered *</label>
                <input type="date" name="date_administered" value="{{ old('date_administered', date('Y-m-d')) }}" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Next Due Date</label>
                <input type="date" name="next_due_date" value="{{ old('next_due_date') }}"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Birds Treated</label>
                <input type="number" name="birds_treated" value="{{ old('birds_treated') }}" min="0"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Cost (₱)</label>
                <input type="number" name="cost" value="{{ old('cost') }}" step="0.01" min="0"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex gap-4 pt-4">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Save Record</button>
            <a href="{{ route('vaccinations.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500">Cancel</a>
        </div>
    </form>
</div>
@endsection
