@extends('admin.layouts.app')

@section('title', 'Account Details')
@section('page-title', 'Account Details')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $account->account_name }}</h2>
        <div class="space-x-2">
            <a href="{{ route('admin.finance.accounts.edit', $account) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
            <a href="{{ route('admin.finance.accounts.index') }}"
                class="px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 dark:border-slate-600">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Account Code</h3>
            <p class="mt-1 text-lg font-mono">{{ $account->account_code }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Account Type</h3>
            <p class="mt-1">
                <span class="px-2 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                    {{ ucfirst($account->account_type) }}
                </span>
            </p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Company</h3>
            <p class="mt-1 text-lg">{{ $account->company->name }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Parent Account</h3>
            <p class="mt-1 text-lg">{{ $account->parentAccount?->account_name ?? 'None (Top Level)' }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Status</h3>
            <p class="mt-1">
                <span
                    class="px-2 py-1 text-sm rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $account->is_active ? 'Active' : 'Inactive' }}
                </span>
            </p>
        </div>
        <div class="md:col-span-2">
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Description</h3>
            <p class="mt-1 text-lg">{{ $account->description ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection