<x-app-layout>
    <div class="flex h-screen bg-[#1a202c] font-sans text-gray-200">
        <aside class="w-64 bg-[#111827] border-r border-gray-700 flex flex-col">
            <div class="p-6">
                <h2 class="text-[#ed8936] font-bold text-xl tracking-tighter uppercase">Poultry Admin</h2>
            </div>
            
            <nav class="flex-1 px-4 space-y-1">
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-4 py-3 hover:bg-gray-800 text-gray-400 rounded-lg transition">
                    <span>Overview</span>
                </a>
                <a href="{{ route('eggs.index') }}" class="flex items-center px-4 py-3 bg-[#ed8936] text-white rounded-lg font-semibold">
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
                </a>
            </nav>
        </aside>

        <main class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-[#111827] border-b border-gray-700 py-4 px-8 flex justify-between items-center">
                <h1 class="text-2xl font-semibold">Egg Monitoring Records</h1>
                <div class="flex items-center gap-4">
                    <span class="text-xs bg-gray-800 px-3 py-1 rounded-full text-[#4fd1c5]">System Active</span>
                </div>
            </header>

            <div class="p-8 overflow-y-auto">
                <div class="bg-gradient-to-r from-[#111827] to-[#1f2937] border border-gray-700 rounded-2xl shadow-2xl overflow-hidden">
                    <div class="p-6 border-b border-gray-700">
                        <h3 class="text-[#ed8936] font-bold text-sm uppercase tracking-widest">All Records ({{ $eggRecords->total() }})</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-900/50 border-b border-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase">Batch Source</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase">Good Trays</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase">Broken Eggs</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase">Size Category</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @forelse ($eggRecords as $record)
                                    <tr class="hover:bg-gray-900/30 transition">
                                        <td class="px-6 py-4 text-sm">{{ $record->date_collected?->format('M d, Y') ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-300">{{ $record->batch_source ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="font-semibold text-[#4fd1c5]">{{ $record->good_trays ?? 0 }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="text-red-400">{{ $record->broken_eggs ?? 0 }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-900/30 text-blue-400">
                                                {{ ucfirst($record->size_category ?? 'standard') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            No egg monitoring records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($eggRecords->hasPages())
                        <div class="px-6 py-4 border-t border-gray-700 flex justify-between items-center">
                            {{ $eggRecords->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
