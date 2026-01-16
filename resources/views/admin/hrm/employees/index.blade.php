@extends('admin.layouts.app')

@section('title', 'Employees')
@section('page-title', 'Employees')
@use('App\Constants\Design')

@section('content')
<div class="px-6 md:px-8 py-6 space-y-6">
    <!-- Header -->
    <div class="bg-slate-900 border border-slate-800 rounded-lg p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-semibold text-white">Employee HR Records</h2>
                <p class="text-slate-400 text-sm mt-1">Manage employee information, positions, and departments</p>
                <p class="text-xs text-slate-500 mt-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    For system accounts and Jibble integration, use <a href="{{ route('admin.users.index') }}"
                        class="text-lime-400 hover:text-lime-300 underline">Employees & Accounts</a>
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index') }}"
                    class="px-3 py-1.5 text-sm bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Accounts & Jibble</span>
                </a>
                <a href="{{ route('admin.hrm.employees.create') }}"
                    class="{{ Design::BTN_SMALL_PADDING }} {{ Design::TEXT_SM }} bg-lime-500 text-slate-950 {{ Design::FONT_SEMIBOLD }} rounded-lg hover:bg-lime-400 transition flex items-center {{ Design::GAP_SM }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add Employee</span>
                </a>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET"
            class="bg-white/80 dark:bg-slate-800/50 backdrop-blur-sm border border-slate-200 dark:border-slate-700 rounded-lg p-3">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employees..."
                    class="px-3 py-2 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">

                <select name="company_id"
                    class="px-3 py-2 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id')==$company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                    @endforeach
                </select>

                <select name="status"
                    class="px-3 py-2 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="terminated" {{ request('status')=='terminated' ? 'selected' : '' }}>Terminated
                    </option>
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
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Employee
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Department</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-4 py-2.5 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-slate-800/50 divide-y divide-slate-700">
                    @forelse($employees as $employee)
                    <tr class="hover:bg-slate-700/50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    @if($employee->avatar_url)
                                    <img class="h-10 w-10 rounded-full object-cover border-2 border-slate-600"
                                        src="{{ $employee->avatar_url }}" alt="{{ $employee->name }}">
                                    @else
                                    <div
                                        class="h-10 w-10 rounded-full bg-gradient-to-br from-lime-500 to-lime-600 flex items-center justify-center shadow-lg">
                                        <span class="text-slate-900 font-bold text-base">{{
                                            strtoupper(substr($employee->name
                                            ?? 'U', 0, 1)) }}</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-semibold text-white">{{ $employee->name }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $employee->position ?? 'Employee' }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $employee->company->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-300">
                            {{ $employee->department->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $employee->status === 'active' ? 'bg-lime-500/20 text-lime-400' : '' }}
                            {{ $employee->status === 'inactive' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                            {{ $employee->status === 'terminated' ? 'bg-red-500/20 text-red-400' : '' }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </td>
                        <!-- Jibble sync status removed -->
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.hrm.attendance.employee', $employee) }}"
                                class="text-teal-400 hover:text-teal-300">Timesheet</a>
                            <a href="{{ route('admin.hrm.employees.show', $employee) }}"
                                class="text-blue-400 hover:text-blue-300">View</a>
                            <a href="{{ route('admin.hrm.employees.edit', $employee) }}"
                                class="text-lime-400 hover:text-lime-300">Edit</a>
                            <button type="button" onclick="openDeleteModal('delete-employee-{{ $employee->id }}')"
                                class="text-red-400 hover:text-red-300">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <p class="mb-2 text-sm">No employees found</p>
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
                        class="h-12 w-12 rounded-full bg-gradient-to-br from-lime-500 to-lime-600 flex items-center justify-center shadow-lg">
                        <span class="text-slate-900 font-bold text-lg">{{ strtoupper(substr($employee->name ?? 'U', 0,
                            1))
                            }}</span>
                    </div>
                    @endif
                    <div>
                        <div class="text-sm font-semibold text-white">{{ $employee->name }}</div>
                        <div class="text-xs text-slate-400">{{ $employee->position ?? 'Employee' }}</div>
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
                <button type="button" onclick="openDeleteModal('delete-employee-{{ $employee->id }}')"
                    class="px-3 py-1.5 text-xs bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition">Delete</button>
            </div>
        </div>
        @empty
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6 text-center">
            <svg class="w-10 h-10 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <p class="text-slate-400 mb-2 text-sm">No employees found</p>
            <a href="{{ route('admin.hrm.employees.create') }}" class="text-lime-400 hover:text-lime-300">Add your first
                employee</a>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $employees->links() }}
    </div>

    <!-- Delete Modals -->
    @foreach($employees as $employee)
    <div id="delete-employee-{{ $employee->id }}"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-slate-800 rounded-lg max-w-md w-full p-6 border border-slate-700">
            <div class="flex items-start gap-4 mb-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-white mb-2">Delete Employee</h3>
                    <p class="text-slate-300 text-sm mb-3">Are you sure you want to delete <strong>{{ $employee->name
                            }}</strong>? This action cannot be undone and will remove all associated data.</p>
                    <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700 text-xs">
                        <p class="text-slate-400"><span class="font-semibold">Code:</span> {{ $employee->employee_code
                            }}</p>
                        <p class="text-slate-400"><span class="font-semibold">Position:</span> {{ $employee->position ??
                            'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeDeleteModal('delete-employee-{{ $employee->id }}')"
                    class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
                    Cancel
                </button>
                <form action="{{ route('admin.hrm.employees.destroy', $employee) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                        Delete Employee
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    @push('scripts')
    <script>
        function openDeleteModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function closeDeleteModal(id) {
    document.getElementById(id).classList.add('hidden');
}

// Close on escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id^="delete-employee-"]').forEach(modal => {
            modal.classList.add('hidden');
        });
    }
});

// Close on background click
document.querySelectorAll('[id^="delete-employee-"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal(this.id);
    });
});
    </script>
    @endpush
    @endsection