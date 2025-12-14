@extends('admin.layouts.app')

@section('title', 'Chart of Accounts')
@section('page-title', 'Chart of Accounts')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Chart of Accounts</h2>
        <a href="{{ route('admin.finance.accounts.create', ['company_id' => $companyId]) }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i> Add Account
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}
    </div>
    @endif

    <div class="mb-4">
        <select id="company_id" onchange="location.href='?company_id='+this.value"
            class="px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
            @foreach($companies as $company)
            <option value="{{ $company->id }}" {{ $companyId==$company->id ? 'selected' : '' }}>{{ $company->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="w-full">
        <table class="w-full table-auto divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Account Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Parent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($accounts as $account)
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ $account->account_code
                        }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">{{ $account->account_name }}</td>
                    <td class="px-6 py-4">
                        <span
                            class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                            {{ ucfirst($account->account_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">{{
                        $account->parentAccount?->account_name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span
                            class="px-2 py-1 text-xs rounded-full {{ $account->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                            {{ $account->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('admin.finance.accounts.edit', $account) }}"
                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Edit</a>
                        <form action="{{ route('admin.finance.accounts.destroy', $account) }}" method="POST"
                            class="inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">No accounts found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $accounts->links() }}</div>
</div>
@endsection