@extends('farmowner.layouts.app')

@section('title', 'Expenses')
@section('header', 'Expense Tracking')
@section('subheader', 'Monitor and manage farm expenses')

@section('header-actions')
<a href="{{ route('expenses.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">+ Add Expense</a>
@endsection

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-red-600">
        <p class="text-gray-400 text-xs">This Month</p>
        <p class="text-2xl font-bold text-red-600">₱{{ number_format($stats['total_this_month'] ?? 0, 2) }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-yellow-600">
        <p class="text-gray-400 text-xs">Unpaid</p>
        <p class="text-2xl font-bold text-yellow-600">₱{{ number_format($stats['unpaid'] ?? 0, 2) }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-blue-600">
        <p class="text-gray-400 text-xs">Top Category</p>
        <p class="text-lg font-bold text-blue-600">
            @if(!empty($stats['by_category']))
                {{ ucfirst($stats['by_category']->keys()->first() ?? 'N/A') }}
            @else
                N/A
            @endif
        </p>
    </div>
</div>

<!-- Filter -->
<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <select name="category" class="px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            <option value="">All Categories</option>
            @foreach(['feeds', 'medications', 'utilities', 'equipment', 'labor', 'maintenance', 'transportation', 'packaging', 'miscellaneous'] as $cat)
            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
            @endforeach
        </select>
        <select name="payment_status" class="px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            <option value="">All Status</option>
            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
            <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
        </select>
        <input type="month" name="month" value="{{ request('month') }}"
            class="px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500">Filter</button>
    </form>
</div>

<!-- Table -->
<div class="bg-gray-800 border border-gray-700 rounded-lg">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Expense #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-600">
                @forelse($expenses as $expense)
                <tr class="hover:bg-gray-700">
                    <td class="px-6 py-4 font-mono text-sm">{{ $expense->expense_number }}</td>
                    <td class="px-6 py-4 font-medium text-white">{{ Str::limit($expense->description, 30) }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded-full">{{ ucfirst($expense->category) }}</span></td>
                    <td class="px-6 py-4 text-gray-300">{{ $expense->supplier?->company_name ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-300">{{ $expense->expense_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 font-semibold text-red-600">₱{{ number_format($expense->total_amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($expense->payment_status === 'paid') bg-green-900 text-green-300
                            @elseif($expense->payment_status === 'partial') bg-yellow-900 text-yellow-300
                            @else bg-red-900 text-red-300 @endif">
                            {{ ucfirst($expense->payment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('expenses.show', $expense) }}" class="text-blue-400 hover:text-blue-300">View</a>
                            <a href="{{ route('expenses.edit', $expense) }}" class="text-green-400 hover:text-green-300">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">No expenses found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
    <div class="p-6 border-t border-gray-600">{{ $expenses->links() }}</div>
    @endif
</div>
@endsection
