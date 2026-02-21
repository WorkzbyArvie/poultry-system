@extends('farmowner.layouts.app')

@section('title', 'Income')
@section('header', 'Income Records')
@section('subheader', 'Track sales and revenue')

@section('header-actions')
<a href="{{ route('income.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">+ Add Income</a>
@endsection

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-green-600">
        <p class="text-gray-400 text-xs">This Month</p>
        <p class="text-2xl font-bold text-green-600">₱{{ number_format($stats['total_this_month'] ?? 0, 2) }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
        <p class="text-gray-400 text-xs mb-2">By Source</p>
        <div class="flex flex-wrap gap-2">
            @forelse($stats['by_source'] ?? [] as $source => $amount)
            <span class="px-2 py-1 text-xs bg-blue-900 text-blue-300 rounded">{{ ucfirst(str_replace('_', ' ', $source)) }}: ₱{{ number_format($amount, 0) }}</span>
            @empty
            <span class="text-gray-400">No data</span>
            @endforelse
        </div>
    </div>
</div>

<!-- Filter -->
<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <select name="source" class="px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            <option value="">All Sources</option>
            @foreach(['egg_sales', 'chicken_sales', 'manure_sales', 'chick_sales', 'feed_sales', 'other'] as $src)
            <option value="{{ $src }}" {{ request('source') === $src ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $src)) }}</option>
            @endforeach
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Income #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Source</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-600">
                @forelse($incomes as $income)
                <tr class="hover:bg-gray-700">
                    <td class="px-6 py-4 font-mono text-sm">{{ $income->income_number }}</td>
                    <td class="px-6 py-4 font-medium text-white">{{ Str::limit($income->description, 30) }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs bg-green-900 text-green-300 rounded-full">{{ ucfirst(str_replace('_', ' ', $income->source)) }}</span></td>
                    <td class="px-6 py-4 text-gray-300">{{ $income->income_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-gray-300">{{ ucfirst($income->payment_method ?? 'N/A') }}</td>
                    <td class="px-6 py-4 font-semibold text-green-600">₱{{ number_format($income->amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('income.show', $income) }}" class="text-blue-400 hover:text-blue-300">View</a>
                            <a href="{{ route('income.edit', $income) }}" class="text-green-400 hover:text-green-300">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">No income records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($incomes->hasPages())
    <div class="p-6 border-t border-gray-600">{{ $incomes->links() }}</div>
    @endif
</div>
@endsection
