@extends('admin.layouts.app')

@section('title', 'Expense Claims')
@section('page-title', 'Expense Claims')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Expense Claims</h1>
            <p class="text-slate-600 dark:text-slate-400 mt-1">Manage staff expense reimbursement claims</p>
        </div>
        <a href="{{ route('admin.hrm.expense-claims.create') }}"
            class="px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
            New Claim
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-500 dark:text-slate-400">Pending</div>
            <div class="text-2xl font-bold text-yellow-500">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-500 dark:text-slate-400">Approved</div>
            <div class="text-2xl font-bold text-green-500">{{ $stats['approved'] }}</div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-500 dark:text-slate-400">Paid</div>
            <div class="text-2xl font-bold text-blue-500">{{ $stats['paid'] }}</div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-500 dark:text-slate-400">Total Pending</div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white">NPR {{
                number_format($stats['total_pending_amount'], 2) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-lg p-4 md:p-6 border border-slate-200 dark:border-slate-700">
        <form method="GET" action="{{ route('admin.hrm.expense-claims.index') }}"
            class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
                <select name="status"
                    class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved</option>
                    <option value="paid" {{ request('status')==='paid' ? 'selected' : '' }}>Paid</option>
                    <option value="rejected" {{ request('status')==='rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Expense Type</label>
                <select name="expense_type"
                    class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500">
                    <option value="">All Types</option>
                    <option value="travel" {{ request('expense_type')==='travel' ? 'selected' : '' }}>Travel</option>
                    <option value="accommodation" {{ request('expense_type')==='accommodation' ? 'selected' : '' }}>
                        Accommodation</option>
                    <option value="meals" {{ request('expense_type')==='meals' ? 'selected' : '' }}>Meals</option>
                    <option value="transportation" {{ request('expense_type')==='transportation' ? 'selected' : '' }}>
                        Transportation</option>
                    <option value="supplies" {{ request('expense_type')==='supplies' ? 'selected' : '' }}>Supplies
                    </option>
                    <option value="communication" {{ request('expense_type')==='communication' ? 'selected' : '' }}>
                        Communication</option>
                    <option value="other" {{ request('expense_type')==='other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-slate-300 mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500">
            </div>

            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-slate-300 mb-2">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500">
            </div>

            <button type="submit"
                class="px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
                Filter
            </button>
            <a href="{{ route('admin.hrm.expense-claims.index') }}"
                class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
                Reset
            </a>
        </form>
    </div>

    <!-- Claims Table -->
    <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-900 border-b border-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Claim #</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Employee</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Title</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Type</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Amount</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Expense Date</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Payroll</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($claims as $claim)
                    <tr class="hover:bg-slate-700/50">
                        <td class="px-4 py-3 text-sm text-slate-300">{{ $claim->claim_number }}</td>
                        <td class="px-4 py-3 text-sm text-white">{{ $claim->employee->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-white">{{ $claim->title }}</td>
                        <td class="px-4 py-3 text-sm text-slate-300">
                            <span class="capitalize">{{ str_replace('_', ' ', $claim->expense_type) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-white font-semibold">
                            {{ $claim->currency }} {{ number_format($claim->amount, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-300">{{ $claim->expense_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                @if($claim->status === 'paid') bg-blue-500/20 text-blue-400
                                @elseif($claim->status === 'approved') bg-green-500/20 text-green-400
                                @elseif($claim->status === 'pending') bg-yellow-500/20 text-yellow-400
                                @else bg-red-500/20 text-red-400
                                @endif">
                                {{ ucfirst($claim->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($claim->included_in_payroll)
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-500/20 text-blue-400">
                                Included
                            </span>
                            @else
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-slate-500/20 text-slate-400">
                                Pending
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('admin.hrm.expense-claims.show', $claim->id) }}"
                                class="text-lime-400 hover:text-lime-300">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                            No expense claims found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($claims->hasPages())
        <div class="px-4 py-3 border-t border-slate-700">
            {{ $claims->links() }}
        </div>
        @endif
    </div>
</div>
@endsection