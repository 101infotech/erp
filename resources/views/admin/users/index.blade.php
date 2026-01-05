@extends('admin.layouts.app')

@section('title', 'Employees & Accounts')
@section('page-title', 'Employees & Accounts Management')

@push('styles')
<style>
    #setPasswordModal {
        transition: opacity 0.2s ease-in-out;
        opacity: 0;
    }

    #setPasswordModal.show {
        opacity: 1;
    }

    [id^="password-menu-"] {
        animation: slideDown 0.2s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Custom scrollbar for table */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: rgb(30 41 59);
        border-radius: 4px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: rgb(71 85 105);
        border-radius: 4px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: rgb(100 116 139);
    }
</style>
@endpush

@section('content')
    <div class="space-y-4">
    <!-- Include Confirmation Modal -->
    @include('components.confirm-modal')

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white">Employees & Accounts</h2>
            <p class="text-slate-400 mt-1">Manage employee records, system accounts, and Jibble integration in one place
            </p>
        </div>
        <div class="flex flex-wrap gap-2 justify-end">
            <a href="{{ route('admin.hrm.attendance.index') }}"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-700 text-white rounded-lg text-sm font-medium hover:bg-slate-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Attendance Records</span>
            </a>
            <a href="{{ route('admin.hrm.attendance.sync-employees') }}"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-lime-500 text-slate-950 rounded-lg text-sm font-medium hover:bg-lime-400 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582M20 20v-5h-.581M5 9a7 7 0 0114 0M5 9H4m15 0h1M5 15a7 7 0 0114 0M5 15H4m15 0h1" />
                </svg>
                <span>Sync from Jibble</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">

            <select name="status"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="suspended" {{ request('status')=='suspended' ? 'selected' : '' }}>Suspended</option>
            </select>

            <select name="role"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role')=='admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ request('role')=='user' ? 'selected' : '' }}>User</option>
            </select>

            <select name="company_id"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                <option value="">All Companies</option>
                @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ request('company_id')==$company->id ? 'selected' : '' }}>
                    {{ $company->name }}
                </option>
                @endforeach
            </select>

            <button type="submit"
                class="px-4 py-2 text-sm bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-medium transition">
                Apply Filters
            </button>
        </div>
    </form>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Employee
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Company / Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Account
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-slate-800/50 divide-y divide-slate-700">
                    @forelse($users as $employee)
                    @php
                    $user = $employee->user; // Get the related user account if exists
                    $displayName = $user ? $user->name : $employee->name;
                    $displayEmail = $user ? $user->email : $employee->email;
                    @endphp
                    <tr class="hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    @if($employee->avatar_url)
                                    <img class="h-12 w-12 rounded-full object-cover border-2 border-slate-600"
                                        src="{{ $employee->avatar_url }}" alt="{{ $displayName }}">
                                    @else
                                    <div
                                        class="h-12 w-12 rounded-full bg-gradient-to-br from-lime-500 to-lime-600 flex items-center justify-center shadow-lg">
                                        <span class="text-slate-900 font-bold text-lg">{{
                                            strtoupper(substr($displayName,
                                            0, 1)) }}</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-white">{{ $displayName }}</div>
                                    @if($displayEmail)
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $displayEmail }}</div>
                                    @else
                                    <div class="text-xs text-orange-400 mt-0.5">⚠️ No email</div>
                                    @endif
                                    @if($employee->position)
                                    <div class="text-xs text-slate-500 mt-1">{{ $employee->position }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-white">{{ $employee->company->name ?? 'N/A' }}</div>
                            <div class="text-xs text-slate-400">{{ $employee->department->name ?? 'No Department' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user)
                            <div class="flex flex-col gap-1">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-blue-500/20 text-blue-400' : 'bg-teal-500/20 text-teal-400' }}">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $user->role === 'admin' ? 'Admin Access' : 'User Access' }}
                                </span>
                            </div>
                            @else
                            <div class="flex flex-col gap-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-700 text-slate-400">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    No Login Account
                                </span>
                                <a href="{{ route('admin.users.create-for-employee', $employee) }}"
                                    class="px-2 py-1 text-xs font-semibold rounded-full bg-lime-500/20 text-lime-400 hover:bg-lime-500/30 transition text-center">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Create Account
                                </a>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <!-- View Employee Profile Button -->
                                @if($user)
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium bg-transparent text-slate-300 rounded-lg hover:bg-slate-700/50 hover:text-white transition border border-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>View</span>
                                </a>
                                @else
                                <a href="{{ route('admin.hrm.employees.show', $employee) }}"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium bg-transparent text-slate-300 rounded-lg hover:bg-slate-700/50 hover:text-white transition border border-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>View</span>
                                </a>
                                @endif

                                @if($employee->jibble_person_id)
                                <!-- Jibble Actions Dropdown -->
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false"
                                        class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium bg-slate-700/50 text-slate-300 rounded-lg hover:bg-slate-700 transition border border-slate-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        <span>Jibble</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="open" x-transition
                                        class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-slate-800 border border-slate-700 z-10">
                                        <div class="py-1">
                                            @if(!$user)
                                            <!-- Link to User Account -->
                                            <a href="{{ route('admin.employees.link-jibble-form', $employee) }}"
                                                class="flex items-center gap-2 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                </svg>
                                                Link to User
                                            </a>
                                            @else
                                            <!-- Unlink from User Account -->
                                            <form action="{{ route('admin.employees.unlink-jibble', $employee) }}"
                                                method="POST" id="unlink-form-{{ $employee->id }}">
                                                @csrf
                                                <button type="button"
                                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-yellow-400 hover:bg-slate-700 hover:text-yellow-300 transition text-left"
                                                    onclick="confirmAction({title: 'Unlink User Account?', message: 'Are you sure you want to unlink this employee from their user account? The user account will remain but will no longer be linked to this employee.', type: 'warning', confirmText: 'Yes, Unlink', onConfirm: () => document.getElementById('unlink-form-{{ $employee->id }}').submit()})">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                                                    </svg>
                                                    Unlink User
                                                </button>
                                            </form>
                                            @endif

                                            <div class="border-t border-slate-700 my-1"></div>

                                            <!-- Delete Jibble Employee -->
                                            <form action="{{ route('admin.employees.delete-jibble', $employee) }}"
                                                method="POST" id="delete-jibble-form-{{ $employee->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:bg-red-500/20 hover:text-red-300 transition text-left"
                                                    onclick="confirmAction({title: 'Delete Jibble Employee?', message: 'Are you sure you want to delete this Jibble employee?&lt;br&gt;&lt;br&gt;&lt;strong&gt;Warning:&lt;/strong&gt; This will also delete all attendance records. This action cannot be undone.', type: 'danger', confirmText: 'Yes, Delete', onConfirm: () => document.getElementById('delete-jibble-form-{{ $employee->id }}').submit()})">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete Employee
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($user)
                                <!-- Delete User Account Button -->
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                    id="delete-user-form-{{ $user->id }}" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="inline-flex items-center justify-center p-2 text-sm font-medium bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition border border-red-500/50"
                                        onclick="confirmAction({
                                            title: 'Delete User & Employee?',
                                            message: 'Are you sure you want to delete <strong>{{ $user->name }}</strong>?<br><br>This will permanently delete:<br>• User login account<br>• Employee record<br>• All attendance data<br><br><strong class=\'text-red-400\'>This action cannot be undone!</strong>',
                                            type: 'danger',
                                            confirmText: 'Yes, Delete Everything',
                                            onConfirm: () => document.getElementById('delete-user-form-{{ $user->id }}').submit()
                                        })">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <p>No users found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-4">
        @forelse($users as $user)
        @php
        $employee = $user->hrmEmployee;
        @endphp
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 h-14 w-14">
                        @if($employee && $employee->avatar_url)
                        <img class="h-14 w-14 rounded-full object-cover border-2 border-slate-600"
                            src="{{ $employee->avatar_url }}" alt="{{ $user->name }}">
                        @else
                        <div
                            class="h-14 w-14 rounded-full bg-gradient-to-br from-lime-500 to-lime-600 flex items-center justify-center shadow-lg">
                            <span class="text-slate-900 font-bold text-xl">{{ strtoupper(substr($user->name, 0, 1))
                                }}</span>
                        </div>
                        @endif
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-white">{{ $user->name }}</div>
                        <div class="text-xs text-slate-400">{{ $user->email }}</div>
                        @if($employee && $employee->position)
                        <div class="text-xs text-slate-500 mt-1">{{ $employee->position }}</div>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full 
                        {{ ($user->status ?? 'active') === 'active' ? 'bg-lime-500/20 text-lime-400' : '' }}
                        {{ ($user->status ?? 'active') === 'inactive' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                        {{ ($user->status ?? 'active') === 'suspended' ? 'bg-red-500/20 text-red-400' : '' }}">
                        {{ ucfirst($user->status ?? 'active') }}
                    </span>
                    <span
                        class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-blue-500/20 text-blue-400' : 'bg-slate-700 text-slate-300' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3 text-sm">
                <div>
                    <span class="text-slate-400">Company:</span>
                    @if($employee && $employee->company)
                    <span class="text-white ml-1">{{ $employee->company->name }}</span>
                    @else
                    <span class="text-slate-500 ml-1 italic">N/A</span>
                    @endif
                </div>
                <div>
                    <span class="text-slate-400">Dept:</span>
                    @if($employee && $employee->department)
                    <span class="text-white ml-1">{{ $employee->department->name }}</span>
                    @else
                    <span class="text-slate-500 ml-1 italic">N/A</span>
                    @endif
                </div>
                <div class="col-span-2">
                    <span class="text-slate-400">Joined:</span>
                    <span class="text-white ml-1">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 pt-3 border-t border-slate-700">
                @if($employee)
                <a href="{{ route('admin.hrm.employees.show', $employee) }}"
                    class="px-3 py-1.5 text-xs bg-teal-500/20 text-teal-400 rounded-lg hover:bg-teal-500/30 transition">
                    Profile
                </a>
                <a href="{{ route('admin.hrm.attendance.employee', $employee) }}"
                    class="px-3 py-1.5 text-xs bg-purple-500/20 text-purple-400 rounded-lg hover:bg-purple-500/30 transition">
                    Timesheet
                </a>
                @endif
                <a href="{{ route('admin.users.show', $user) }}"
                    class="px-3 py-1.5 text-xs bg-lime-500/20 text-lime-400 rounded-lg hover:bg-lime-500/30 transition">
                    View
                </a>
                <a href="{{ route('admin.users.edit', $user) }}"
                    class="px-3 py-1.5 text-xs bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/30 transition">
                    Edit
                </a>

                <!-- Password Options Button for Mobile -->
                <div class="relative">
                    <button type="button" onclick="togglePasswordMenu('password-menu-mobile-{{ $user->id }}')"
                        class="px-3 py-1.5 text-xs bg-yellow-500/10 text-yellow-400 rounded-lg hover:bg-yellow-500/20 transition font-medium flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Password
                    </button>

                    <!-- Password Menu Dropdown for Mobile -->
                    <div id="password-menu-mobile-{{ $user->id }}"
                        class="hidden absolute left-0 bottom-full mb-2 w-64 bg-slate-800 border border-slate-700 rounded-lg shadow-xl z-50 overflow-hidden">
                        <button type="button"
                            onclick="openSetPasswordModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')"
                            class="w-full text-left px-4 py-3 text-sm hover:bg-slate-700 transition border-b border-slate-700">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-lime-500/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-lime-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-white text-xs">Set New Password</div>
                                    <div class="text-xs text-slate-400">Manually set password</div>
                                </div>
                            </div>
                        </button>
                        <form action="{{ route('admin.users.send-reset-link', $user) }}" method="POST"
                            id="reset-link-form-mobile-{{ $user->id }}">
                            @csrf
                            <button type="button"
                                class="w-full text-left px-4 py-3 text-sm hover:bg-slate-700 transition border-b border-slate-700"
                                onclick="confirmAction({title: 'Send Password Reset Link?', message: 'Send a password reset link to &lt;strong&gt;{{ $user->email }}&lt;/strong&gt;?&lt;br&gt;&lt;br&gt;The user will receive an email with a secure link to reset their password.', type: 'info', confirmText: 'Yes, Send Link', onConfirm: () => document.getElementById('reset-link-form-mobile-{{ $user->id }}').submit()})">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex-shrink-0 w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-white text-xs">Send Reset Link</div>
                                        <div class="text-xs text-slate-400">User sets own password</div>
                                    </div>
                                </div>
                            </button>
                        </form>
                        <form action="{{ route('admin.users.reset-password', $user) }}" method="POST"
                            id="random-password-form-mobile-{{ $user->id }}">
                            @csrf
                            <button type="button"
                                class="w-full text-left px-4 py-3 text-sm hover:bg-slate-700 transition"
                                onclick="confirmAction({title: 'Generate Random Password?', message: 'Generate a random secure password and email it to &lt;strong&gt;{{ $user->email }}&lt;/strong&gt;?&lt;br&gt;&lt;br&gt;A new random password will be created and sent to the user immediately.', type: 'warning', confirmText: 'Yes, Generate & Send', onConfirm: () => document.getElementById('random-password-form-mobile-{{ $user->id }}').submit()})">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex-shrink-0 w-8 h-8 bg-yellow-500/10 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-white text-xs">Generate & Email</div>
                                        <div class="text-xs text-slate-400">Auto-generate password</div>
                                    </div>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>

                @if($user->id !== Auth::id())
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block"
                    id="delete-user-mobile-form-{{ $user->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                        class="px-3 py-1.5 text-xs bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition"
                        onclick="confirmAction({title: 'Delete User & Employee?', message: 'Are you sure you want to delete &lt;strong&gt;{{ $user->name }}&lt;/strong&gt;?&lt;br&gt;&lt;br&gt;This will permanently delete:&lt;br&gt;• User login account&lt;br&gt;• Employee record&lt;br&gt;• All attendance data&lt;br&gt;&lt;br&gt;&lt;strong&gt;This action cannot be undone!&lt;/strong&gt;', type: 'danger', confirmText: 'Yes, Delete Everything', onConfirm: () => document.getElementById('delete-user-mobile-form-{{ $user->id }}').submit()})">
                        Delete
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-8 text-center">
            <svg class="w-12 h-12 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <p class="text-slate-400">No users found</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>

<!-- Set Password Modal -->
<div id="setPasswordModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50" style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div
            class="bg-slate-800 border border-slate-700 rounded-xl shadow-2xl max-w-md w-full transform transition-all">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700 bg-slate-900/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-lime-500/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Set New Password</h3>
                </div>
                <button onclick="closeSetPasswordModal()" class="text-slate-400 hover:text-white transition"
                    type="button">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="setPasswordForm" method="POST" class="p-6 space-y-5">
                @csrf

                <!-- User Info -->
                <div class="bg-slate-900/50 border border-slate-700 rounded-lg p-3">
                    <p class="text-xs text-slate-400 mb-1">Setting password for:</p>
                    <p id="modalUserName" class="text-sm text-white font-medium"></p>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                        New Password <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:ring-2 focus:ring-lime-500 focus:border-lime-500 transition"
                            placeholder="Enter new password">
                        <button type="button" onclick="togglePasswordVisibility('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Minimum 8 characters, mix of letters, numbers & symbols
                    </p>
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">
                        Confirm Password <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:ring-2 focus:ring-lime-500 focus:border-lime-500 transition"
                            placeholder="Confirm new password">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Notify User Checkbox -->
                <div class="bg-slate-900/50 border border-slate-700 rounded-lg p-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" id="notify_user" name="notify_user" value="1" checked
                            class="mt-0.5 w-4 h-4 text-lime-500 bg-slate-900 border-slate-600 rounded focus:ring-lime-500 focus:ring-2">
                        <div class="flex-1">
                            <span class="text-sm font-medium text-slate-200">Send notification email to user</span>
                            <p class="text-xs text-slate-400 mt-1">User will receive an email notification about this
                                password change</p>
                        </div>
                    </label>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-700">
                    <button type="button" onclick="closeSetPasswordModal()"
                        class="px-5 py-2.5 bg-slate-700 text-slate-300 rounded-lg hover:bg-slate-600 transition font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-lime-500 text-slate-950 rounded-lg font-medium hover:bg-lime-400 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Set Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Toggle dropdown menu (works for all dropdowns)
    function toggleDropdownMenu(menuId) {
        const menu = document.getElementById(menuId);
        // Close all other dropdown menus first
        document.querySelectorAll('[id$="-menu-' + menuId.split('-').pop() + '"]').forEach(m => {
            if (m.id !== menuId) {
                m.classList.add('hidden');
            }
        });
        // Close all dropdowns that might be open
        document.querySelectorAll('[id^="view-menu-"], [id^="actions-menu-"], [id^="password-menu-"]').forEach(m => {
            if (m.id !== menuId) {
                m.classList.add('hidden');
            }
        });
        menu.classList.toggle('hidden');
    }
    
    // Legacy function for backward compatibility
    function togglePasswordMenu(menuId) {
        toggleDropdownMenu(menuId);
    }

    // Close menus when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative')) {
            document.querySelectorAll('[id^="view-menu-"], [id^="actions-menu-"], [id^="password-menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });

    // Open set password modal
    function openSetPasswordModal(userId, userName, userEmail) {
        const modal = document.getElementById('setPasswordModal');
        const form = document.getElementById('setPasswordForm');
        const modalUserName = document.getElementById('modalUserName');
        
        form.action = `/admin/users/${userId}/set-password`;
        modalUserName.textContent = `${userName} (${userEmail})`;
        
        // Reset form
        form.reset();
        document.getElementById('notify_user').checked = true;
        
        // Show modal with fade-in effect
        modal.classList.remove('hidden');
        modal.style.display = 'block';
        setTimeout(() => {
            modal.style.opacity = '1';
        }, 10);
        
        // Focus on password field
        setTimeout(() => {
            document.getElementById('password').focus();
        }, 100);
        
        // Close dropdown menu
        document.querySelectorAll('[id^="password-menu-"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    }

    // Close set password modal
    function closeSetPasswordModal() {
        const modal = document.getElementById('setPasswordModal');
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }, 200);
    }

    // Toggle password visibility
    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        if (field.type === 'password') {
            field.type = 'text';
        } else {
            field.type = 'password';
        }
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeSetPasswordModal();
            // Also close dropdowns
            document.querySelectorAll('[id^="password-menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });

    // Close modal when clicking outside
    document.getElementById('setPasswordModal')?.addEventListener('click', function(event) {
        if (event.target === this) {
            closeSetPasswordModal();
        }
    });

    // Form validation
    document.getElementById('setPasswordForm')?.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;
        
        if (password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long');
            return false;
        }
        
        if (password !== confirmation) {
            e.preventDefault();
            alert('Passwords do not match');
            return false;
        }
    });
</script>

@endsection