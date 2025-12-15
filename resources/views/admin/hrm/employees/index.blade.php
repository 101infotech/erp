@extends('admin.layouts.app')

@section('title', 'Employees')
@section('page-title', 'Employees')

@section('content')
<!-- Header -->
<div class="mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
        <div>
            <h2 class="text-2xl font-bold text-white">Employees</h2>
            <p class="text-slate-400 mt-1">Manage your team members and their information</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <form method="POST" action="{{ route('admin.hrm.employees.sync-from-jibble') }}">
                @csrf
                <button type="submit"
                    class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Sync</span>
                </button>
            </form>
            <a href="{{ route('admin.hrm.employees.create') }}"
                class="px-3 py-1.5 text-sm bg-lime-500 text-slate-950 font-semibold rounded-lg hover:bg-lime-400 transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Employee</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employees..."
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">

            <select name="company_id"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                <option value="">All Companies</option>
                @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ request('company_id')==$company->id ? 'selected' : '' }}>
                    {{ $company->name }}
                </option>
                @endforeach
            </select>

            <select name="status"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="terminated" {{ request('status')=='terminated' ? 'selected' : '' }}>Terminated</option>
            </select>

            <button type="submit"
                class="px-4 py-2 text-sm bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-medium transition">
                Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Desktop Table View -->
<div class="hidden lg:block bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-700">
            <thead class="bg-slate-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Employee
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                        Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Jibble
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-slate-800/50 divide-y divide-slate-700">
                @forelse($employees as $employee)
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-12 w-12 flex-shrink-0">
                                @if($employee->avatar_url)
                                <img class="h-12 w-12 rounded-full object-cover border-2 border-slate-600"
                                    src="{{ $employee->avatar_url }}" alt="{{ $employee->name }}">
                                @else
                                <div
                                    class="h-12 w-12 rounded-full bg-gradient-to-br from-lime-500 to-lime-600 flex items-center justify-center shadow-lg">
                                    <span class="text-slate-900 font-bold text-lg">{{ strtoupper(substr($employee->name
                                        ?? 'U', 0, 1)) }}</span>
                                </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-base font-semibold text-white">{{ $employee->name }}</div>
                                <div class="text-sm text-slate-400 mt-0.5">{{ $employee->position ?? 'Employee' }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $employee->company->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                        {{ $employee->department->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $employee->status === 'active' ? 'bg-lime-500/20 text-lime-400' : '' }}
                            {{ $employee->status === 'inactive' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                            {{ $employee->status === 'terminated' ? 'bg-red-500/20 text-red-400' : '' }}">
                            {{ ucfirst($employee->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($employee->jibble_person_id)
                        <span
                            class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-500/20 text-blue-400 flex items-center space-x-1 w-fit">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Synced</span>
                        </span>
                        @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-700 text-slate-400">Not
                            Synced</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <a href="{{ route('admin.hrm.attendance.employee', $employee) }}"
                            class="text-teal-400 hover:text-teal-300">Timesheet</a>
                        <a href="{{ route('admin.hrm.employees.show', $employee) }}"
                            class="text-blue-400 hover:text-blue-300">View</a>
                        <a href="{{ route('admin.hrm.employees.edit', $employee) }}"
                            class="text-lime-400 hover:text-lime-300">Edit</a>
                        <form method="POST" action="{{ route('admin.hrm.employees.destroy', $employee) }}"
                            class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="text-red-400 hover:text-red-300"
                                onclick="deleteEmployee('{{ route('admin.hrm.employees.destroy', $employee->id) }}')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="mb-2">No employees found</p>
                        <a href="{{ route('admin.hrm.employees.create') }}"
                            class="text-lime-400 hover:text-lime-300">Add your first employee</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Card View -->
<div class="lg:hidden space-y-4">
    @forelse($employees as $employee)
    <div
        class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4 hover:border-slate-600 transition">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center space-x-3">
                @if($employee->avatar_url)
                <img class="h-14 w-14 rounded-full object-cover border-2 border-slate-600"
                    src="{{ $employee->avatar_url }}" alt="{{ $employee->name }}">
                @else
                <div
                    class="h-14 w-14 rounded-full bg-gradient-to-br from-lime-500 to-lime-600 flex items-center justify-center shadow-lg">
                    <span class="text-slate-900 font-bold text-xl">{{ strtoupper(substr($employee->name ?? 'U', 0, 1))
                        }}</span>
                </div>
                @endif
                <div>
                    <div class="text-base font-semibold text-white">{{ $employee->name }}</div>
                    <div class="text-sm text-slate-400">{{ $employee->position ?? 'Employee' }}</div>
                </div>
            </div>
            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                {{ $employee->status === 'active' ? 'bg-lime-500/20 text-lime-400' : '' }}
                {{ $employee->status === 'inactive' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                {{ $employee->status === 'terminated' ? 'bg-red-500/20 text-red-400' : '' }}">
                {{ ucfirst($employee->status) }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-4 text-sm">
            <div>
                <span class="text-slate-400">Company:</span>
                <span class="text-white ml-1">{{ $employee->company->name }}</span>
            </div>
            <div>
                <span class="text-slate-400">Department:</span>
                <span class="text-white ml-1">{{ $employee->department->name ?? 'N/A' }}</span>
            </div>
            @if($employee->email)
            <div class="col-span-2">
                <span class="text-slate-400">Email:</span>
                <span class="text-white ml-1 text-xs">{{ $employee->email }}</span>
            </div>
            @endif
        </div>

        <div class="flex flex-wrap gap-2 pt-3 border-t border-slate-700">
            <a href="{{ route('admin.hrm.attendance.employee', $employee) }}"
                class="px-3 py-1.5 text-xs bg-teal-500/20 text-teal-400 rounded-lg hover:bg-teal-500/30 transition">Timesheet</a>
            <a href="{{ route('admin.hrm.employees.show', $employee) }}"
                class="px-3 py-1.5 text-xs bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/30 transition">View</a>
            <a href="{{ route('admin.hrm.employees.edit', $employee) }}"
                class="px-3 py-1.5 text-xs bg-lime-500/20 text-lime-400 rounded-lg hover:bg-lime-500/30 transition">Edit</a>
            <form method="POST" action="{{ route('admin.hrm.employees.destroy', $employee) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="button"
                    class="px-3 py-1.5 text-xs bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition"
                    onclick="deleteEmployee('{{ route('admin.hrm.employees.destroy', $employee->id) }}')">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-8 text-center">
        <svg class="w-12 h-12 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <p class="text-slate-400 mb-2">No employees found</p>
        <a href="{{ route('admin.hrm.employees.create') }}" class="text-lime-400 hover:text-lime-300">Add your first
            employee</a>
    </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $employees->links() }}
</div>

<!-- Delete Employee Confirmation Dialog -->
<x-confirm-dialog name="delete-employee" title="Delete Employee"
    message="Are you sure you want to delete this employee? This action cannot be undone." type="danger"
    confirmText="Delete Employee" form="deleteEmployeeForm" />

<form id="deleteEmployeeForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function deleteEmployee(url) {
        document.getElementById('deleteEmployeeForm').action = url;
        window.dispatchEvent(new CustomEvent('open-confirm', { detail: 'delete-employee' }));
    }
</script>
@endsection