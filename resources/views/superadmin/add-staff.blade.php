<x-app-layout>
    <div class="flex h-screen bg-[#1a202c] font-sans text-gray-200">
        <main class="flex-1 p-8 overflow-y-auto">
            <div class="max-w-2xl mx-auto">
                <h2 class="text-2xl font-bold text-[#ed8936] mb-6 uppercase tracking-tight">Staff Registration Form</h2>
                
                <div class="bg-[#111827] border border-gray-700 p-8 rounded-2xl shadow-2xl">
                    <form action="#" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">First Name</label>
                                <input type="text" name="fname" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:border-[#ed8936] outline-none" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Last Name</label>
                                <input type="text" name="lname" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:border-[#ed8936] outline-none" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">ID Number</label>
                            <input type="text" name="id_no" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:border-[#ed8936] outline-none" placeholder="e.g. 2026-0001" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Assign Role</label>
                            <select name="role" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:border-[#ed8936] outline-none">
                                <option value="manager">Manager</option>
                                <option value="customer_assistance">Customer Assistance</option>
                                <option value="checker">Checker</option>
                                <option value="delivery_rider">Delivery Rider</option>
                            </select>
                        </div>

                        <div class="flex gap-4 mt-8">
                            <button type="submit" class="flex-1 bg-[#ed8936] hover:bg-[#f6ad55] text-white font-bold py-3 rounded-lg transition shadow-lg">
                                Register Staff
                            </button>
                            <a href="{{ route('superadmin.dashboard') }}" class="flex-1 bg-gray-700 hover:bg-gray-600 text-center py-3 rounded-lg transition font-bold">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>