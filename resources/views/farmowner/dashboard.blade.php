<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Dashboard - Poultry System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white shadow-lg border-r border-gray-700">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-orange-500">Farm Portal</h1>
                <p class="text-gray-400 text-sm mt-1">{{ $farm_owner?->farm_name ?? 'Farm' }}</p>
            </div>
            
            <nav class="p-4 space-y-2">
                <a href="{{ route('farmowner.dashboard') }}" class="block px-4 py-3 bg-orange-600 text-white rounded-lg">Dashboard</a>
                <a href="{{ route('products.index') }}" class="block px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">Products</a>
                <a href="{{ route('orders.index') }}" class="block px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">Orders</a>
                <a href="{{ route('farmowner.subscriptions') }}" class="block px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">Subscription</a>
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
                    <h2 class="text-2xl font-bold text-white">Dashboard</h2>
                    <p class="text-gray-400 text-sm">Welcome back, {{ Auth::user()->name }}</p>
                </div>
            </header>

            <div class="p-8">
                @if(session('success'))
                <div class="mb-6 p-4 bg-green-900/30 border border-green-200 rounded-lg text-green-400">
                    {{ session('success') }}
                </div>
                @endif

                <!-- Status Alert -->
                @if($stats['permit_status'] === 'pending')
                <div class="mb-6 p-4 bg-yellow-900/30 border border-yellow-700 rounded-lg">
                    <p class="text-yellow-700 font-semibold">⏳ Awaiting Admin Verification</p>
                    <p class="text-yellow-600 text-sm">Your farm is pending admin approval. Once approved, you can start adding products and receiving orders.</p>
                </div>
                @elseif($stats['permit_status'] === 'rejected')
                <div class="mb-6 p-4 bg-red-900/30 border border-red-700 rounded-lg">
                    <p class="text-red-700 font-semibold">❌ Registration Rejected</p>
                    <p class="text-red-600 text-sm">Your farm registration was rejected. Please contact support for more information.</p>
                </div>
                @endif

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 border-l-4 border-green-600">
                        <p class="text-gray-300 text-sm mb-2">Total Products</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['total_products'] ?? 0 }}</p>
                    </div>
                    
                    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 border-l-4 border-blue-600">
                        <p class="text-gray-300 text-sm mb-2">Total Orders</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_orders'] ?? 0 }}</p>
                    </div>
                    
                    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 border-l-4 border-purple-600">
                        <p class="text-gray-300 text-sm mb-2">Subscription Status</p>
                        <p class="text-lg font-bold {{ $stats['active_subscription'] ? 'text-green-600' : 'text-gray-600' }}">
                            {{ $stats['active_subscription'] ? '✓ Active' : 'Inactive' }}
                        </p>
                    </div>
                    
                    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 border-l-4 border-orange-600">
                        <p class="text-gray-300 text-sm mb-2">Farm Status</p>
                        <p class="text-lg font-bold">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                @if($stats['permit_status'] === 'approved') bg-green-900 text-green-300
                                @elseif($stats['permit_status'] === 'pending') bg-yellow-900 text-yellow-300
                                @else bg-red-900 text-red-300
                                @endif">
                                {{ ucfirst($stats['permit_status'] ?? 'unknown') }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Recent Products & Orders Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Products -->
                    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-white">Recent Products</h3>
                            <a href="{{ route('products.create') }}" class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">+ Add Product</a>
                        </div>
                        
                        @if($products && $products->count() > 0)
                        <div class="divide-y">
                            @foreach($products as $product)
                            <div class="py-3 flex justify-between items-start hover:bg-gray-700 px-2 -mx-2 rounded">
                                <div>
                                    <p class="font-semibold text-white">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-300">{{ $product->category }}</p>
                                </div>
                                <p class="font-bold text-green-600">₱{{ number_format($product->price, 2) }}</p>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-400 text-center py-6">No products yet. Start adding your products!</p>
                        @endif
                    </div>

                    <!-- Recent Orders -->
                    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-white mb-4">Recent Orders</h3>
                        
                        @if($recent_orders && $recent_orders->count() > 0)
                        <div class="divide-y">
                            @foreach($recent_orders as $order)
                            <div class="py-3 hover:bg-gray-700 px-2 -mx-2 rounded">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-white">Order #{{ $order->id }}</p>
                                        <p class="text-sm text-gray-300">{{ $order->created_at?->format('M d, Y') }}</p>
                                    </div>
                                    <span class="px-2 py-1 rounded text-xs font-semibold
                                        @if($order->status === 'completed') bg-green-900 text-green-300
                                        @elseif($order->status === 'pending') bg-yellow-900 text-yellow-300
                                        @else bg-gray-700 text-gray-300
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <p class="text-sm font-bold text-green-600 mt-1">₱{{ number_format($order->total_amount ?? 0, 2) }}</p>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-400 text-center py-6">No orders yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
