@extends('farmowner.layouts.app')

@section('title', 'Add Expense')
@section('header', 'Record Expense')

@section('content')
<div class="max-w-xl">
    <form action="{{ route('expenses.store') }}" method="POST" class="bg-gray-800 border border-gray-700 rounded-lg p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Category *</label>
                <select name="category" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">Select category</option>
                    <option value="feeds" {{ old('category') === 'feeds' ? 'selected' : '' }}>Feeds</option>
                    <option value="vaccines" {{ old('category') === 'vaccines' ? 'selected' : '' }}>Vaccines & Medicine</option>
                    <option value="utilities" {{ old('category') === 'utilities' ? 'selected' : '' }}>Utilities</option>
                    <option value="salaries" {{ old('category') === 'salaries' ? 'selected' : '' }}>Salaries</option>
                    <option value="equipment" {{ old('category') === 'equipment' ? 'selected' : '' }}>Equipment</option>
                    <option value="transport" {{ old('category') === 'transport' ? 'selected' : '' }}>Transport</option>
                    <option value="maintenance" {{ old('category') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Date *</label>
                <input type="date" name="expense_date" value="{{ old('expense_date', now()->format('Y-m-d')) }}" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1">Description *</label>
                <input type="text" name="description" value="{{ old('description') }}" required placeholder="e.g., B-Meg Grower 50kg x 10 bags"
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
                    <option value="credit" {{ old('payment_method') === 'credit' ? 'selected' : '' }}>Credit/Payable</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Receipt/Reference #</label>
                <input type="text" name="receipt_number" value="{{ old('receipt_number') }}"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Supplier</label>
                <select name="supplier_id"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">-- None --</option>
                    @foreach($suppliers ?? [] as $supplier)
                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->company_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Save Expense</button>
            <a href="{{ route('expenses.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500">Cancel</a>
        </div>
    </form>
</div>
@endsection
