@extends('admin.layouts.app')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.finance.customers.index', ['company_id' => $customer->company_id]) }}"
            class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i> Back to Customers
        </a>
    </div>

    <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-6">Edit Customer</h2>

    <form action="{{ route('admin.finance.customers.update', $customer) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Customer Code</label>
                <input type="text" name="customer_code" value="{{ old('customer_code', $customer->customer_code) }}"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                @error('customer_code')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Company</label>
                <input type="text" value="{{ $customer->company->name }}" readonly
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white bg-slate-50">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Customer Name *</label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Customer Type *</label>
                <select name="customer_type" required
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                    <option value="individual" {{ old('customer_type', $customer->customer_type)=='individual' ? 'selected' : '' }}>Individual</option>
                    <option value="corporate" {{ old('customer_type', $customer->customer_type)=='corporate' ? 'selected' : '' }}>Corporate</option>
                    <option value="government" {{ old('customer_type', $customer->customer_type)=='government' ? 'selected' : '' }}>Government</option>
                </select>
                @error('customer_type')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">PAN Number</label>
                <input type="text" name="pan_number" value="{{ old('pan_number', $customer->pan_number) }}"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                @error('pan_number')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Contact Person</label>
                <input type="text" name="contact_person" value="{{ old('contact_person', $customer->contact_person) }}"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                @error('contact_person')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                @error('phone')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Address</label>
                <textarea name="address" rows="2"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">{{ old('address', $customer->address) }}</textarea>
                @error('address')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ $customer->is_active ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm text-slate-700 dark:text-slate-300">Active</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end space-x-4 mt-6">
            <a href="{{ route('admin.finance.customers.index', ['company_id' => $customer->company_id]) }}"
                class="px-6 py-2 bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Update Customer
            </button>
        </div>
    </form>
</div>
@endsection
