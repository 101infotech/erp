@extends('admin.layouts.app')

@section('title', 'Recurring Expenses - Finance Management')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Recurring Expenses</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage automated recurring expense schedules</p>
        </div>
        <a href="{{ route('admin.finance.recurring-expenses.create') }}"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Recurring Expense
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('admin.finance.recurring-expenses.index') }}"
            class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Frequency</label>
                <select name="frequency"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">All Frequencies</option>
                    <option value="monthly" {{ request('frequency')=='monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="quarterly" {{ request('frequency')=='quarterly' ? 'selected' : '' }}>Quarterly
                    </option>
                    <option value="annually" {{ request('frequency')=='annually' ? 'selected' : '' }}>Annually</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="is_active"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active')==='1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active')==='0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="md:col-span-3 flex gap-2">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route('admin.finance.recurring-expenses.index') }}"
                    class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Recurring Expenses Table -->
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
                            Expense Name</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Category</th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Amount</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Frequency</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Next Due Date</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Auto-Create</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recurringExpenses as $expense)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $expense->company->company_name
                            }}</td>
                        <td class="px-6 py-4 text-sm">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $expense->expense_name }}</p>
                                @if($expense->payment_method)
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ ucfirst($expense->payment_method)
                                    }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $expense->category->category_name
                            ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900 dark:text-white">रू {{
                            number_format($expense->amount, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="px-3 py-1 text-xs rounded-full 
                                    {{ $expense->frequency == 'monthly' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : '' }}
                                    {{ $expense->frequency == 'quarterly' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300' : '' }}
                                    {{ $expense->frequency == 'annually' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300' : '' }}">
                                {{ ucfirst($expense->frequency) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-center text-gray-900 dark:text-white">
                            {{ $expense->next_due_date_bs ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($expense->auto_create_transaction)
                            <span class="inline-flex items-center text-green-600 dark:text-green-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                                </svg>
                            </span>
                            @else
                            <span class="inline-flex items-center text-gray-400 dark:text-gray-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" />
                                </svg>
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="px-3 py-1 text-xs rounded-full {{ $expense->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                {{ $expense->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.finance.recurring-expenses.show', $expense->id) }}"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.finance.recurring-expenses.edit', $expense->id) }}"
                                    class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300"
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button type="button" onclick="openModal('deleteExpenseModal_{{ $expense->id }}')"
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
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2">No recurring expenses found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($recurringExpenses->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $recurringExpenses->links() }}
        </div>
        @endif
    </div>
</div>

@foreach($expenses as $expense)
<!-- Delete Recurring Expense Modal -->
<x-professional-modal id="deleteExpenseModal_{{ $expense->id }}" title="Delete Recurring Expense"
    subtitle="This action cannot be undone" icon="trash" iconColor="red" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Are you sure you want to delete this recurring expense?</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Expense:</span> {{ $expense->description }}</p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Amount:</span> NPR {{
                number_format($expense->amount, 2) }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('deleteExpenseModal_{{ $expense->id }}')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Keep</button>
        <form action="{{ route('admin.finance.recurring-expenses.destroy', $expense->id) }}" method="POST"
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
@endforeach
@endsection