@extends('farmowner.layouts.app')

@section('title', 'Edit Driver')
@section('header', 'Edit Driver')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('drivers.update', $driver) }}" method="POST" class="bg-gray-800 border border-gray-700 rounded-lg p-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Info -->
        <h3 class="font-semibold text-lg mb-4 pb-2 border-b border-gray-600">Basic Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $driver->name) }}" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 @error('name') border-red-500 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Phone Number *</label>
                <input type="tel" name="phone" value="{{ old('phone', $driver->phone) }}" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 @error('phone') border-red-500 @enderror">
                @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $driver->email) }}"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                <select name="status"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="available" {{ old('status', $driver->status) === 'available' ? 'selected' : '' }}>Available</option>
                    <option value="on_delivery" {{ old('status', $driver->status) === 'on_delivery' ? 'selected' : '' }}>On Delivery</option>
                    <option value="off_duty" {{ old('status', $driver->status) === 'off_duty' ? 'selected' : '' }}>Off Duty</option>
                    <option value="inactive" {{ old('status', $driver->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <!-- Vehicle Info -->
        <h3 class="font-semibold text-lg mb-4 pb-2 border-b border-gray-600">Vehicle Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Vehicle Type *</label>
                <select name="vehicle_type" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="motorcycle" {{ old('vehicle_type', $driver->vehicle_type) === 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                    <option value="tricycle" {{ old('vehicle_type', $driver->vehicle_type) === 'tricycle' ? 'selected' : '' }}>Tricycle</option>
                    <option value="van" {{ old('vehicle_type', $driver->vehicle_type) === 'van' ? 'selected' : '' }}>Van</option>
                    <option value="truck" {{ old('vehicle_type', $driver->vehicle_type) === 'truck' ? 'selected' : '' }}>Truck</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Plate Number</label>
                <input type="text" name="plate_number" value="{{ old('plate_number', $driver->plate_number) }}"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
        </div>

        <!-- License Info -->
        <h3 class="font-semibold text-lg mb-4 pb-2 border-b border-gray-600">License Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">License Number</label>
                <input type="text" name="license_number" value="{{ old('license_number', $driver->license_number) }}"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">License Expiry *</label>
                <input type="date" name="license_expiry" value="{{ old('license_expiry', $driver->license_expiry?->format('Y-m-d')) }}" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
        </div>

        <!-- Address -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-300 mb-1">Address</label>
            <textarea name="address" rows="2"
                class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">{{ old('address', $driver->address) }}</textarea>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Update Driver</button>
            <a href="{{ route('drivers.show', $driver) }}" class="px-6 py-2 bg-gray-200 text-gray-200 rounded-lg hover:bg-gray-300">Cancel</a>
        </div>
    </form>
</div>
@endsection
