@extends('farmowner.layouts.app')

@section('title', 'DSS Dashboard')
@section('header', 'Decision Support Dashboard')
@section('subheader', 'Real-time metrics and alerts for informed decisions')

@section('content')
<!-- Alerts -->
@if(!empty($alerts))
<div class="mb-6 space-y-2">
    @foreach($alerts as $alert)
    <div class="p-4 rounded-lg {{ $alert['type'] === 'danger' ? 'bg-red-900/30 border border-red-700' : 'bg-yellow-900/30 border border-yellow-700' }}">
        <p class="{{ $alert['type'] === 'danger' ? 'text-red-700' : 'text-yellow-700' }}">
            {{ $alert['type'] === 'danger' ? '🚨' : '⚠️' }} {{ $alert['message'] }}
        </p>
    </div>
    @endforeach
</div>
@endif

<!-- Key Metrics -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 text-center">
        <p class="text-3xl font-bold text-blue-600">{{ number_format($metrics['total_birds'] ?? 0) }}</p>
        <p class="text-gray-300 text-sm mt-1">Total Birds</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 text-center">
        <p class="text-3xl font-bold text-green-600">₱{{ number_format(($metrics['monthly_income'] ?? 0) / 1000, 1) }}K</p>
        <p class="text-gray-300 text-sm mt-1">Monthly Income</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 text-center">
        <p class="text-3xl font-bold text-red-600">₱{{ number_format(($metrics['monthly_expenses'] ?? 0) / 1000, 1) }}K</p>
        <p class="text-gray-300 text-sm mt-1">Monthly Expenses</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 text-center">
        <p class="text-3xl font-bold {{ ($metrics['profit'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ ($metrics['profit'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($metrics['profit_margin'] ?? 0, 1) }}%
        </p>
        <p class="text-gray-300 text-sm mt-1">Profit Margin</p>
    </div>
</div>

<!-- Secondary Metrics -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-yellow-500">
        <p class="text-2xl font-bold text-yellow-600">{{ $metrics['pending_orders'] ?? 0 }}</p>
        <p class="text-gray-400 text-xs">Pending Orders</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-blue-500">
        <p class="text-2xl font-bold text-blue-600">{{ $metrics['pending_deliveries'] ?? 0 }}</p>
        <p class="text-gray-400 text-xs">Pending Deliveries</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-red-500">
        <p class="text-2xl font-bold text-red-600">{{ $metrics['low_stock_items'] ?? 0 }}</p>
        <p class="text-gray-400 text-xs">Low Stock Items</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-green-500">
        <p class="text-2xl font-bold text-green-600">{{ $metrics['employees_active'] ?? 0 }}</p>
        <p class="text-gray-400 text-xs">Active Employees</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
    <h3 class="font-semibold text-lg mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('flocks.create') }}" class="flex items-center gap-2 px-4 py-3 bg-gray-700 rounded-lg hover:bg-gray-700">
            <span>🐔</span> Add Flock
        </a>
        <a href="{{ route('supplies.alerts') }}" class="flex items-center gap-2 px-4 py-3 bg-gray-700 rounded-lg hover:bg-gray-700">
            <span>📦</span> Stock Alerts
        </a>
        <a href="{{ route('attendance.index') }}" class="flex items-center gap-2 px-4 py-3 bg-gray-700 rounded-lg hover:bg-gray-700">
            <span>⏰</span> Attendance
        </a>
        <a href="{{ route('reports.financial') }}" class="flex items-center gap-2 px-4 py-3 bg-gray-700 rounded-lg hover:bg-gray-700">
            <span>📊</span> Financial Report
        </a>
    </div>
</div>

<!-- Profit Summary -->
<div class="mt-6 bg-gray-800 border border-gray-700 rounded-lg p-6">
    <h3 class="font-semibold text-lg mb-4">This Month Summary</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <p class="text-sm text-gray-300">Total Income</p>
            <p class="text-2xl font-bold text-green-600">₱{{ number_format($metrics['monthly_income'] ?? 0, 2) }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-300">Total Expenses</p>
            <p class="text-2xl font-bold text-red-600">₱{{ number_format($metrics['monthly_expenses'] ?? 0, 2) }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-300">Net Profit</p>
            <p class="text-2xl font-bold {{ ($metrics['profit'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                ₱{{ number_format($metrics['profit'] ?? 0, 2) }}
            </p>
        </div>
    </div>
</div>
@endsection
