@extends('farmowner.layouts.app')

@section('title', 'Edit Supplier')
@section('header', 'Edit Supplier')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="bg-gray-800 border border-gray-700 rounded-lg p-6">
        @csrf
        @method('PUT')
        
        <!-- Company Info -->
        <h3 class="font-semibold text-lg mb-4 pb-2 border-b border-gray-600">Company Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1">Company Name *</label>
                <input type="text" name="company_name" value="{{ old('company_name', $supplier->company_name) }}" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 @error('company_name') border-red-500 @enderror">
                @error('company_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Category *</label>
                <select name="category" required
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="feeds" {{ old('category', $supplier->category) === 'feeds' ? 'selected' : '' }}>Feeds</option>
                    <option value="vitamins" {{ old('category', $supplier->category) === 'vitamins' ? 'selected' : '' }}>Vitamins</option>
                    <option value="vaccines" {{ old('category', $supplier->category) === 'vaccines' ? 'selected' : '' }}>Vaccines</option>
                    <option value="equipment" {{ old('category', $supplier->category) === 'equipment' ? 'selected' : '' }}>Equipment</option>
                    <option value="chicks" {{ old('category', $supplier->category) === 'chicks' ? 'selected' : '' }}>Day-Old Chicks</option>
                    <option value="general" {{ old('category', $supplier->category) === 'general' ? 'selected' : '' }}>General</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                <select name="status"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="active" {{ old('status', $supplier->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $supplier->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <!-- Contact Info -->
        <h3 class="font-semibold text-lg mb-4 pb-2 border-b border-gray-600">Contact Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Contact Person</label>
                <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Position/Title</label>
                <input type="text" name="contact_title" value="{{ old('contact_title', $supplier->contact_title) }}"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Phone</label>
                <input type="tel" name="phone" value="{{ old('phone', $supplier->phone) }}"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $supplier->email) }}"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1">Address</label>
                <textarea name="address" rows="2"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">{{ old('address', $supplier->address) }}</textarea>
            </div>
        </div>

        <!-- Payment Terms -->
        <h3 class="font-semibold text-lg mb-4 pb-2 border-b border-gray-600">Payment Terms</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Payment Terms</label>
                <select name="payment_terms"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="cod" {{ old('payment_terms', $supplier->payment_terms) === 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                    <option value="net15" {{ old('payment_terms', $supplier->payment_terms) === 'net15' ? 'selected' : '' }}>Net 15</option>
                    <option value="net30" {{ old('payment_terms', $supplier->payment_terms) === 'net30' ? 'selected' : '' }}>Net 30</option>
                    <option value="net60" {{ old('payment_terms', $supplier->payment_terms) === 'net60' ? 'selected' : '' }}>Net 60</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Credit Limit (₱)</label>
                <input type="number" name="credit_limit" value="{{ old('credit_limit', $supplier->credit_limit) }}" step="0.01" min="0"
                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
        </div>

        <!-- Notes -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-300 mb-1">Notes</label>
            <textarea name="notes" rows="3"
                class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500">{{ old('notes', $supplier->notes) }}</textarea>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Update Supplier</button>
            <a href="{{ route('suppliers.show', $supplier) }}" class="px-6 py-2 bg-gray-200 text-gray-200 rounded-lg hover:bg-gray-300">Cancel</a>
        </div>
    </form>
</div>
@endsection
