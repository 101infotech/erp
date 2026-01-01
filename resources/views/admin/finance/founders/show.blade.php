@extends('admin.layouts.app')

@section('title', 'Founder Details')
@section('page-title', 'Founder Details')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.finance.founders.index') }}" class="group">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-white">{{ $founder->name }}</h2>
    </div>

    <!-- Founder Info Card -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <p class="text-slate-400 mt-1">{{ $founder->email }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.finance.founders.edit', $founder) }}"
                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('admin.finance.founders.index') }}"
                    class="bg-slate-500 hover:bg-slate-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="text-sm text-blue-600 dark:text-blue-400 mb-1">Investment Limit</div>
                <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                    NPR {{ number_format($founder->investment_limit, 2) }}
                </div>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="text-sm text-green-600 dark:text-green-400 mb-1">Total Invested</div>
                <div class="text-2xl font-bold text-green-700 dark:text-green-300">
                    NPR {{ number_format($founder->total_invested, 2) }}
                </div>
            </div>

            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                <div class="text-sm text-red-600 dark:text-red-400 mb-1">Total Withdrawn</div>
                <div class="text-2xl font-bold text-red-700 dark:text-red-300">
                    NPR {{ number_format($founder->total_withdrawn, 2) }}
                </div>
            </div>

            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                <div class="text-sm text-purple-600 dark:text-purple-400 mb-1">Current Balance</div>
                <div
                    class="text-2xl font-bold {{ $founder->current_balance >= 0 ? 'text-purple-700 dark:text-purple-300' : 'text-red-700 dark:text-red-300' }}">
                    NPR {{ number_format($founder->current_balance, 2) }}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="text-sm text-slate-600 dark:text-slate-400">Company</div>
                <div class="font-medium text-slate-900 dark:text-white">{{ $founder->company->name ?? 'N/A' }}</div>
            </div>

            <div>
                <div class="text-sm text-slate-600 dark:text-slate-400">Phone</div>
                <div class="font-medium text-slate-900 dark:text-white">{{ $founder->phone ?? 'N/A' }}</div>
            </div>

            <div>
                <div class="text-sm text-slate-600 dark:text-slate-400">PAN Number</div>
                <div class="font-medium text-slate-900 dark:text-white">{{ $founder->pan_number ?? 'N/A' }}</div>
            </div>

            <div>
                <div class="text-sm text-slate-600 dark:text-slate-400">Status</div>
                <span
                    class="px-2 py-1 text-xs rounded-full {{ $founder->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $founder->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            @if($founder->notes)
            <div class="md:col-span-2">
                <div class="text-sm text-slate-600 dark:text-slate-400">Notes</div>
                <div class="font-medium text-slate-900 dark:text-white">{{ $founder->notes }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Transaction History -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white">Transaction History</h3>
            <a href="{{ route('admin.finance.founder-transactions.create', ['founder_id' => $founder->id]) }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i> New Transaction
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">
                            Transaction #</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Date (BS)
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Type</th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-200">Amount
                        </th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-slate-700 dark:text-slate-200">Status
                        </th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-slate-700 dark:text-slate-200">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                    @forelse($founder->transactions()->latest()->get() as $transaction)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700">
                        <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $transaction->transaction_number }}
                        </td>
                        <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $transaction->transaction_date_bs }}
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-1 text-xs rounded-full {{ $transaction->transaction_type == 'investment' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($transaction->transaction_type) }}
                            </span>
                        </td>
                        <td
                            class="px-4 py-3 text-right font-medium {{ $transaction->transaction_type == 'investment' ? 'text-green-600' : 'text-red-600' }}">
                            NPR {{ number_format($transaction->amount, 2) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $transaction->status == 'settled' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $transaction->status == 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $transaction->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $transaction->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.finance.founder-transactions.show', $transaction) }}"
                                class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                            No transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection