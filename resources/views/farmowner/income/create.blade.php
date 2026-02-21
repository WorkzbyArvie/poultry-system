@extends('farmowner.layouts.app')

@section('title', 'Record Income')
@section('header', 'Record Income')

@section('content')
<div class="max-w-xl">
    <form action="{{ route('income.store') }}" method="POST" class="bg-gray-800 border border-gray-700 rounded-lg p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Source *</label>
                <select name="source" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">Select source</option>
                    <option value="egg_sales" {{ old('source') === 'egg_sales' ? 'selected' : '' }}>Egg Sales</option>
                    <option value="chicken_sales" {{ old('source') === 'chicken_sales' ? 'selected' : '' }}>Chicken Sales</option>
                    <option value="manure_sales" {{ old('source') === 'manure_sales' ? 'selected' : '' }}>Manure Sales</option>
                    <option value="order_payment" {{ old('source') === 'order_payment' ? 'selected' : '' }}>Order Payment</option>
                    <option value="other" {{ old('source') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('source')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Date *</label>
                <input type="date" name="income_date" value="{{ old('income_date', now()->format('Y-m-d')) }}" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1">Description *</label>
                <input type="text" name="description" value="{{ old('description') }}" required placeholder="e.g., 50 trays eggs sold to Restaurant ABC"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 @error('description') border-red-500 @enderror">
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Amount (₱) *</label>
                <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 @error('amount') border-red-500 @enderror">
                @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Payment Method</label>
                <select name="payment_method"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="gcash" {{ old('payment_method') === 'gcash' ? 'selected' : '' }}>GCash</option>
                    <option value="receivable" {{ old('payment_method') === 'receivable' ? 'selected' : '' }}>Receivable (Not Yet Paid)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Customer Name</label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Optional"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Reference #</label>
                <input type="text" name="reference_number" value="{{ old('reference_number') }}"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Save Income</button>
            <a href="{{ route('income.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500">Cancel</a>
        </div>
    </form>
</div>
@endsection
