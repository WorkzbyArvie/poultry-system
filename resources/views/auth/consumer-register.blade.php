<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-[#1a202c] p-6">
        <div class="w-full max-w-md p-8 bg-[#111827] border border-gray-700 rounded-3xl shadow-2xl">
            
            <h2 class="text-2xl font-black text-[#4fd1c5] mb-2 uppercase tracking-widest text-center">
                Shopper Registration
            </h2>
            <p class="text-gray-400 text-sm text-center mb-8 uppercase font-bold tracking-tighter">Create your consumer account</p>

            <form method="POST" action="{{ route('consumer.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-500 text-xs font-black uppercase mb-1 ml-1">Full Name</label>
                    <input type="text" name="full_name" placeholder="John Doe" class="w-full bg-[#1a202c] border-gray-700 text-white rounded-xl focus:ring-[#4fd1c5] focus:border-[#4fd1c5] transition" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-500 text-xs font-black uppercase mb-1 ml-1">Email Address</label>
                    <input type="email" name="email" placeholder="john@example.com" class="w-full bg-[#1a202c] border-gray-700 text-white rounded-xl focus:ring-[#4fd1c5] focus:border-[#4fd1c5] transition" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-500 text-xs font-black uppercase mb-1 ml-1">Contact Number</label>
                    <input type="text" name="phone_number" placeholder="09123456789" class="w-full bg-[#1a202c] border-gray-700 text-white rounded-xl focus:ring-[#4fd1c5] focus:border-[#4fd1c5] transition" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-500 text-xs font-black uppercase mb-1 ml-1">Delivery Address</label>
                    <textarea name="address" rows="2" placeholder="Street, Barangay, City" class="w-full bg-[#1a202c] border-gray-700 text-white rounded-xl focus:ring-[#4fd1c5] focus:border-[#4fd1c5] transition" required></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-500 text-xs font-black uppercase mb-1 ml-1">Password</label>
                    <input type="password" name="password" class="w-full bg-[#1a202c] border-gray-700 text-white rounded-xl focus:ring-[#4fd1c5] focus:border-[#4fd1c5] transition" required>
                </div>

                <div class="mb-8">
                    <label class="block text-gray-500 text-xs font-black uppercase mb-1 ml-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full bg-[#1a202c] border-gray-700 text-white rounded-xl focus:ring-[#4fd1c5] focus:border-[#4fd1c5] transition" required>
                </div>

                <button type="submit" class="w-full bg-[#4fd1c5] hover:bg-[#38b2ac] text-[#111827] font-black py-4 rounded-2xl transition duration-300 uppercase tracking-widest shadow-lg transform hover:scale-[1.02]">
                    Complete Registration
                </button>

                <p class="mt-6 text-center text-gray-500 text-xs">
                    By registering, you agree to our terms of service.
                </p>
            </form>
        </div>
    </div>
</x-guest-layout>