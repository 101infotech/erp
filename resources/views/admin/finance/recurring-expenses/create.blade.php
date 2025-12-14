@extends('admin.layouts.app')

@section('title', 'Create Recurring Expense - Finance Management')

@section('content')
<div class="container-fluid px-4 py-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-2">
            <a href="{{ route('admin.finance.recurring-expenses.index') }}"
                class="hover:text-gray-900 dark:hover:text-white">Recurring Expenses</a>
            <span>/</span>
            <span>Create New</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Recurring Expense</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Set up automated recurring expense schedule</p>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.finance.recurring-expenses.store') }}" method="POST"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        @csrf

        <div class="p-6 space-y-6">
            <!-- Company Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Company <span class="text-red-500">*</span>
                </label>
                <select name="company_id" id="company_id" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('company_id') border-red-500 @enderror">
                    <option value="">Select Company</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ old('company_id', $companyId)==$company->id ? 'selected' : ''
                        }}>
                        {{ $company->company_name }}
                    </option>
                    @endforeach
                </select>
                @error('company_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Expense Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Expense Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="expense_name" id="expense_name" required maxlength="255"
                    value="{{ old('expense_name') }}" placeholder="e.g., Office Rent, Internet Subscription"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('expense_name') border-red-500 @enderror">
                @error('expense_name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expense Category</label>
                <select name="category_id" id="category_id"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('category_id') border-red-500 @enderror">
                    <option value="">Select Category (Optional)</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id')==$category->id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Amount (रू) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="amount" id="amount" step="0.01" min="0" required value="{{ old('amount') }}"
                    placeholder="0.00"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @enderror">
                @error('amount')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Frequency -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Frequency <span class="text-red-500">*</span>
                </label>
                <select name="frequency" id="frequency" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('frequency') border-red-500 @enderror">
                    <option value="">Select Frequency</option>
                    <option value="monthly" {{ old('frequency')=='monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="quarterly" {{ old('frequency')=='quarterly' ? 'selected' : '' }}>Quarterly</option>
                    <option value="annually" {{ old('frequency')=='annually' ? 'selected' : '' }}>Annually</option>
                </select>
                @error('frequency')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Start Date (BS) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="start_date_bs" id="start_date_bs" required
                        value="{{ old('start_date_bs') }}" placeholder="YYYY-MM-DD"
                        class="nepali-datepicker w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('start_date_bs') border-red-500 @enderror">
                    @error('start_date_bs')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date (BS)</label>
                    <input type="text" name="end_date_bs" id="end_date_bs" value="{{ old('end_date_bs') }}"
                        placeholder="YYYY-MM-DD (Optional)"
                        class="nepali-datepicker w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('end_date_bs') border-red-500 @enderror">
                    @error('end_date_bs')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank for ongoing expense</p>
                </div>
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Payment Method <span class="text-red-500">*</span>
                </label>
                <select name="payment_method" id="payment_method" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('payment_method') border-red-500 @enderror">
                    <option value="">Select Payment Method</option>
                    <option value="cash" {{ old('payment_method')=='cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank" {{ old('payment_method')=='bank' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="cheque" {{ old('payment_method')=='cheque' ? 'selected' : '' }}>Cheque</option>
                    <option value="online" {{ old('payment_method')=='online' ? 'selected' : '' }}>Online Payment
                    </option>
                </select>
                @error('payment_method')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Account -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Account</label>
                <select name="account_id" id="account_id"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('account_id') border-red-500 @enderror">
                    <option value="">Select Account (Optional)</option>
                    @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ old('account_id')==$account->id ? 'selected' : '' }}>
                        {{ $account->account_name }} ({{ $account->account_code }})
                    </option>
                    @endforeach
                </select>
                @error('account_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Next Due Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Next Due Date
                    (BS)</label>
                <input type="text" name="next_due_date_bs" id="next_due_date_bs" value="{{ old('next_due_date_bs') }}"
                    placeholder="YYYY-MM-DD (Optional)"
                    class="nepali-datepicker w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('next_due_date_bs') border-red-500 @enderror">
                @error('next_due_date_bs')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">System will calculate if left blank</p>
            </div>

            <!-- Auto Create Transaction -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="hidden" name="auto_create_transaction" value="0">
                    <input type="checkbox" name="auto_create_transaction" id="auto_create_transaction" value="1" {{
                        old('auto_create_transaction') ? 'checked' : '' }}
                        class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Auto-Create Transactions</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                            Automatically create expense transactions when due date arrives
                        </p>
                    </div>
                </label>
            </div>

            <!-- Active Status -->
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked'
                        : '' }} class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                </label>
                <p class="ml-8 text-xs text-gray-500 dark:text-gray-400">Inactive expenses will not generate
                    transactions</p>
            </div>
        </div>

        <!-- Form Actions -->
        <div
            class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600 rounded-b-lg flex gap-3">
            <button type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                Create Recurring Expense
            </button>
            <a href="{{ route('admin.finance.recurring-expenses.index') }}"
                class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection