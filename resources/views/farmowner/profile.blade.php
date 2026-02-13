<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Farm Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white shadow-lg border-r border-gray-700">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-orange-500">Farm Portal</h1>
                <p class="text-gray-400 text-sm mt-1">{{ Auth::user()->farmOwner?->farm_name ?? 'Farm' }}</p>
            </div>
            
            <nav class="p-4 space-y-2">
                <a href="{{ route('farmowner.dashboard') }}" class="block px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">Dashboard</a>
                <a href="{{ route('products.index') }}" class="block px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">Products</a>
                <a href="{{ route('orders.index') }}" class="block px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">Orders</a>
                <a href="{{ route('farmowner.subscriptions') }}" class="block px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">Subscription</a>
                <a href="{{ route('farmowner.profile') }}" class="block px-4 py-3 bg-orange-600 text-white rounded-lg">Profile</a>
                <hr class="my-4 border-gray-700">
                <form method="POST" action="{{ route('farmowner.logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 text-left text-gray-300 hover:bg-red-600 rounded-lg">Logout</button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <header class="bg-gray-800 border-b border-gray-700">
                <div class="px-8 py-4">
                    <h2 class="text-2xl font-bold text-white">Profile & Settings</h2>
                    <p class="text-gray-400 text-sm">Manage your farm information</p>
                </div>
            </header>

            <div class="p-8">
                @if(session('success'))
                <div class="mb-6 p-4 bg-green-900/30 border border-green-200 rounded-lg text-green-400">
                    {{ session('success') }}
                </div>
                @endif

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
                                       class="w-full px-4 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-1">City</label>
                                    <input type="text" name="city" value="{{ Auth::user()->farmOwner->city }}"
                                           class="w-full px-4 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-1">Province</label>
                                    <input type="text" name="province" value="{{ Auth::user()->farmOwner->province }}"
                                           class="w-full px-4 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-1">Postal Code</label>
                                    <input type="text" name="postal_code" value="{{ Auth::user()->farmOwner->postal_code }}"
                                           class="w-full px-4 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-1">Latitude</label>
                                    <input type="number" step="0.000001" name="latitude" value="{{ Auth::user()->farmOwner->latitude }}"
                                           class="w-full px-4 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-1">Longitude</label>
                                    <input type="number" step="0.000001" name="longitude" value="{{ Auth::user()->farmOwner->longitude }}"
                                           class="w-full px-4 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
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
            </div>
        </main>
    </div>
</body>
</html>
