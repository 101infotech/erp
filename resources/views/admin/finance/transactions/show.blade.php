@extends('admin.layouts.app')

@section('title', 'Transaction Details')
@section('page-title', 'Transaction Details')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $transaction->transaction_number }}</h2>
        <div class="space-x-2">
            <a href="{{ route('admin.finance.transactions.edit', $transaction) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
            <a href="{{ route('admin.finance.transactions.index') }}"
                class="px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 dark:border-slate-600">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Company</h3>
            <p class="mt-1 text-lg">{{ $transaction->company->name }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Transaction Date (BS)</h3>
            <p class="mt-1 text-lg">{{ $transaction->transaction_date_bs }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Transaction Type</h3>
            <p class="mt-1">
                <span
                    class="px-2 py-1 text-sm rounded-full 
                    {{ $transaction->transaction_type === 'income' ? 'bg-green-100 text-green-800' : 
                       ($transaction->transaction_type === 'expense' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ ucfirst($transaction->transaction_type) }}
                </span>
            </p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Amount</h3>
            <p class="mt-1 text-lg font-semibold">NPR {{ number_format($transaction->amount, 2) }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Payment Method</h3>
            <p class="mt-1 text-lg">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Category</h3>
            <p class="mt-1 text-lg">{{ $transaction->category?->name ?? '-' }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Fiscal Year/Month</h3>
            <p class="mt-1 text-lg">{{ $transaction->fiscal_year_bs }} / {{ $transaction->fiscal_month_bs }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Payment Reference</h3>
            <p class="mt-1 text-lg">{{ $transaction->payment_reference ?? '-' }}</p>
        </div>
        <div class="md:col-span-2">
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Description</h3>
            <p class="mt-1 text-lg">{{ $transaction->description }}</p>
        </div>
        <div class="md:col-span-2">
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Notes</h3>
            <p class="mt-1 text-lg">{{ $transaction->notes ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection