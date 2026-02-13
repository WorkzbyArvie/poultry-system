@extends('farmowner.layouts.app')

@section('title', 'Financial Report')
@section('header', 'Financial Report')
@section('subheader', $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y'))

@section('header-actions')
<a href="{{ route('reports.export', ['type' => 'financial']) }}?start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}" 
   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">📥 Export CSV</a>
@endsection

@section('content')
<!-- Date Filter -->
<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm text-gray-600 mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                class="px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                class="px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Apply</button>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 border-l-4 border-green-600">
        <p class="text-gray-300 text-sm">Total Income</p>
        <p class="text-3xl font-bold text-green-600">₱{{ number_format($totalIncome, 2) }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 border-l-4 border-red-600">
        <p class="text-gray-300 text-sm">Total Expenses</p>
        <p class="text-3xl font-bold text-red-600">₱{{ number_format($totalExpenses, 2) }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 border-l-4 {{ $netProfit >= 0 ? 'border-blue-600' : 'border-orange-600' }}">
        <p class="text-gray-300 text-sm">Net Profit</p>
        <p class="text-3xl font-bold {{ $netProfit >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
            {{ $netProfit >= 0 ? '+' : '' }}₱{{ number_format($netProfit, 2) }}
        </p>
    </div>
</div>

<!-- Income & Expense Breakdown -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Income by Source -->
    <div class="bg-gray-800 border border-gray-700 rounded-lg">
        <div class="p-6 border-b border-gray-600">
            <h3 class="font-semibold text-lg text-green-600">📈 Income by Source</h3>
        </div>
        <div class="p-6">
            @forelse($income as $item)
            <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                <span class="text-gray-300">{{ ucfirst(str_replace('_', ' ', $item->source)) }}</span>
                <span class="font-semibold text-green-600">₱{{ number_format($item->total, 2) }}</span>
            </div>
            @empty
            <p class="text-gray-400 text-center py-4">No income recorded</p>
            @endforelse
        </div>
    </div>

    <!-- Expenses by Category -->
    <div class="bg-gray-800 border border-gray-700 rounded-lg">
        <div class="p-6 border-b border-gray-600">
            <h3 class="font-semibold text-lg text-red-600">📉 Expenses by Category</h3>
        </div>
        <div class="p-6">
            @forelse($expenses as $item)
            <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                <span class="text-gray-300">{{ ucfirst($item->category) }}</span>
                <span class="font-semibold text-red-600">₱{{ number_format($item->total, 2) }}</span>
            </div>
            @empty
            <p class="text-gray-400 text-center py-4">No expenses recorded</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Daily Trend (Simple Table) -->
<div class="mt-6 bg-gray-800 border border-gray-700 rounded-lg">
    <div class="p-6 border-b border-gray-600">
        <h3 class="font-semibold text-lg">Daily Income Trend</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Date</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-400">Income</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-600">
                @forelse($dailyTrend->take(15) as $day)
                <tr>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                    <td class="px-4 py-2 text-right text-green-600 font-medium text-white">₱{{ number_format($day->income, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="px-4 py-4 text-center text-gray-400">No data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
