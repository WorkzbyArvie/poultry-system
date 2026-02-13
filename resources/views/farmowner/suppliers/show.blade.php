@extends('farmowner.layouts.app')

@section('title', $supplier->company_name)
@section('header', $supplier->company_name)
@section('subheader', ucfirst($supplier->category) . ' Supplier')

@section('header-actions')
<div class="flex gap-2">
    <a href="{{ route('suppliers.edit', $supplier) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
    <span class="px-4 py-2 rounded-lg {{ $supplier->status === 'active' ? 'bg-green-900 text-green-300' : 'bg-gray-700 text-gray-300' }}">
        {{ ucfirst($supplier->status) }}
    </span>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Contact Info -->
    <div class="space-y-4">
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-4">Contact Information</h3>
            <div class="space-y-3">
                @if($supplier->contact_person)
                <div>
                    <p class="text-xs text-gray-400">Contact Person</p>
                    <p class="font-medium text-white">{{ $supplier->contact_person }}</p>
                    @if($supplier->contact_title)
                    <p class="text-sm text-gray-300">{{ $supplier->contact_title }}</p>
                    @endif
                </div>
                @endif
                @if($supplier->phone)
                <div>
                    <p class="text-xs text-gray-400">Phone</p>
                    <p class="font-medium text-white">{{ $supplier->phone }}</p>
                </div>
                @endif
                @if($supplier->email)
                <div>
                    <p class="text-xs text-gray-400">Email</p>
                    <p class="font-medium text-white">{{ $supplier->email }}</p>
                </div>
                @endif
                @if($supplier->address)
                <div>
                    <p class="text-xs text-gray-400">Address</p>
                    <p class="text-sm text-gray-300">{{ $supplier->address }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-4">Payment Terms</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-400">Terms</p>
                    <span class="px-2 py-1 text-xs bg-blue-900 text-blue-300 rounded-full">
                        {{ strtoupper($supplier->payment_terms ?? 'COD') }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Credit Limit</p>
                    <p class="font-medium text-white">₱{{ number_format($supplier->credit_limit ?? 0, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Outstanding Balance</p>
                    <p class="font-medium {{ $supplier->outstanding_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                        ₱{{ number_format($supplier->outstanding_balance ?? 0, 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Purchases & Notes -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Notes -->
        @if($supplier->notes)
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-2">Notes</h3>
            <p class="text-gray-300">{{ $supplier->notes }}</p>
        </div>
        @endif

        <!-- Recent Purchases -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg">
            <div class="p-6 border-b border-gray-600 flex justify-between items-center">
                <h3 class="font-semibold text-lg">Recent Purchases</h3>
                <a href="{{ route('supplies.create') }}?supplier_id={{ $supplier->id }}" 
                   class="text-sm text-green-400 hover:text-green-300">+ Add Purchase</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-600">
                        @forelse($supplier->stockTransactions()->where('transaction_type', 'in')->latest()->take(10)->get() as $tx)
                        <tr>
                            <td class="px-6 py-4 text-gray-300">{{ $tx->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-medium text-white">{{ $tx->supplyItem?->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4">{{ $tx->quantity }} {{ $tx->supplyItem?->unit }}</td>
                            <td class="px-6 py-4 text-green-600 font-medium text-white">₱{{ number_format($tx->total_cost, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">No purchase records</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Items Supplied -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg">
            <div class="p-6 border-b border-gray-600">
                <h3 class="font-semibold text-lg">Items Supplied</h3>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap gap-2">
                    @forelse($supplier->supplyItems as $item)
                    <a href="{{ route('supplies.show', $item) }}" 
                       class="px-3 py-1 bg-gray-700 text-gray-700 rounded-full hover:bg-gray-200 text-sm">
                        {{ $item->name }}
                    </a>
                    @empty
                    <p class="text-gray-400">No items linked</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
