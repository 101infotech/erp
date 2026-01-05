@extends('admin.layouts.app')

@section('title', 'Budget Details - Finance Management')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-2">
            <a href="{{ route('admin.finance.budgets.index') }}"
                class="hover:text-gray-900 dark:hover:text-white">Budgets</a>
            <span>/</span>
            <span>Budget Details</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Budget Details</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $budget->company->company_name }} - FY {{
                    $budget->fiscal_year_bs }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.finance.budgets.edit', $budget->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Budget
                </a>
            </div>
        </div>
    </div>

    <!-- Budget Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Budget Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Budget Information</h3>
                <span
                    class="px-3 py-1 text-xs rounded-full 
                    {{ $budget->status == 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                    {{ $budget->status == 'approved' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : '' }}
                    {{ $budget->status == 'draft' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' : '' }}">
                    {{ ucfirst($budget->status) }}
                </span>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Company</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $budget->company->company_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Category</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $budget->category->category_name ?? 'Overall
                        Budget' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Budget Type</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ ucfirst($budget->budget_type) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Period</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ $budget->budget_type == 'monthly' ? 'Month ' . $budget->period : ($budget->budget_type ==
                        'quarterly' ? 'Quarter ' . $budget->period : 'Full Year') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Fiscal Year</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $budget->fiscal_year_bs }} BS</p>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Financial Summary</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <p class="text-sm text-blue-600 dark:text-blue-400 mb-1">Budgeted Amount</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">NPR {{
                        number_format($budget->budgeted_amount, 2) }}</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <p class="text-sm text-green-600 dark:text-green-400 mb-1">Actual Expenses</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-300">NPR {{
                        number_format($budget->actual_amount, 2) }}</p>
                </div>
                <div
                    class="bg-{{ $budget->variance >= 0 ? 'green' : 'red' }}-50 dark:bg-{{ $budget->variance >= 0 ? 'green' : 'red' }}-900/20 rounded-lg p-4">
                    <p
                        class="text-sm text-{{ $budget->variance >= 0 ? 'green' : 'red' }}-600 dark:text-{{ $budget->variance >= 0 ? 'green' : 'red' }}-400 mb-1">
                        Variance {{ $budget->variance >= 0 ? '(Under)' : '(Over)' }}
                    </p>
                    <p
                        class="text-2xl font-bold text-{{ $budget->variance >= 0 ? 'green' : 'red' }}-700 dark:text-{{ $budget->variance >= 0 ? 'green' : 'red' }}-300">
                        NPR {{ number_format(abs($budget->variance), 2) }}
                    </p>
                    <p
                        class="text-xs text-{{ $budget->variance >= 0 ? 'green' : 'red' }}-600 dark:text-{{ $budget->variance >= 0 ? 'green' : 'red' }}-400 mt-1">
                        {{ number_format(abs($budget->variance_percentage), 2) }}%
                    </p>
                </div>
            </div>

            <!-- Progress Bar -->
            @php
            $percentage = $budget->budgeted_amount > 0 ? ($budget->actual_amount / $budget->budgeted_amount) * 100 : 0;
            $progressColor = $percentage <= 80 ? 'bg-green-500' : ($percentage <=100 ? 'bg-yellow-500' : 'bg-red-500' );
                $progressTextColor=$percentage <=80 ? 'text-green-600 dark:text-green-400' : ($percentage <=100
                ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400' ); @endphp <div
                class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Budget Utilization</span>
                    <span class="text-sm font-bold {{ $progressTextColor }}">{{ number_format($percentage, 1) }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-4 overflow-hidden">
                    <div class="{{ $progressColor }} h-full rounded-full transition-all duration-500"
                        style="width: {{ min($percentage, 100) }}%"></div>
                </div>
                <div class="flex items-center justify-between mt-1 text-xs text-gray-600 dark:text-gray-400">
                    <span>NPR 0</span>
                    <span>NPR {{ number_format($budget->budgeted_amount, 0) }}</span>
                </div>
        </div>

        <!-- Status Indicator -->
        @if($percentage >= 90)
        <div
            class="bg-{{ $percentage >= 100 ? 'red' : 'yellow' }}-50 dark:bg-{{ $percentage >= 100 ? 'red' : 'yellow' }}-900/20 border border-{{ $percentage >= 100 ? 'red' : 'yellow' }}-200 dark:border-{{ $percentage >= 100 ? 'red' : 'yellow' }}-700 rounded-lg p-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-{{ $percentage >= 100 ? 'red' : 'yellow' }}-600 dark:text-{{ $percentage >= 100 ? 'red' : 'yellow' }}-400"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" />
                </svg>
                <span
                    class="text-sm font-medium text-{{ $percentage >= 100 ? 'red' : 'yellow' }}-800 dark:text-{{ $percentage >= 100 ? 'red' : 'yellow' }}-200">
                    {{ $percentage >= 100 ? 'Budget exceeded! Review expenses immediately.' : 'Warning: Budget
                    utilization exceeds 90%' }}
                </span>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Additional Info & Recent Transactions -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Additional Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Additional Information</h3>
        <div class="space-y-3">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Created By</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $budget->createdBy->name ?? 'System' }}</p>
            </div>
            @if($budget->approved_by_user_id)
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Approved By</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $budget->approvedBy->name ?? 'N/A' }}</p>
            </div>
            @endif
            @if($budget->notes)
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Notes</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $budget->notes }}</p>
            </div>
            @endif
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Created At</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $budget->created_at->format('Y-m-d h:i A') }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Last Updated</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $budget->updated_at->format('Y-m-d h:i A') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Transactions</h3>
        @if($recentTransactions->count() > 0)
        <div class="space-y-3">
            @foreach($recentTransactions as $transaction)
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div class="flex-1">
                    <p class="font-medium text-gray-900 dark:text-white text-sm">{{
                        $transaction->category->category_name ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $transaction->transaction_date_bs }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-900 dark:text-white">NPR {{ number_format($transaction->credit_amount,
                        2) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $transaction->debitAccount->account_name ??
                        'N/A' }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="mt-2 text-sm">No transactions found</p>
        </div>
        @endif
    </div>
</div>
</div>
@endsection