@extends('admin.layouts.app')

@section('title', 'Finance Companies')
@section('page-title', 'Finance Companies')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Companies</h2>
        <a href="{{ route('admin.finance.companies.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i> Add Company
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="w-full">
        <table class="w-full table-auto divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">PAN
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Transactions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($companies as $company)
                <tr>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $company->name }}</div>
                        @if($company->parent_company_id)
                        <div class="text-xs text-slate-500 dark:text-slate-400">Sub of: {{ $company->parentCompany->name
                            }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($company->type === 'holding') bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300
                            @elseif($company->type === 'subsidiary') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300
                            @else bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-300 @endif">
                            {{ ucfirst($company->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                        {{ $company->pan_number ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                        <div>{{ $company->contact_email ?? 'N/A' }}</div>
                        <div class="text-xs">{{ $company->contact_phone ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                        {{ $company->transactions_count + $company->sales_count + $company->purchases_count }}
                    </td>
                    <td class="px-6 py-4">
                        @if($company->is_active)
                        <span
                            class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Active</span>
                        @else
                        <span
                            class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <a href="{{ route('admin.finance.companies.show', $company) }}"
                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">View</a>
                        <a href="{{ route('admin.finance.companies.edit', $company) }}"
                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Edit</a>
                        <form action="{{ route('admin.finance.companies.destroy', $company) }}" method="POST"
                            class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">
                        No companies found. <a href="{{ route('admin.finance.companies.create') }}"
                            class="text-blue-600">Create one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $companies->links() }}
    </div>
</div>
@endsection