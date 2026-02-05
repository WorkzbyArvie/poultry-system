<x-app-layout>
    <div class="flex h-screen bg-[#1a202c] font-sans text-gray-200">
        <aside class="w-64 bg-[#111827] border-r border-gray-700 flex flex-col">
            <div class="p-6">
                <h2 class="text-[#ed8936] font-bold text-xl tracking-tighter uppercase">Poultry Admin</h2>
            </div>
            
            <nav class="flex-1 px-4 space-y-1">
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-4 py-3 bg-[#ed8936] text-white rounded-lg font-semibold">
                    <span>Overview</span>
                </a>
                <a href="{{ route('eggs.index') }}" class="flex items-center px-4 py-3 hover:bg-gray-800 text-gray-400 rounded-lg transition">
                    <span>Egg Section</span>
                </a>
                <a href="{{ route('chickens.index') }}" class="flex items-center px-4 py-3 hover:bg-gray-800 text-gray-400 rounded-lg transition">
                    <span>Chicken Section</span>
                </a>
                <a href="{{ route('staff.create') }}" class="flex items-center px-4 py-3 hover:bg-gray-800 text-gray-400 rounded-lg transition">
                    <span>Add Staff</span>
                </a>
                <a href="{{ route('admin.verifications') }}" class="flex items-center px-4 py-3 hover:bg-gray-800 text-gray-400 rounded-lg transition">
    <span>Verification Requests</span>
    <span class="ml-auto bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">PENDING</span>
</a>
         
            </nav>
        </aside>

        <main class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-[#111827] border-b border-gray-700 py-4 px-8 flex justify-between items-center">
                <h1 class="text-2xl font-semibold">Welcome, <span class="text-[#ed8936]">{{ Auth::user()->full_name }}</span></h1>
                <div class="flex items-center gap-4">
                    <span class="text-xs bg-gray-800 px-3 py-1 rounded-full text-[#4fd1c5]">System Active</span>
                </div>
            </header>

            <div class="p-8 overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                    <a href="{{ route('eggs.index') }}" class="bg-[#2d3748] border-b-4 border-[#ed8936] p-6 rounded-xl shadow-lg hover:translate-y-[-4px] transition-all block text-left">
                        <span class="block text-sm text-gray-400 uppercase">Input Data</span>
                        <span class="text-lg font-bold">Update Stocks</span>
                    </a>
                </div>

                <div class="bg-gradient-to-r from-[#111827] to-[#1f2937] border border-gray-700 p-8 rounded-2xl shadow-2xl relative overflow-hidden">
                    <h3 class="text-[#ed8936] font-bold mb-6 text-sm uppercase tracking-widest">Strategic Decision Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                        <div class="bg-gray-900/50 p-4 rounded-lg">
                            <p class="text-gray-400 text-xs mb-1">Today's Harvest</p>
                            <p class="text-3xl font-bold text-[#4fd1c5]">0 <span class="text-lg font-normal">Trays Collected</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-auto pb-6 px-4">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full flex items-center justify-start gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-red-600/20 border border-transparent hover:border-red-600/50 rounded-xl transition-all duration-200 group">
            <svg class="w-5 h-5 text-gray-500 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="font-bold uppercase tracking-widest text-xs">Logout</span>
        </button>
    </form>
</div>
        </main>
    </div>
</x-app-layout>