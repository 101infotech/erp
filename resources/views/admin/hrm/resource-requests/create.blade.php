@extends('admin.layouts.app')

@section('title', 'New Resource Request')
@section('page-title', 'New Resource Request')

@section('content')
<div class="px-6 md:px-8 py-6">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">New Resource Request</h1>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Submit a new resource or item request</p>
            </div>
            <a href="{{ route('admin.hrm.resource-requests.index') }}"
                class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white font-semibold rounded-lg transition">
                Back to List
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
            <form action="{{ route('admin.hrm.resource-requests.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Employee Selection -->
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                        Employee <span class="text-red-500">*</span>
                    </label>
                    <select name="employee_id" id="employee_id" required
                        class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('employee_id') border-red-500 @enderror">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id')==$employee->id ? 'selected' : '' }}>
                            {{ $employee->code }} - {{ $employee->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Item Name -->
                    <div>
                        <label for="item_name"
                            class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                            Item Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="item_name" id="item_name" value="{{ old('item_name') }}" required
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('item_name') border-red-500 @enderror"
                            placeholder="e.g., Laptop, Office Chair, Stationery">
                        @error('item_name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                            Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1"
                            required
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('quantity') border-red-500 @enderror">
                        @error('quantity')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category" id="category" required
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('category') border-red-500 @enderror">
                            <option value="">Select Category</option>
                            <option value="office_supplies" {{ old('category')=='office_supplies' ? 'selected' : '' }}>
                                Office Supplies</option>
                            <option value="equipment" {{ old('category')=='equipment' ? 'selected' : '' }}>Equipment
                            </option>
                            <option value="pantry" {{ old('category')=='pantry' ? 'selected' : '' }}>Pantry Items
                            </option>
                            <option value="furniture" {{ old('category')=='furniture' ? 'selected' : '' }}>Furniture
                            </option>
                            <option value="technology" {{ old('category')=='technology' ? 'selected' : '' }}>Technology
                            </option>
                            <option value="other" {{ old('category')=='other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('category')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                            Priority <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" id="priority" required
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('priority') border-red-500 @enderror">
                            <option value="">Select Priority</option>
                            <option value="low" {{ old('priority')=='low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority')=='medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority')=='high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority')=='urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estimated Cost -->
                    <div>
                        <label for="estimated_cost"
                            class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                            Estimated Cost (NPR)
                        </label>
                        <input type="number" name="estimated_cost" id="estimated_cost"
                            value="{{ old('estimated_cost') }}" min="0" step="0.01"
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('estimated_cost') border-red-500 @enderror"
                            placeholder="0.00">
                        @error('estimated_cost')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('description') border-red-500 @enderror"
                        placeholder="Provide additional details about the item...">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                        Reason for Request
                    </label>
                    <textarea name="reason" id="reason" rows="3"
                        class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('reason') border-red-500 @enderror"
                        placeholder="Explain why this resource is needed...">{{ old('reason') }}</textarea>
                    @error('reason')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('admin.hrm.resource-requests.index') }}"
                        class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-900 dark:text-white font-semibold rounded-lg transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endsection