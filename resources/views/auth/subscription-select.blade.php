<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-[#1a202c] p-6">
        <div class="mb-10 text-center">
            <span class="bg-[#ed8936]/20 text-[#ed8936] text-xs font-black px-4 py-1.5 rounded-full uppercase tracking-widest mb-4 inline-block">
                Limited Time: 50% Early Bird Discount
            </span>
            <h1 class="text-4xl font-black text-white uppercase tracking-tighter">Choose Your Plan</h1>
            <p class="text-gray-400 font-medium">Powering your poultry business with sustainable tools</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full max-w-6xl">
            
            <div class="bg-[#111827] border-2 border-gray-800 p-8 rounded-3xl shadow-2xl flex flex-col h-full">
                <h3 class="text-white text-xl font-black uppercase mb-2">Basic Monthly</h3>
                <div class="flex items-baseline gap-2 mb-6">
                    <span class="text-3xl font-black text-[#ed8936]">₱35</span>
                    <span class="text-gray-500 line-through text-sm">₱70</span>
                    <span class="text-gray-500 text-xs">/ 1 mo</span>
                </div>
                <ul class="text-gray-400 text-sm space-y-4 mb-8 flex-grow">
                    <li class="flex items-center gap-2">✅ Basic Stock Tracking</li>
                    <li class="flex items-center gap-2">✅ List 2 Products in Market</li>
                    <li class="flex items-center gap-2">✅ Weekly PDF Reports</li>
                </ul>
                <a href="{{ route('subscription.pay', ['plan' => '1_month']) }}" class="w-full bg-gray-800 hover:bg-[#ed8936] text-white font-bold py-4 rounded-2xl text-center transition-all duration-300">
                    Get Started
                </a>
            </div>

            <div class="bg-[#111827] border-2 border-[#ed8936] p-8 rounded-3xl shadow-2xl flex flex-col h-full transform scale-105 relative">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-[#ed8936] text-[#111827] text-[10px] font-black px-4 py-1 rounded-full uppercase">Best Value</div>
                <h3 class="text-white text-xl font-black uppercase mb-2">Annual Professional</h3>
                <div class="flex items-baseline gap-2 mb-6">
                    <span class="text-3xl font-black text-[#ed8936]">₱410</span>
                    <span class="text-gray-500 line-through text-sm">₱820</span>
                    <span class="text-gray-500 text-xs">/ 12 mo</span>
                </div>
                <ul class="text-gray-400 text-sm space-y-4 mb-8 flex-grow">
                    <li class="flex items-center gap-2 text-white">⭐ Unlimited Marketplace Listings</li>
                    <li class="flex items-center gap-2 text-white">⭐ Full Annual Analytics</li>
                    <li class="flex items-center gap-2 text-white">⭐ Priority 1-on-1 Support</li>
                    <li class="flex items-center gap-2 text-white">⭐ Predictive Stock Alerts</li>
                </ul>
                <a href="{{ route('subscription.pay', ['plan' => '12_month']) }}" class="w-full bg-[#ed8936] hover:bg-[#fbd38d] text-[#111827] font-black py-4 rounded-2xl text-center transition-all duration-300 shadow-[0_0_20px_rgba(237,137,54,0.3)]">
                    Subscribe Now
                </a>
            </div>

            <div class="bg-[#111827] border-2 border-gray-800 p-8 rounded-3xl shadow-2xl flex flex-col h-full">
                <h3 class="text-white text-xl font-black uppercase mb-2">Semi-Annual</h3>
                <div class="flex items-baseline gap-2 mb-6">
                    <span class="text-3xl font-black text-[#ed8936]">₱205</span>
                    <span class="text-gray-500 line-through text-sm">₱410</span>
                    <span class="text-gray-500 text-xs">/ 6 mo</span>
                </div>
                <ul class="text-gray-400 text-sm space-y-4 mb-8 flex-grow">
                    <li class="flex items-center gap-2">✅ List 10 Products in Market</li>
                    <li class="flex items-center gap-2">✅ Monthly Profit/Loss Reports</li>
                    <li class="flex items-center gap-2">✅ Full Stock History</li>
                </ul>
                <a href="{{ route('subscription.pay', ['plan' => '6_month']) }}" class="w-full bg-gray-800 hover:bg-[#ed8936] text-white font-bold py-4 rounded-2xl text-center transition-all duration-300">
                    Get Started
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>