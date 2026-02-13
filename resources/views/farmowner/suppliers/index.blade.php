@extends('farmowner.layouts.app')

@section('title', 'Suppliers')
@section('header', 'Supplier Management')
@section('subheader', 'Manage your feed, vaccine, and equipment suppliers')

@section('header-actions')
<a href="{{ route('suppliers.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">+ Add Supplier</a>
@endsection

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-blue-600">
        <p class="text-gray-400 text-xs">Total Suppliers</p>
        <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-green-600">
        <p class="text-gray-400 text-xs">Active</p>
        <p class="text-2xl font-bold text-green-600">{{ $stats['active'] ?? 0 }}</p>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 border-l-4 border-red-600">
        <p class="text-gray-400 text-xs">Total Outstanding</p>
        <p class="text-2xl font-bold text-red-600">₱{{ number_format($stats['total_outstanding'] ?? 0, 2) }}</p>
    </div>
</div>

<!-- Filter -->
<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <select name="category" class="px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            <option value="">All Categories</option>
            @foreach(['feeds', 'vitamins', 'vaccines', 'equipment', 'chicks', 'general'] as $cat)
            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
            @endforeach
        </select>
        <select name="status" class="px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-200 text-gray-200 rounded-lg hover:bg-gray-300">Filter</button>
    </form>
</div>

<!-- Table -->
<div class="bg-gray-800 border border-gray-700 rounded-lg">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Company</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Outstanding</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-600">
                @forelse($suppliers as $supplier)
                <tr class="hover:bg-gray-700">
                    <td class="px-6 py-4 font-medium text-white">{{ $supplier->company_name }}</td>
                    <td class="px-6 py-4 text-gray-300">{{ $supplier->contact_person ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-300">{{ $supplier->phone ?? '-' }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs bg-blue-900 text-blue-300 rounded-full">{{ ucfirst($supplier->category) }}</span></td>
                    <td class="px-6 py-4 {{ $supplier->outstanding_balance > 0 ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                        ₱{{ number_format($supplier->outstanding_balance ?? 0, 2) }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $supplier->status === 'active' ? 'bg-green-900 text-green-300' : 'bg-gray-700 text-gray-300' }}">
                            {{ ucfirst($supplier->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('suppliers.show', $supplier) }}" class="text-blue-400 hover:text-blue-300">View</a>
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="text-green-400 hover:text-green-300">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">No suppliers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
    <div class="p-6 border-t border-gray-600">{{ $suppliers->links() }}</div>
    @endif
</div>
@endsection
