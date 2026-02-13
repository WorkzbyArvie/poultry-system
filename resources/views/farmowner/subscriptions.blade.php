<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscriptions - Farm Dashboard</title>
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
                <a href="{{ route('farmowner.subscriptions') }}" class="block px-4 py-3 bg-orange-600 text-white rounded-lg">Subscription</a>
                <a href="{{ route('farmowner.profile') }}" class="block px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">Profile</a>
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
                    <h2 class="text-2xl font-bold text-white">Subscription Plans</h2>
                    <p class="text-gray-400 text-sm">Manage your farm subscription</p>
                </div>
            </header>

            <div class="p-8">
                @if(session('success'))
                <div class="mb-6 p-4 bg-green-900/30 border border-green-200 rounded-lg text-green-400">
                    {{ session('success') }}
                </div>
                @endif

                <!-- Current Subscription Status -->
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-bold text-white mb-4">Your Current Subscription</h3>
                    
                    @if(Auth::user()->farmOwner && Auth::user()->farmOwner->subscriptions()->where('status', 'active')->exists())
                    @php
                    $active_sub = Auth::user()->farmOwner->subscriptions()->where('status', 'active')->first();
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-green-900/30 p-4 rounded-lg border border-green-700">
                            <p class="text-sm text-gray-400 mb-2">Plan Type</p>
                            <p class="text-2xl font-bold text-green-600">{{ ucfirst($active_sub->plan_type ?? 'Standard') }}</p>
                        </div>
                        
                        <div class="bg-blue-900/30 p-4 rounded-lg border border-blue-700">
                            <p class="text-sm text-gray-400 mb-2">Status</p>
                            <p class="px-3 py-1 rounded font-semibold bg-green-900 text-green-300 w-fit">✓ Active</p>
                        </div>

                        <div class="bg-purple-900/30 p-4 rounded-lg border border-purple-700">
                            <p class="text-sm text-gray-400 mb-2">Expires On</p>
                            <p class="font-bold text-purple-600">{{ $active_sub->expires_at?->format('M d, Y') }}</p>
                        </div>

                        <div class="bg-orange-900/30 p-4 rounded-lg border border-orange-700">
                            <p class="text-sm text-gray-400 mb-2">Days Remaining</p>
                            <p class="font-bold text-orange-600">{{ now()->diffInDays($active_sub->expires_at) }} days</p>
                        </div>
                    </div>
                    @else
                    <div class="bg-yellow-900/30 p-4 rounded-lg border border-yellow-200">
                        <p class="text-yellow-700 font-semibold">⚠️ No Active Subscription</p>
                        <p class="text-yellow-600 text-sm mt-1">Upgrade to a paid plan to unlock premium features and reach more customers.</p>
                    </div>
                    @endif
                </div>

                <!-- Available Plans -->
                <div>
                    <h3 class="text-lg font-bold text-white mb-4">Available Plans</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Starter Plan -->
                        <div class="bg-gray-800 border border-gray-700 rounded-lg border border-gray-600 overflow-hidden hover:shadow-lg transition">
                            <div class="bg-blue-600 text-white p-6">
                                <h4 class="text-xl font-bold">Starter</h4>
                                <p class="text-blue-100 text-sm">For new farms</p>
                            </div>
                            <div class="p-6">
                                <div class="mb-6">
                                    <p class="text-3xl font-bold text-white">₱500<span class="text-sm text-gray-300">/month</span></p>
                                </div>
                                <ul class="space-y-3 mb-6">
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Up to 50 products</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Basic analytics</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Email support</span>
                                    </li>
                                </ul>
                                <a href="{{ route('subscription.pay') }}" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded text-center">
                                    Subscribe Now
                                </a>
                            </div>
                        </div>

                        <!-- Professional Plan -->
                        <div class="bg-gray-800 border border-gray-700 rounded-lg border-2 border-green-600 overflow-hidden hover:shadow-lg transition">
                            <div class="bg-green-600 text-white p-6">
                                <h4 class="text-xl font-bold">Professional</h4>
                                <p class="text-green-100 text-sm">Most popular</p>
                            </div>
                            <div class="p-6">
                                <div class="mb-6">
                                    <p class="text-3xl font-bold text-white">₱1,200<span class="text-sm text-gray-300">/month</span></p>
                                </div>
                                <ul class="space-y-3 mb-6">
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Unlimited products</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Advanced analytics</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Priority support</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Marketing tools</span>
                                    </li>
                                </ul>
                                <a href="{{ route('subscription.pay') }}" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded text-center">
                                    Subscribe Now
                                </a>
                            </div>
                        </div>

                        <!-- Enterprise Plan -->
                        <div class="bg-gray-800 border border-gray-700 rounded-lg border border-gray-600 overflow-hidden hover:shadow-lg transition">
                            <div class="bg-purple-600 text-white p-6">
                                <h4 class="text-xl font-bold">Enterprise</h4>
                                <p class="text-purple-100 text-sm">For large operations</p>
                            </div>
                            <div class="p-6">
                                <div class="mb-6">
                                    <p class="text-3xl font-bold text-white">₱2,500<span class="text-sm text-gray-300">/month</span></p>
                                </div>
                                <ul class="space-y-3 mb-6">
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Unlimited everything</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Custom integrations</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">24/7 support</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Dedicated manager</span>
                                    </li>
                                </ul>
                                <a href="{{ route('subscription.pay') }}" class="w-full py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded text-center">
                                    Subscribe Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
