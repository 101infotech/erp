@extends('admin.layouts.app')

@section('title', 'Founders Management')
@section('page-title', 'Founders Management')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Founders</h2>
        <div class="flex gap-2">
            <form action="{{ route('admin.finance.founders.export') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-file-excel mr-2"></i> Export to Excel
                </button>
            </form>
            <a href="{{ route('admin.finance.founders.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i> Add Founder
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.finance.founders.index') }}"
            class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Company</label>
                <select name="company_id"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id')==$company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Status</label>
                <select name="is_active"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active')==='1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active')==='0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Email, Phone..."
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="{{ route('admin.finance.founders.index') }}"
                    class="bg-slate-500 hover:bg-slate-600 text-white px-4 py-2 rounded-lg">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Founders Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Founder</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Company</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-200">Investment
                        Limit</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-200">Total
                        Invested</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-200">Total
                        Withdrawn</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-200">Current
                        Balance</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-slate-700 dark:text-slate-200">Status</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-slate-700 dark:text-slate-200">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                @forelse($founders as $founder)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700">
                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-900 dark:text-white">{{ $founder->name }}</div>
                        <div class="text-sm text-slate-500">{{ $founder->email }}</div>
                        @if($founder->phone)
                        <div class="text-sm text-slate-500">{{ $founder->phone }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                        {{ $founder->company->name ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 text-right text-slate-700 dark:text-slate-300">
                        NPR {{ number_format($founder->investment_limit, 2) }}
                    </td>
                    <td class="px-4 py-3 text-right text-green-600 font-medium">
                        NPR {{ number_format($founder->total_invested, 2) }}
                    </td>
                    <td class="px-4 py-3 text-right text-red-600 font-medium">
                        NPR {{ number_format($founder->total_withdrawn, 2) }}
                    </td>
                    <td
                        class="px-4 py-3 text-right font-bold {{ $founder->current_balance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                        NPR {{ number_format($founder->current_balance, 2) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span
                            class="px-2 py-1 text-xs rounded-full {{ $founder->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $founder->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.finance.founders.show', $founder) }}"
                                class="text-blue-600 hover:text-blue-800" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.finance.founders.edit', $founder) }}"
                                class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.finance.founders.destroy', $founder) }}" method="POST"
                                class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this founder?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-slate-500">
                        No founders found. <a href="{{ route('admin.finance.founders.create') }}"
                            class="text-blue-600 hover:underline">Add your first founder</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $founders->links() }}
    </div>
</div>
@endsection