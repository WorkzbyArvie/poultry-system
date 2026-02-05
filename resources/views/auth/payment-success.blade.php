<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-[#1a202c] p-6 text-center">
        <div class="w-full max-w-md p-10 bg-[#111827] border border-[#4fd1c5]/30 rounded-3xl shadow-[0_0_50px_rgba(79,209,197,0.1)]">
            
            <div class="w-20 h-20 bg-[#4fd1c5]/10 rounded-full flex items-center justify-center mx-auto mb-8 animate-pulse">
                <svg class="w-10 h-10 text-[#4fd1c5]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-black text-white uppercase tracking-tighter mb-4">Payment Received!</h1>
            <p class="text-gray-400 mb-8 leading-relaxed">
                Your subscription has been activated. You now have full access to the Poultry Management tools and the marketplace.
            </p>

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