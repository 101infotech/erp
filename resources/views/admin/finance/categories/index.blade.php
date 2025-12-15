@extends('admin.layouts.app')

@section('title', 'Finance Categories')
@section('page-title', 'Finance Categories')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Finance Categories</h2>
        <a href="{{ route('admin.finance.categories.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i> Add Category
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <!-- Filters -->
    <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Company</label>
                <select name="company_id"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id')==$company->id ? 'selected' : '' }}>{{
                        $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Type</label>
                <select name="type" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                    <option value="">All Types</option>
                    <option value="income" {{ request('type')=='income' ? 'selected' : '' }}>Income</option>
                    <option value="expense" {{ request('type')=='expense' ? 'selected' : '' }}>Expense</option>
                    <option value="asset" {{ request('type')=='asset' ? 'selected' : '' }}>Asset</option>
                    <option value="liability" {{ request('type')=='liability' ? 'selected' : '' }}>Liability</option>
                    <option value="equity" {{ request('type')=='equity' ? 'selected' : '' }}>Equity</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"><i
                        class="fas fa-search mr-2"></i> Filter</button>
                <a href="{{ route('admin.finance.categories.index') }}"
                    class="bg-slate-500 hover:bg-slate-600 text-white px-4 py-2 rounded-lg">Reset</a>
            </div>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Name</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Code</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Type</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Parent</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Company</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-slate-700 dark:text-slate-200">Status</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-slate-700 dark:text-slate-200">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                @forelse($categories as $category)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700">
                    <td class="px-4 py-3 text-slate-900 dark:text-white font-medium">{{ $category->name }}</td>
                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $category->code ?? 'N/A' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $category->type == 'income' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $category->type == 'expense' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $category->type == 'asset' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $category->type == 'liability' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $category->type == 'equity' ? 'bg-purple-100 text-purple-800' : '' }}">
                            {{ ucfirst($category->type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $category->parentCategory->name ?? '-'
                        }}</td>
                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $category->company->name ?? 'Global' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span
                            class="px-2 py-1 text-xs rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.finance.categories.edit', $category) }}"
                                class="text-yellow-600 hover:text-yellow-800"><i class="fas fa-edit"></i></a>
                            <button type="button" onclick="openModal('deleteCategoryModal_{{ $category->id }}')"
                                class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">No categories found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $categories->links() }}</div>
</div>
@foreach($categories as $category)
<!-- Delete Category Modal -->
<x-professional-modal id="deleteCategoryModal_{{ $category->id }}" title="Delete Category"
    subtitle="This action cannot be undone" icon="trash" iconColor="red" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Are you sure you want to delete this category?</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Category:</span> {{ $category->name }}</p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Status:</span> {{ $category->is_active ?
                'Active' : 'Inactive' }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('deleteCategoryModal_{{ $category->id }}')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Keep</button>
        <form action="{{ route('admin.finance.categories.destroy', $category) }}" method="POST" class="inline">
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