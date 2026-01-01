@extends('admin.layouts.app')

@section('title', 'Team & Users')
@section('page-title', 'Team & User Management')

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
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white">Team & Users</h2>
            <p class="text-slate-400 mt-1">Manage your team members and system users in one place</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.hrm.attendance.index') }}"
                class="px-4 py-2 bg-slate-700 text-white rounded-lg font-medium hover:bg-slate-600 transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Attendance Records</span>
            </a>
            <a href="{{ route('admin.hrm.attendance.sync-employees') }}"
                class="px-4 py-2 bg-lime-500 text-slate-950 rounded-lg font-medium hover:bg-lime-400 transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582M20 20v-5h-.581M5 9a7 7 0 0114 0M5 9H4m15 0h1M5 15a7 7 0 0114 0M5 15H4m15 0h1" />
                </svg>
                <span>Sync Employees from Jibble</span>
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

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Total Users</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $users->total() }}</p>
                </div>
                <div class="w-10 h-10 bg-lime-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Active</p>
                    @php
                    if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'status')) {
                    $activeCount = \App\Models\User::where('status', 'active')->count();
                    } else {
                    $activeCount = \App\Models\User::count();
                    }
                    @endphp
                    <p class="text-2xl font-bold text-lime-400 mt-1">{{ $activeCount }}</p>
                </div>
                <div class="w-10 h-10 bg-lime-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Admins</p>
                    <p class="text-2xl font-bold text-blue-400 mt-1">{{ \App\Models\User::where('role',
                        'admin')->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">With Employee Profile</p>
                    <p class="text-2xl font-bold text-teal-400 mt-1">{{
                        \App\Models\User::whereHas('hrmEmployee')->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-teal-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Company / Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Role
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Joined</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-slate-800/50 divide-y divide-slate-700">
                    @forelse($users as $user)
                    @php
                    $employee = $user->hrmEmployee;
                    @endphp
                    <tr class="hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    @if($employee && $employee->avatar_url)
                                    <img class="h-12 w-12 rounded-full object-cover border-2 border-slate-600"
                                        src="{{ $employee->avatar_url }}" alt="{{ $user->name }}">
                                    @else
                                    <div
                                        class="h-12 w-12 rounded-full bg-gradient-to-br from-lime-500 to-lime-600 flex items-center justify-center shadow-lg">
                                        <span class="text-slate-900 font-bold text-lg">{{ strtoupper(substr($user->name,
                                            0, 1)) }}</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $user->email }}</div>
                                    @if($employee && $employee->position)
                                    <div class="text-xs text-slate-500 mt-1">{{ $employee->position }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($employee)
                            <div class="text-sm text-white">{{ $employee->company->name ?? 'N/A' }}</div>
                            <div class="text-xs text-slate-400">{{ $employee->department->name ?? 'No Department' }}
                            </div>
                            @else
                            <span class="text-xs text-slate-500 italic">No employee profile</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ ($user->status ?? 'active') === 'active' ? 'bg-lime-500/20 text-lime-400' : '' }}
                                {{ ($user->status ?? 'active') === 'inactive' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                {{ ($user->status ?? 'active') === 'suspended' ? 'bg-red-500/20 text-red-400' : '' }}">
                                {{ ucfirst($user->status ?? 'active') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-blue-500/20 text-blue-400' : 'bg-slate-700 text-slate-300' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <!-- View User Profile Button -->
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-transparent text-slate-300 rounded-lg hover:bg-slate-700/50 hover:text-white transition border border-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>View</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">
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
                        <form action="{{ route('admin.users.send-reset-link', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Send password reset link to {{ $user->email }}?')"
                                class="w-full text-left px-4 py-3 text-sm hover:bg-slate-700 transition border-b border-slate-700">
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
                        <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Generate random password and email to {{ $user->email }}?')"
                                class="w-full text-left px-4 py-3 text-sm hover:bg-slate-700 transition">
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
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this user?')"
                        class="px-3 py-1.5 text-xs bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition">
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