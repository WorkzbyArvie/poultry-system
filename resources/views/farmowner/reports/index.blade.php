@extends('farmowner.layouts.app')

@section('title', 'Reports')
@section('header', 'Reports & Analytics')
@section('subheader', 'Generate business reports and insights')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- DSS Dashboard -->
    <a href="{{ route('reports.dashboard') }}" class="bg-gray-800 border border-gray-700 rounded-lg p-6 hover:shadow-lg transition-shadow border-l-4 border-green-600">
        <div class="text-3xl mb-3">📊</div>
        <h3 class="font-semibold text-lg text-white">DSS Dashboard</h3>
        <p class="text-gray-300 text-sm mt-1">Key metrics and real-time alerts for decision support</p>
    </a>

    <!-- Financial Report -->
    <a href="{{ route('reports.financial') }}" class="bg-gray-800 border border-gray-700 rounded-lg p-6 hover:shadow-lg transition-shadow border-l-4 border-blue-600">
        <div class="text-3xl mb-3">💰</div>
        <h3 class="font-semibold text-lg text-white">Financial Report</h3>
        <p class="text-gray-300 text-sm mt-1">Income vs expenses, profit margins, trends</p>
    </a>

    <!-- Production Report -->
    <a href="{{ route('reports.production') }}" class="bg-gray-800 border border-gray-700 rounded-lg p-6 hover:shadow-lg transition-shadow border-l-4 border-yellow-600">
        <div class="text-3xl mb-3">🐔</div>
        <h3 class="font-semibold text-lg text-white">Production Report</h3>
        <p class="text-gray-300 text-sm mt-1">Flock performance, egg production, mortality</p>
    </a>

    <!-- Inventory Report -->
    <a href="{{ route('reports.inventory') }}" class="bg-gray-800 border border-gray-700 rounded-lg p-6 hover:shadow-lg transition-shadow border-l-4 border-purple-600">
        <div class="text-3xl mb-3">📦</div>
        <h3 class="font-semibold text-lg text-white">Inventory Report</h3>
        <p class="text-gray-300 text-sm mt-1">Stock levels, value, expiring items</p>
    </a>

    <!-- Sales Report -->
    <a href="{{ route('reports.sales') }}" class="bg-gray-800 border border-gray-700 rounded-lg p-6 hover:shadow-lg transition-shadow border-l-4 border-orange-600">
        <div class="text-3xl mb-3">🛒</div>
        <h3 class="font-semibold text-lg text-white">Sales Report</h3>
        <p class="text-gray-300 text-sm mt-1">Order analytics, top customers, trends</p>
    </a>

    <!-- Delivery Report -->
    <a href="{{ route('reports.delivery') }}" class="bg-gray-800 border border-gray-700 rounded-lg p-6 hover:shadow-lg transition-shadow border-l-4 border-teal-600">
        <div class="text-3xl mb-3">🚚</div>
        <h3 class="font-semibold text-lg text-white">Delivery Report</h3>
        <p class="text-gray-300 text-sm mt-1">Delivery success rate, driver performance</p>
    </a>

    <!-- Payroll Report -->
    <a href="{{ route('reports.payroll') }}" class="bg-gray-800 border border-gray-700 rounded-lg p-6 hover:shadow-lg transition-shadow border-l-4 border-red-600">
        <div class="text-3xl mb-3">👥</div>
        <h3 class="font-semibold text-lg text-white">Payroll Report</h3>
        <p class="text-gray-300 text-sm mt-1">Monthly payroll, deductions, by department</p>
    </a>
</div>

<!-- Quick Export -->
<div class="mt-8 bg-gray-800 border border-gray-700 rounded-lg p-6">
    <h3 class="font-semibold text-lg mb-4">Quick Export (CSV)</h3>
    <div class="flex flex-wrap gap-4">
        <a href="{{ route('reports.export', ['type' => 'financial']) }}?start_date={{ now()->startOfMonth()->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" 
           class="px-4 py-2 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-200">
            📥 Financial (This Month)
        </a>
        <a href="{{ route('reports.export', ['type' => 'inventory']) }}" 
           class="px-4 py-2 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-200">
            📥 Current Inventory
        </a>
    </div>
</div>
@endsection
