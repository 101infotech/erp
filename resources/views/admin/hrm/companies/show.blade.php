@extends('admin.layouts.app')

@section('title', 'Company Details')
@section('page-title', 'Company Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-white">{{ $company->name }}</h1>
            <p class="text-slate-400 mt-1">{{ $company->employees_count }} Employees • {{ $company->departments_count }}
                Departments</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.hrm.companies.edit', $company) }}"
                class="px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Company
            </a>
            <a href="{{ route('admin.hrm.companies.index') }}"
                class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
                Back
            </a>
        </div>
    </div>

    <!-- Company Information -->
    <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Company Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-slate-400 text-sm mb-1">Company Name</label>
                <p class="text-white font-medium">{{ $company->name }}</p>
            </div>
            <div>
                <label class="block text-slate-400 text-sm mb-1">Finance Company</label>
                <p class="text-white font-medium">
                    @if($company->financeCompany)
                    <a class="text-lime-400 hover:text-lime-300 underline"
                        href="{{ route('admin.finance.companies.show', $company->financeCompany) }}">{{
                        $company->financeCompany->name }}</a>
                    @else
                    Not linked
                    @endif
                </p>
            </div>
            <div>
                <label class="block text-slate-400 text-sm mb-1">Contact Email</label>
                <p class="text-white font-medium">{{ $company->contact_email ?? 'N/A' }}</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-slate-400 text-sm mb-1">Address</label>
                <p class="text-white font-medium">{{ $company->address ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-slate-400 text-sm mb-1">Created At</label>
                <p class="text-white font-medium">{{ $company->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <div>
                <label class="block text-slate-400 text-sm mb-1">Last Updated</label>
                <p class="text-white font-medium">{{ $company->updated_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Departments -->
    <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">Departments ({{ $company->departments_count }})</h3>
            <a href="{{ route('admin.hrm.departments.create') }}"
                class="px-3 py-1.5 bg-lime-500 hover:bg-lime-600 text-slate-900 text-sm font-semibold rounded transition">
                Add Department
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-700">
                <thead>
                    <tr class="bg-slate-900">
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase">Department Name
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase">Employees</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($company->departments as $department)
                    <tr class="hover:bg-slate-700/50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-white">{{ $department->name }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-300">{{ $department->employees_count
                            ?? 0 }}</td>
                        <td class="px-4 py-3 text-sm text-slate-400">{{ $department->description ?? 'N/A' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                            <a href="{{ route('admin.hrm.departments.edit', $department) }}"
                                class="text-lime-400 hover:text-lime-300 font-medium">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">
                            No departments found. <a href="{{ route('admin.hrm.departments.create') }}"
                                class="text-lime-400 hover:text-lime-300">Add a department</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Employees -->
    <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">Employees ({{ $company->employees_count }})</h3>
            <a href="{{ route('admin.hrm.employees.create') }}"
                class="px-3 py-1.5 bg-lime-500 hover:bg-lime-600 text-slate-900 text-sm font-semibold rounded transition">
                Add Employee
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-700">
                <thead>
                    <tr class="bg-slate-900">
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase">Employee Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase">Position</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($company->employees as $employee)
                    <tr class="hover:bg-slate-700/50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-white">{{ $employee->name }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-300">{{ $employee->email ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-300">{{ $employee->department->name ??
                            'N/A' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-300">{{ $employee->position ?? 'N/A'
                            }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            @if($employee->status === 'active')
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-400">Active</span>
                            @else
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full bg-red-500/20 text-red-400">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                            <a href="{{ route('admin.hrm.employees.show', $employee) }}"
                                class="text-lime-400 hover:text-lime-300 font-medium">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400">
                            No employees found. <a href="{{ route('admin.hrm.employees.create') }}"
                                class="text-lime-400 hover:text-lime-300">Add an employee</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-red-400 mb-2">Danger Zone</h3>
        <p class="text-slate-400 text-sm mb-4">Deleting this company will also delete all associated departments and
            employees. This action cannot be undone.</p>
        <button type="button" onclick="openModal('deleteCompanyShowModal')"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">Delete
            Company</button>
    </div>
</div>

<!-- Delete Company Modal -->
<x-professional-modal id="deleteCompanyShowModal" title="Delete Company" subtitle="This action cannot be undone"
    icon="trash" iconColor="red" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Are you absolutely sure? This will permanently delete the company, all its
            departments, and all its employees.</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Company:</span> {{ $company->name }}</p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Location:</span> {{ $company->location ??
                'N/A' }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('deleteCompanyShowModal')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Cancel</button>
        <form method="POST" action="{{ route('admin.hrm.companies.destroy', $company) }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete Permanently
            </button>
        </form>
    </x-slot>
</x-professional-modal>
@endsection