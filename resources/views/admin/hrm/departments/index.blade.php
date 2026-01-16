@extends('admin.layouts.app')

@section('title', 'Departments')
@section('page-title', 'Departments')

@section('content')
<div class="px-6 md:px-8 py-6 space-y-6">
    <!-- Header -->
    <div>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Departments</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Manage organizational departments</p>
            </div>
            <a href="{{ route('admin.hrm.departments.create') }}"
                class="px-4 py-2 bg-lime-500 text-slate-950 font-semibold rounded-lg hover:bg-lime-400 transition flex items-center space-x-2 w-fit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Department</span>
            </a>
        </div>

        <!-- Filters -->
        <form method="GET"
            class="bg-white/80 dark:bg-slate-800/50 backdrop-blur-sm border border-slate-200 dark:border-slate-700 rounded-lg p-4">
            <div class="flex gap-3">
                <select name="company_id"
                    class="flex-1 px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id')==$company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                    @endforeach
                </select>
                <button type="submit"
                    class="px-6 py-2 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-medium transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Desktop Table View -->
    <div
        class="hidden md:block bg-white/80 dark:bg-slate-800/50 backdrop-blur-sm border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-100 dark:bg-slate-900">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Company
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Employees</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Description</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-slate-800/50 divide-y divide-slate-700">
                    @forelse($departments as $department)
                    <tr class="hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-white">{{ $department->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                            {{ $department->company->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-lime-500/20 text-lime-400">
                                {{ $department->employees_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-400 max-w-xs truncate">
                            {{ Str::limit($department->description, 50) ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.hrm.departments.edit', $department) }}"
                                class="text-lime-400 hover:text-lime-300">Edit</a>
                            <form method="POST" action="{{ route('admin.hrm.departments.destroy', $department) }}"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <p class="mb-2">No departments found</p>
                            <a href="{{ route('admin.hrm.departments.create') }}"
                                class="text-lime-400 hover:text-lime-300">Add your first department</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-4">
        @forelse($departments as $department)
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="text-sm font-semibold text-white">{{ $department->name }}</h3>
                    <p class="text-xs text-slate-400 mt-1">{{ $department->company->name }}</p>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-lime-500/20 text-lime-400">
                    {{ $department->employees_count }} emp
                </span>
            </div>

            @if($department->description)
            <p class="text-sm text-slate-400 mb-3">{{ Str::limit($department->description, 100) }}</p>
            @endif

            <div class="flex gap-2 pt-3 border-t border-slate-700">
                <a href="{{ route('admin.hrm.departments.edit', $department) }}"
                    class="flex-1 px-3 py-1.5 text-xs text-center bg-lime-500/20 text-lime-400 rounded-lg hover:bg-lime-500/30 transition">Edit</a>
                <form method="POST" action="{{ route('admin.hrm.departments.destroy', $department) }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full px-3 py-1.5 text-xs bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition"
                        onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-8 text-center">
            <svg class="w-12 h-12 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <p class="text-slate-400 mb-2">No departments found</p>
            <a href="{{ route('admin.hrm.departments.create') }}" class="text-lime-400 hover:text-lime-300">Add your
                first
                department</a>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $departments->links() }}
    </div>
    @endsection