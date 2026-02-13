<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Owner Registration - Poultry System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 min-h-screen py-12">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-2xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-orange-500">Register Your Farm</h1>
                <p class="text-gray-400 mt-2">Join our agricultural marketplace</p>
            </div>

            @if ($errors->any())
            <div class="mb-6 p-4 bg-red-900/30 border border-red-700 rounded-lg">
                <p class="text-red-400 font-semibold mb-2">Registration Errors</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                    <li class="text-red-300 text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('farmowner.register.store') }}" class="space-y-6">
                @csrf

                <!-- Owner Information -->
                <div class="bg-gray-700 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-white mb-4">Owner Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full px-4 py-2 bg-gray-600 text-white border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-2 bg-gray-600 text-white border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   class="w-full px-4 py-2 bg-gray-600 text-white border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Password *</label>
                            <input type="password" name="password" required
                                   class="w-full px-4 py-2 bg-gray-600 text-white border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <p class="text-xs text-gray-400 mt-1">Minimum 8 characters</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Confirm Password *</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full px-4 py-2 bg-gray-600 text-white border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                </div>

                <!-- Farm Information -->
                <div class="bg-gray-700 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-white mb-4">Farm Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Farm Name *</label>
                            <input type="text" name="farm_name" value="{{ old('farm_name') }}" required
                                   class="w-full px-4 py-2 bg-gray-600 text-white border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Business Registration Number *</label>
                            <input type="text" name="business_registration_number" value="{{ old('business_registration_number') }}" required
                                   class="w-full px-4 py-2 bg-gray-600 text-white border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <p class="text-xs text-gray-400 mt-1">Your farm's official registration number</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Farm Address *</label>
                            <input type="text" name="farm_address" value="{{ old('farm_address') }}" required
                                   class="w-full px-4 py-2 bg-gray-600 text-white border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">City *</label>
                                <input type="text" name="city" value="{{ old('city') }}" required
                                       class="w-full px-4 py-2 bg-gray-600 text-white border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">Province *</label>
                                <input type="text" name="province" value="{{ old('province') }}" required
                                       class="w-full px-4 py-2 bg-gray-600 text-white border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">Postal Code</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                                       class="w-full px-4 py-2 bg-gray-600 text-white border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">Latitude</label>
                                <input type="number" step="0.000001" name="latitude" value="{{ old('latitude') }}"
                                       class="w-full px-4 py-2 bg-gray-600 text-white border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">Longitude</label>
                                <input type="number" step="0.000001" name="longitude" value="{{ old('longitude') }}"
                                       class="w-full px-4 py-2 bg-gray-600 text-white border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 rounded-lg transition">
                        Register Farm
                    </button>
                    <a href="{{ route('farmowner.login') }}" class="flex-1 bg-gray-600 hover:bg-gray-500 text-white font-semibold py-3 rounded-lg transition text-center">
                        Back to Login
                    </a>
                </div>
            </form>
        </div>

        <div class="mt-6 text-center text-gray-400">
            <p class="text-sm">Poultry System © 2026 | For Farm Owners Only</p>
        </div>
    </div>
</body>
</html>
