@extends('farmowner.layouts.app')

@section('title', 'Profile')
@section('header', 'Profile & Settings')
@section('subheader', 'Manage your farm information')

@section('content')
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Profile Card -->
                    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-white mb-4">Account Information</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-300">Name</p>
                                <p class="font-semibold text-white">{{ Auth::user()->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-300">Email</p>
                                <p class="font-semibold text-white">{{ Auth::user()->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-300">Phone</p>
                                <p class="font-semibold text-white">{{ Auth::user()->phone ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-300">Member Since</p>
                                <p class="font-semibold text-white">{{ Auth::user()->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Farm Information -->
                    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 lg:col-span-2">
                        <h3 class="text-lg font-bold text-white mb-4">Farm Information</h3>
                        @if(Auth::user()->farmOwner)
                        <form method="PUT" action="{{ route('farmowner.update_profile') }}" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-1">Farm Name</label>
                                    <input type="text" value="{{ Auth::user()->farmOwner->farm_name }}" disabled
                                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-300">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-1">Business Registration</label>
                                    <input type="text" value="{{ Auth::user()->farmOwner->business_registration_number }}" disabled
                                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-300">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-1">Address</label>
                                <input type="text" name="farm_address" value="{{ Auth::user()->farmOwner->farm_address }}"
                                       class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-1">City</label>
                                    <input type="text" name="city" value="{{ Auth::user()->farmOwner->city }}"
                                           class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-1">Province</label>
                                    <input type="text" name="province" value="{{ Auth::user()->farmOwner->province }}"
                                           class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-1">Postal Code</label>
                                    <input type="text" name="postal_code" value="{{ Auth::user()->farmOwner->postal_code }}"
                                           class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-1">Latitude</label>
                                    <input type="number" step="0.000001" name="latitude" value="{{ Auth::user()->farmOwner->latitude }}"
                                           class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-1">Longitude</label>
                                    <input type="number" step="0.000001" name="longitude" value="{{ Auth::user()->farmOwner->longitude }}"
                                           class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                            </div>

                            <div class="flex space-x-4">
                                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg">
                                    Save Changes
                                </button>
                                <a href="{{ route('farmowner.dashboard') }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-500 text-white font-semibold rounded-lg">
                                    Cancel
                                </a>
                            </div>
                        </form>
                        @else
                        <p class="text-gray-400">No farm information registered yet.</p>
                        @endif
                    </div>
                </div>
@endsection
