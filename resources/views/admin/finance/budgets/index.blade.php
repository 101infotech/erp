@extends('admin.layouts.app')

@section('title', 'Budgets - Finance Management')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Budget Management</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Track and monitor budget allocations</p>
        </div>
        <a href="{{ route('admin.finance.budgets.create') }}"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Budget
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Budgeted</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">रू {{
                        number_format($totalBudgeted, 2) }}</h3>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Actual Expenses</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">रू {{ number_format($totalActual,
                        2) }}</h3>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Variance</p>
                    <h3
                        class="text-2xl font-bold {{ $totalVariance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-1">
                        रू {{ number_format(abs($totalVariance), 2) }}
                    </h3>
                    <p
                        class="text-xs {{ $totalVariance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-1">
                        {{ $totalVariance >= 0 ? 'Under Budget' : 'Over Budget' }}
                    </p>
                </div>
                <div
                    class="p-3 {{ $totalVariance >= 0 ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} rounded-lg">
                    <svg class="w-8 h-8 {{ $totalVariance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('admin.finance.budgets.index') }}"
            class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company</label>
                <select name="company_id"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id')==$company->id ? 'selected' : '' }}>
                        {{ $company->company_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fiscal Year</label>
                <input type="text" name="fiscal_year_bs" value="{{ request('fiscal_year_bs') }}"
                    placeholder="e.g., 2081" maxlength="4"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Budget Type</label>
                <select name="budget_type"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">All Types</option>
                    <option value="monthly" {{ request('budget_type')=='monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="quarterly" {{ request('budget_type')=='quarterly' ? 'selected' : '' }}>Quarterly
                    </option>
                    <option value="annual" {{ request('budget_type')=='annual' ? 'selected' : '' }}>Annual</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status')=='draft' ? 'selected' : '' }}>Draft</option>
                    <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                    <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                </select>
            </div>

            <div class="md:col-span-4 flex gap-2">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route('admin.finance.budgets.index') }}"
                    class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Budgets Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <div class="w-full">
            <table class="w-full table-auto">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Company</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Category</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Fiscal Year</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Type</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Period</th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Budgeted</th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actual</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Progress</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($budgets as $budget)
                    @php
                    $percentage = $budget->budgeted_amount > 0 ? ($budget->actual_amount / $budget->budgeted_amount) *
                    100 : 0;
                    $progressColor = $percentage <= 80 ? 'bg-green-500' : ($percentage <=100 ? 'bg-yellow-500'
                        : 'bg-red-500' ); @endphp <tr
                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $budget->company->company_name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $budget->category->category_name
                            ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $budget->fiscal_year_bs }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span
                                class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                {{ ucfirst($budget->budget_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                            {{ $budget->budget_type == 'monthly' ? 'Month ' . $budget->period : ($budget->budget_type ==
                            'quarterly' ? 'Q' . $budget->period : 'Full Year') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900 dark:text-white">रू {{
                            number_format($budget->budgeted_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900 dark:text-white">रू {{
                            number_format($budget->actual_amount, 2) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 dark:bg-gray-600 rounded-full h-2 overflow-hidden">
                                    <div class="{{ $progressColor }} h-full rounded-full transition-all"
                                        style="width: {{ min($percentage, 100) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400 min-w-[3rem] text-right">{{
                                    number_format($percentage, 1) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="px-3 py-1 text-xs rounded-full 
                                    {{ $budget->status == 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                                    {{ $budget->status == 'approved' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : '' }}
                                    {{ $budget->status == 'draft' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' : '' }}">
                                {{ ucfirst($budget->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.finance.budgets.show', $budget->id) }}"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.finance.budgets.edit', $budget->id) }}"
                                    class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300"
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button type="button" onclick="openModal('deleteBudgetModal_{{ $budget->id }}')"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                    title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="mt-2">No budgets found</p>
                            </td>
                        </tr>
                        @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($budgets->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $budgets->links() }}
        </div>
        @endif
    </div>
</div>
@foreach($budgets as $budget)
<!-- Delete Budget Modal -->
<x-professional-modal id="deleteBudgetModal_{{ $budget->id }}" title="Delete Budget"
    subtitle="This action cannot be undone" icon="trash" iconColor="red" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Are you sure you want to delete this budget?</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Budget:</span> {{ $budget->name }}</p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Amount:</span> NPR {{
                number_format($budget->amount, 2) }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('deleteBudgetModal_{{ $budget->id }}')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Keep</button>
        <form action="{{ route('admin.finance.budgets.destroy', $budget->id) }}" method="POST" class="inline">
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
@endforeach
@endsection