@extends('admin.layouts.app')

@section('title', 'Company Details')
@section('page-title', 'Company Details')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $company->name }}</h2>
        <div class="space-x-2">
            <a href="{{ route('admin.finance.companies.edit', $company) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
            <a href="{{ route('admin.finance.companies.index') }}"
                class="px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 dark:border-slate-600">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Company Type</h3>
            <p class="mt-1 text-lg">{{ ucfirst($company->company_type) }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Parent Company</h3>
            <p class="mt-1 text-lg">{{ $company->parentCompany?->name ?? 'None' }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">PAN Number</h3>
            <p class="mt-1 text-lg">{{ $company->pan_number ?? '-' }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Contact Email</h3>
            <p class="mt-1 text-lg">{{ $company->contact_email ?? '-' }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Contact Phone</h3>
            <p class="mt-1 text-lg">{{ $company->contact_phone ?? '-' }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Established Date</h3>
            <p class="mt-1 text-lg">{{ $company->established_date_bs ?? '-' }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Fiscal Year Start</h3>
            <p class="mt-1 text-lg">Month {{ $company->fiscal_year_start_month }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Status</h3>
            <p class="mt-1">
                <span
                    class="px-2 py-1 text-sm rounded-full {{ $company->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $company->is_active ? 'Active' : 'Inactive' }}
                </span>
            </p>
        </div>
        <div class="md:col-span-2">
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Address</h3>
            <p class="mt-1 text-lg">{{ $company->address ?? '-' }}</p>
        </div>
    </div>

    <div class="border-t pt-6">
        <h3 class="text-xl font-semibold mb-4">Statistics</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <p class="text-sm text-slate-600 dark:text-slate-400">Transactions</p>
                <p class="text-2xl font-bold">{{ $company->transactions_count ?? 0 }}</p>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <p class="text-sm text-slate-600 dark:text-slate-400">Sales</p>
                <p class="text-2xl font-bold">{{ $company->sales_count ?? 0 }}</p>
            </div>
            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                <p class="text-sm text-slate-600 dark:text-slate-400">Purchases</p>
                <p class="text-2xl font-bold">{{ $company->purchases_count ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>
@endsection