<x-guest-layout>
    <div class="min-h-screen bg-[#1a202c] py-12 px-4 text-gray-200">
        <div class="max-w-3xl mx-auto bg-[#111827] border border-gray-700 p-10 rounded-2xl shadow-2xl">
            <h2 class="text-[#ed8936] text-2xl font-bold uppercase tracking-widest mb-8 border-b border-gray-700 pb-4">
                Farm Owner Registration
            </h2>

            @if ($errors->any())
                <div class="bg-red-500/20 border border-red-500 text-red-100 p-4 rounded-xl mb-6">
                    <p class="font-bold mb-2">Registration failed due to the following:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('message'))
                <div class="bg-green-500/20 border border-green-500 text-green-100 p-4 rounded-xl mb-6">
                    {{ session('message') }}
                </div>
            @endif

            <form action="{{ route('client.request.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-400 mb-2">OWNER FULL NAME</label>
                        <input type="text" name="owner_name" value="{{ old('owner_name') }}" class="w-full bg-[#1a202c] border border-gray-600 rounded-lg p-3 text-white focus:border-[#ed8936] outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-400 mb-2">EMAIL ADDRESS</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-[#1a202c] border border-gray-600 rounded-lg p-3 text-white focus:border-[#ed8936] outline-none" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-400 mb-2">FARM NAME</label>
                    <input type="text" name="farm_name" value="{{ old('farm_name') }}" class="w-full bg-[#1a202c] border border-gray-600 rounded-lg p-3 text-white focus:border-[#ed8936] outline-none" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-white text-xs font-black uppercase tracking-widest block mb-2">Create Password</label>
                        <input type="password" name="password" class="w-full bg-[#111827] border-2 border-gray-800 text-white rounded-2xl py-3 px-6 focus:border-[#ed8936] transition-all" required>
                    </div>

                    <div>
                        <label class="text-white text-xs font-black uppercase tracking-widest block mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="w-full bg-[#111827] border-2 border-gray-800 text-white rounded-2xl py-3 px-6 focus:border-[#ed8936] transition-all" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-400 mb-2">FARM LOCATION</label>
                    <textarea name="farm_location" rows="2" class="w-full bg-[#1a202c] border border-gray-600 rounded-lg p-3 text-white focus:border-[#ed8936] outline-none" required>{{ old('farm_location') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-400 mb-2">VALID ID (UPLOAD)</label>
                        <input type="file" name="valid_id" class="text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-[#ed8936] file:text-white" required>
                        <p class="text-xs text-gray-500 mt-1">Max size: 2MB (JPG, PNG)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-400 mb-2">BUSINESS PERMIT (UPLOAD)</label>
                        <input type="file" name="business_permit" class="text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-[#ed8936] file:text-white" required>
                        <p class="text-xs text-gray-500 mt-1">Max size: 2MB (PDF, JPG, PNG)</p>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#ed8936] hover:bg-[#f6ad55] text-white font-black py-4 rounded-xl transition shadow-lg mt-8">
                    SUBMIT FOR ADMIN VERIFICATION
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>