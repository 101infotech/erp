@extends('admin.layouts.app')

@section('title', 'Organization Management')
@section('page-title', 'Organization Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-white">Organization Management</h1>
        <p class="text-slate-400 mt-1">Manage companies and departments</p>
    </div>

    <!-- Tabs -->
    <div class="border-b border-slate-700">
        <nav class="flex space-x-8">
            <a href="{{ route('admin.hrm.organization.index', ['tab' => 'companies']) }}"
                class="pb-4 px-1 border-b-2 font-medium text-sm {{ $tab === 'companies' ? 'border-lime-500 text-lime-400' : 'border-transparent text-slate-400 hover:text-slate-300' }}">
                Companies
            </a>
            <a href="{{ route('admin.hrm.organization.index', ['tab' => 'departments']) }}"
                class="pb-4 px-1 border-b-2 font-medium text-sm {{ $tab === 'departments' ? 'border-lime-500 text-lime-400' : 'border-transparent text-slate-400 hover:text-slate-300' }}">
                Departments
            </a>
        </nav>
    </div>

    @if($tab === 'companies')
    <!-- Companies Section -->
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-white">Companies ({{ $companies->count() }})</h2>
            <a href="{{ route('admin.hrm.organization.companies.create') }}"
                class="px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
                Add Company
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($companies as $company)
            <div class="bg-slate-800 rounded-lg p-6 border border-slate-700 hover:border-slate-600 transition">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white">{{ $company->name }}</h3>
                        @if($company->jibble_id)
                        <p class="text-xs text-slate-400 mt-1">Jibble ID: {{ $company->jibble_id }}</p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.hrm.organization.companies.edit', $company->id) }}"
                            class="text-blue-400 hover:text-blue-300 text-sm">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Employees</span>
                        <span class="text-white font-medium">{{ $company->employees_count }}</span>
                    </div>
                    @if($company->address)
                    <div class="text-sm">
                        <span class="text-slate-400">Address:</span>
                        <p class="text-slate-300 mt-1">{{ $company->address }}</p>
                    </div>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-slate-700">
                    <a href="{{ route('admin.hrm.organization.companies.show', $company->id) }}"
                        class="text-lime-400 hover:text-lime-300 text-sm font-medium">
                        View Details →
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-slate-800 rounded-lg p-12 border border-slate-700 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="mt-2 text-slate-400">No companies found</p>
                <a href="{{ route('admin.hrm.organization.companies.create') }}"
                    class="text-lime-400 hover:text-lime-300 mt-2 inline-block">
                    Add your first company
                </a>
            </div>
            @endforelse
        </div>
    </div>
    @else
    <!-- Departments Section -->
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-white">Departments ({{ $departments->count() }})</h2>
            <a href="{{ route('admin.hrm.departments.create') }}"
                class="px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
                Add Department
            </a>
        </div>

        <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                Department Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                Company</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">
                                Employees</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                Jibble ID</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @forelse($departments as $department)
                        <tr class="hover:bg-slate-750">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-white">{{ $department->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                {{ $department->company->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-white">
                                {{ $department->employees_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400">
                                {{ $department->jibble_id ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <a href="{{ route('admin.hrm.departments.show', $department->id) }}"
                                    class="text-lime-400 hover:text-lime-300 font-medium">View</a>
                                <span class="text-slate-600 mx-1">|</span>
                                <a href="{{ route('admin.hrm.departments.edit', $department->id) }}"
                                    class="text-blue-400 hover:text-blue-300 font-medium">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <svg class="mx-auto h-12 w-12 text-slate-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="mt-2">No departments found</p>
                                <a href="{{ route('admin.hrm.departments.create') }}"
                                    class="text-lime-400 hover:text-lime-300 mt-2 inline-block">
                                    Add your first department
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection