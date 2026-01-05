@extends('admin.layouts.app')

@section('title', 'Recurring Expense Details - Finance Management')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-2">
            <a href="{{ route('admin.finance.recurring-expenses.index') }}"
                class="hover:text-gray-900 dark:hover:text-white">Recurring Expenses</a>
            <span>/</span>
            <span>Details</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $recurringExpense->expense_name }}</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $recurringExpense->company->company_name }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.finance.recurring-expenses.edit', $recurringExpense->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Expense Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Details Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Expense Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Expense Name</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $recurringExpense->expense_name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Company</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{
                            $recurringExpense->company->company_name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Category</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{
                            $recurringExpense->category->category_name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Amount</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">NPR {{
                            number_format($recurringExpense->amount, 2) }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Frequency</p>
                        <span
                            class="inline-block mt-1 px-3 py-1 text-sm rounded-full 
                            {{ $recurringExpense->frequency == 'monthly' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : '' }}
                            {{ $recurringExpense->frequency == 'quarterly' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300' : '' }}
                            {{ $recurringExpense->frequency == 'annually' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300' : '' }}">
                            {{ ucfirst($recurringExpense->frequency) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Payment Method</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{
                            ucfirst($recurringExpense->payment_method) }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Payment Account</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">
                            {{ $recurringExpense->account ? $recurringExpense->account->account_name : 'N/A' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <span
                            class="inline-block mt-1 px-3 py-1 text-sm rounded-full {{ $recurringExpense->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                            {{ $recurringExpense->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Schedule Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Schedule Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Start Date (BS)</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $recurringExpense->start_date_bs }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">End Date (BS)</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $recurringExpense->end_date_bs ??
                            'Ongoing' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Last Generated (BS)</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{
                            $recurringExpense->last_generated_date_bs ?? 'Not yet generated' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Next Due Date (BS)</p>
                        <p class="font-medium text-green-600 dark:text-green-400 mt-1">{{
                            $recurringExpense->next_due_date_bs ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Auto-Create Status -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex items-center gap-3">
                        @if($recurringExpense->auto_create_transaction)
                        <div
                            class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Auto-Create Enabled</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Transactions will be created
                                automatically</p>
                        </div>
                        @else
                        <div
                            class="flex-shrink-0 w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Manual Entry</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Transactions must be created manually
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Stats</h3>

                <div class="space-y-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <p class="text-sm text-blue-600 dark:text-blue-400 mb-1">{{
                            ucfirst($recurringExpense->frequency) }} Cost</p>
                        <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">NPR {{
                            number_format($recurringExpense->amount, 2) }}</p>
                    </div>

                    @php
                    $annualCost = match($recurringExpense->frequency) {
                    'monthly' => $recurringExpense->amount * 12,
                    'quarterly' => $recurringExpense->amount * 4,
                    'annually' => $recurringExpense->amount,
                    default => 0
                    };
                    @endphp

                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <p class="text-sm text-green-600 dark:text-green-400 mb-1">Estimated Annual</p>
                        <p class="text-2xl font-bold text-green-700 dark:text-green-300">NPR {{
                            number_format($annualCost, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Information</h3>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Created By</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $recurringExpense->createdBy->name
                            ?? 'System' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Created At</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{
                            $recurringExpense->created_at->format('Y-m-d h:i A') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Last Updated</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{
                            $recurringExpense->updated_at->format('Y-m-d h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>

                <div class="space-y-2">
                    <a href="{{ route('admin.finance.recurring-expenses.edit', $recurringExpense->id) }}"
                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Expense
                    </a>

                    <button type="button" onclick="openModal('deleteExpenseShowModal')"
                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Expense
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Recurring Expense Modal -->
<x-professional-modal id="deleteExpenseShowModal" title="Delete Recurring Expense"
    subtitle="This action cannot be undone" icon="trash" iconColor="red" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Are you sure you want to delete this recurring expense?</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Expense:</span> {{ $recurringExpense->description }}
            </p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Amount:</span> NPR {{
                number_format($recurringExpense->amount, 2) }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('deleteExpenseShowModal')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Keep</button>
        <form action="{{ route('admin.finance.recurring-expenses.destroy', $recurringExpense->id) }}" method="POST"
            class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete
            </button>
        </form>
    </x-slot>
</x-professional-modal>
@endsection