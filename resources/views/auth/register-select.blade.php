<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-[#1a202c] p-6">
        <h1 class="text-3xl font-black text-white mb-2 uppercase tracking-tighter text-center">Join the Poultry System</h1>
        <p class="text-gray-400 mb-10 font-medium text-center">Select your account type to get started</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-4xl">
            
            <a href="{{ route('client.register') }}" class="group bg-[#111827] border-2 border-transparent hover:border-[#ed8936] p-8 rounded-3xl shadow-2xl transition-all duration-300 transform hover:-translate-y-2 block">
                <div class="bg-[#ed8936]/10 w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-[#ed8936]" style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-[#ed8936] text-xl font-black uppercase mb-2">Farm Owner</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Register your poultry business. Requires Valid ID and Business Permit for verification.</p>
                <div class="mt-6 flex items-center text-[#ed8936] font-bold text-xs uppercase tracking-widest">
                    Start Application <span class="ml-2 group-hover:ml-4 transition-all">→</span>
                </div>
            </a>

            <a href="{{ route('consumer.register') }}" class="group bg-[#111827] border-2 border-transparent hover:border-[#4fd1c5] p-8 rounded-3xl shadow-2xl transition-all duration-300 transform hover:-translate-y-2 block">
                <div class="bg-[#4fd1c5]/10 w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-[#4fd1c5]" style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h3 class="text-[#4fd1c5] text-xl font-black uppercase mb-2">Consumer</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Create a personal account to shop for eggs and chicken directly from local farms.</p>
                <div class="mt-6 flex items-center text-[#4fd1c5] font-bold text-xs uppercase tracking-widest">
                    Register Now <span class="ml-2 group-hover:ml-4 transition-all">→</span>
                </div>
            </a>

        </div> 

        <p class="mt-12 text-gray-500 text-sm text-center">
    Already have an account? <a href="{{ route('login') }}" class="text-[#4fd1c5] font-bold hover:underline">Log in here</a>
</p>
    </div>
</x-guest-layout>