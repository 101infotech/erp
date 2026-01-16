@extends('admin.layouts.app')

@section('title', 'New Expense Claim')
@section('page-title', 'New Expense Claim')

@section('content')
<div class="px-6 md:px-8 py-6">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">New Expense Claim</h1>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Submit a new expense reimbursement claim</p>
            </div>
            <a href="{{ route('admin.hrm.expense-claims.index') }}"
                class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white font-semibold rounded-lg transition">
                Back to List
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
            <form action="{{ route('admin.hrm.expense-claims.store') }}" method="POST" enctype="multipart/form-data"
                class="p-6 space-y-6">
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
                    <!-- Expense Type -->
                    <div>
                        <label for="expense_type"
                            class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                            Expense Type <span class="text-red-500">*</span>
                        </label>
                        <select name="expense_type" id="expense_type" required
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('expense_type') border-red-500 @enderror">
                            <option value="">Select Type</option>
                            <option value="travel" {{ old('expense_type')=='travel' ? 'selected' : '' }}>Travel</option>
                            <option value="accommodation" {{ old('expense_type')=='accommodation' ? 'selected' : '' }}>
                                Accommodation</option>
                            <option value="meals" {{ old('expense_type')=='meals' ? 'selected' : '' }}>Meals</option>
                            <option value="transportation" {{ old('expense_type')=='transportation' ? 'selected' : ''
                                }}>
                                Transportation</option>
                            <option value="supplies" {{ old('expense_type')=='supplies' ? 'selected' : '' }}>Supplies
                            </option>
                            <option value="communication" {{ old('expense_type')=='communication' ? 'selected' : '' }}>
                                Communication</option>
                            <option value="other" {{ old('expense_type')=='other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('expense_type')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Expense Date -->
                    <div>
                        <label for="expense_date"
                            class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                            Expense Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="expense_date" id="expense_date"
                            value="{{ old('expense_date', date('Y-m-d')) }}" required
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('expense_date') border-red-500 @enderror">
                        @error('expense_date')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('title') border-red-500 @enderror"
                        placeholder="e.g., Client Meeting Transportation, Office Supplies">
                    @error('title')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="amount" id="amount" value="{{ old('amount') }}" min="0" step="0.01"
                            required
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('amount') border-red-500 @enderror"
                            placeholder="0.00">
                        @error('amount')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                            Currency <span class="text-red-500">*</span>
                        </label>
                        <select name="currency" id="currency" required
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('currency') border-red-500 @enderror">
                            <option value="NPR" {{ old('currency', 'NPR' )=='NPR' ? 'selected' : '' }}>NPR</option>
                            <option value="USD" {{ old('currency')=='USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ old('currency')=='EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="INR" {{ old('currency')=='INR' ? 'selected' : '' }}>INR</option>
                        </select>
                        @error('currency')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Project Code -->
                    <div>
                        <label for="project_code"
                            class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                            Project Code
                        </label>
                        <input type="text" name="project_code" id="project_code" value="{{ old('project_code') }}"
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('project_code') border-red-500 @enderror"
                            placeholder="e.g., PRJ-2024-001">
                        @error('project_code')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cost Center -->
                    <div>
                        <label for="cost_center"
                            class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                            Cost Center
                        </label>
                        <input type="text" name="cost_center" id="cost_center" value="{{ old('cost_center') }}"
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('cost_center') border-red-500 @enderror"
                            placeholder="e.g., IT-001">
                        @error('cost_center')
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
                        placeholder="Provide details about the expense...">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                        Additional Notes
                    </label>
                    <textarea name="notes" id="notes" rows="2"
                        class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 @error('notes') border-red-500 @enderror"
                        placeholder="Any additional information...">{{ old('notes') }}</textarea>
                    @error('notes')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Receipt Upload -->
                <div>
                    <label for="receipt" class="block text-sm font-medium text-slate-900 dark:text-slate-300 mb-2">
                        Receipt (PDF, JPG, PNG - Max 5MB)
                    </label>
                    <input type="file" name="receipt" id="receipt" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-lime-50 file:text-lime-700 hover:file:bg-lime-100 dark:file:bg-lime-900 dark:file:text-lime-300 @error('receipt') border-red-500 @enderror">
                    @error('receipt')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Upload receipt/invoice for verification
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('admin.hrm.expense-claims.index') }}"
                        class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-900 dark:text-white font-semibold rounded-lg transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
                        Submit Claim
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endsection