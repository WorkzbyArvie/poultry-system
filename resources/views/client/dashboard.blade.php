<x-app-layout>
    <div class="py-12 bg-[#1a202c] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-10 flex justify-between items-start">
                <div>
                    <h2 class="text-3xl font-black text-white uppercase tracking-tighter">Farm Management Console</h2>
                    <p class="text-gray-400">Welcome back, {{ Auth::user()->full_name }}. Here is your farm's status.</p>
                </div>

                <div class="flex items-center space-x-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center px-4 py-2 bg-red-600/10 border border-red-600/50 text-red-500 rounded-xl hover:bg-red-600 hover:text-white transition-all duration-200 font-black text-xs uppercase tracking-widest">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-[#111827] border border-gray-800 p-6 rounded-3xl shadow-xl flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs font-black uppercase tracking-widest mb-1">Subscription Plan</p>
                        <h3 class="text-white text-lg font-black uppercase">
                            {{ ($daysRemaining ?? 0) <= 7 ? '⚠️ Expiring Soon' : '✅ Active Pro' }}
                        </h3>
                    </div>
                    <div class="text-right">
                        <span class="text-4xl font-black {{ ($daysRemaining ?? 0) <= 7 ? 'text-red-500' : 'text-[#4fd1c5]' }}">
                            {{ $daysRemaining ?? 0 }}
                        </span>
                        <span class="block text-gray-500 text-[10px] font-bold uppercase">Days Left</span>
                    </div>
                </div>

                <div class="bg-[#111827] border border-gray-800 p-6 rounded-3xl shadow-xl">
                    <p class="text-gray-500 text-xs font-black uppercase tracking-widest mb-1">Current Stock</p>
                    <h3 class="text-white text-2xl font-black">0 <span class="text-sm text-gray-500 font-medium lowercase">units</span></h3>
                    <p class="text-[10px] text-gray-600 font-bold uppercase mt-2">No products listed yet</p>
                </div>

                <div class="bg-[#111827] border border-gray-800 p-6 rounded-3xl shadow-xl">
                    <p class="text-gray-500 text-xs font-black uppercase tracking-widest mb-1">Total Sales</p>
                    <h3 class="text-[#ed8936] text-2xl font-black">₱0.00</h3>
                    <p class="text-[10px] text-gray-600 font-bold uppercase mt-2">Last 30 days</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <a href="#" class="group bg-[#1a202c] border-2 border-dashed border-gray-800 hover:border-[#ed8936] p-10 rounded-3xl transition-all text-center">
                    <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">➕</div>
                    <h4 class="text-white font-black uppercase tracking-widest">Add New Product</h4>
                    <p class="text-gray-500 text-sm mt-2">List your chicken or eggs in the marketplace</p>
                </a>

                <a href="#" class="group bg-[#1a202c] border-2 border-dashed border-gray-800 hover:border-[#4fd1c5] p-10 rounded-3xl transition-all text-center">
                    <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">📋</div>
                    <h4 class="text-white font-black uppercase tracking-widest">View Orders</h4>
                    <p class="text-gray-500 text-sm mt-2">Manage pending buyer requests</p>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>