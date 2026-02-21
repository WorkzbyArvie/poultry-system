<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-[#1a202c] p-6 text-center">
        <div class="w-full max-w-md p-10 bg-[#111827] border border-[#4fd1c5]/30 rounded-3xl shadow-[0_0_50px_rgba(79,209,197,0.1)]">
            
            <div class="w-20 h-20 bg-[#4fd1c5]/10 rounded-full flex items-center justify-center mx-auto mb-8 animate-pulse">
                <svg class="w-10 h-10 text-[#4fd1c5]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-black text-white uppercase tracking-tighter mb-4">Payment Received!</h1>
            <p class="text-gray-400 mb-6 leading-relaxed">
                Your subscription has been activated. You now have full access to the Poultry Management tools and the marketplace.
            </p>

            @if($subscription)
            <div class="bg-gray-800/50 rounded-xl p-4 mb-6 text-left space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-400 text-sm">Plan</span>
                    <span class="text-white font-semibold">{{ ucfirst($subscription->plan_type) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400 text-sm">Cost</span>
                    <span class="text-white font-semibold">₱{{ number_format($subscription->monthly_cost, 2) }}/mo</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400 text-sm">Products</span>
                    <span class="text-white font-semibold">{{ $subscription->product_limit ?? '∞ Unlimited' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400 text-sm">Valid Until</span>
                    <span class="text-white font-semibold">{{ $subscription->ends_at?->format('M d, Y') }}</span>
                </div>
            </div>
            @endif

            <div class="space-y-4">
                <a href="{{ route('dashboard') }}" class="block w-full bg-[#4fd1c5] hover:bg-[#38b2ac] text-[#111827] font-black py-4 rounded-2xl transition duration-300 uppercase tracking-widest">
                    Go to Dashboard
                </a>
                
                <p class="text-xs text-gray-500 uppercase font-bold tracking-widest">
                    Transaction processed via PayMongo
                </p>
            </div>
        </div>

        <p class="mt-8 text-gray-600 text-sm">
            Need help? Contact <span class="text-gray-400">support@poultrysystem.com</span>
        </p>
    </div>
</x-guest-layout>