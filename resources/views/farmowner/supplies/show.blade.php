@extends('farmowner.layouts.app')

@section('title', $supply->name)
@section('header', $supply->name)
@section('subheader', ucfirst($supply->category) . ' - ' . ($supply->supplier?->company_name ?? 'No Supplier'))

@section('header-actions')
<div class="flex gap-2">
    <a href="{{ route('supplies.edit', $supply) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
    <a href="{{ route('supplies.index') }}" class="px-4 py-2 bg-gray-200 text-gray-200 rounded-lg hover:bg-gray-300">Back</a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Item Details -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-4">Item Details</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-300">Status</dt>
                    <dd><span class="px-2 py-1 text-xs rounded-full 
                        @if($supply->status === 'in_stock') bg-green-900 text-green-300
                        @elseif($supply->status === 'low_stock') bg-yellow-900 text-yellow-300
                        @else bg-red-900 text-red-300 @endif">{{ ucfirst(str_replace('_', ' ', $supply->status)) }}</span></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-300">Quantity</dt>
                    <dd class="font-bold text-lg">{{ number_format($supply->quantity_on_hand, 2) }} {{ $supply->unit }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-300">Min Stock</dt>
                    <dd>{{ $supply->minimum_stock ?? 'Not set' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-300">Unit Cost</dt>
                    <dd>₱{{ number_format($supply->unit_cost, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-300">Total Value</dt>
                    <dd class="font-semibold text-green-600">₱{{ number_format($supply->inventory_value, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-300">Expiry</dt>
                    <dd class="{{ $supply->expiration_date && $supply->expiration_date->isPast() ? 'text-red-600' : '' }}">
                        {{ $supply->expiration_date?->format('M d, Y') ?? 'N/A' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-300">Location</dt>
                    <dd>{{ $supply->storage_location ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Stock In/Out Forms -->
    <div class="lg:col-span-2 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Stock In -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                <h3 class="font-semibold text-lg mb-4 text-green-600">📥 Stock In</h3>
                <form action="{{ route('supplies.stockIn', $supply) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Quantity *</label>
                        <input type="number" name="quantity" step="0.01" min="0.01" required
                            class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Notes</label>
                        <input type="text" name="notes" placeholder="e.g., From Supplier X"
                            class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Add Stock</button>
                </form>
            </div>

            <!-- Stock Out -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                <h3 class="font-semibold text-lg mb-4 text-red-600">📤 Stock Out</h3>
                <form action="{{ route('supplies.stockOut', $supply) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Quantity * (max: {{ $supply->quantity_on_hand }})</label>
                        <input type="number" name="quantity" step="0.01" min="0.01" max="{{ $supply->quantity_on_hand }}" required
                            class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Reason *</label>
                        <input type="text" name="reason" required placeholder="e.g., Used for feeding"
                            class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Deduct Stock</button>
                </form>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg">
            <div class="p-6 border-b border-gray-600">
                <h3 class="font-semibold text-lg">Stock History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Qty</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Balance</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-600">
                        @forelse($supply->stockTransactions as $txn)
                        <tr>
                            <td class="px-4 py-2">{{ $txn->transaction_date->format('M d, Y') }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $txn->transaction_type === 'stock_in' ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300' }}">
                                    {{ $txn->transaction_type === 'stock_in' ? 'IN' : 'OUT' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 font-medium {{ $txn->transaction_type === 'stock_in' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $txn->transaction_type === 'stock_in' ? '+' : '-' }}{{ number_format($txn->quantity, 2) }}
                            </td>
                            <td class="px-4 py-2">{{ number_format($txn->quantity_after, 2) }}</td>
                            <td class="px-4 py-2 text-gray-300">{{ $txn->reason ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400">No transactions yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
