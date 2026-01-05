@extends('admin.layouts.app')

@section('title', 'User Profile')
@section('page-title', 'User Profile')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.users.index') }}" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-white">{{ $user->name }}</h2>
                <p class="text-slate-400 mt-1">{{ $user->email }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($jibbleEmployee)
            <!-- Employee Profile Button -->
            <a href="{{ route('admin.hrm.employees.show', $jibbleEmployee) }}"
                class="group relative px-5 py-2.5 bg-gradient-to-r from-teal-600 to-teal-500 text-white rounded-lg font-medium hover:from-teal-500 hover:to-teal-400 hover:shadow-lg hover:shadow-teal-500/30 transition-all duration-200 inline-flex items-center">
                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-sm">Employee Profile</span>
            </a>

            <!-- Timesheet Button -->
            <a href="{{ route('admin.hrm.attendance.employee', $jibbleEmployee) }}"
                class="group relative px-5 py-2.5 bg-slate-700 text-white rounded-lg font-medium hover:bg-slate-600 hover:shadow-lg hover:shadow-slate-500/20 border border-slate-600 hover:border-slate-500 transition-all duration-200 inline-flex items-center">
                <svg class="w-5 h-5 mr-2 text-slate-300 group-hover:text-white group-hover:scale-110 transition-all duration-200"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Timesheet</span>
            </a>
            @endif

            <!-- Edit User Button -->
            <a href="{{ route('admin.users.edit', $user) }}"
                class="group relative px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-lg font-medium hover:from-blue-500 hover:to-blue-400 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-200 inline-flex items-center">
                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span class="text-sm">Edit User</span>
            </a>

            <!-- Password Options Dropdown -->
            <div class="relative">
                <button type="button" onclick="togglePasswordMenu('password-menu')"
                    class="group relative px-5 py-2.5 bg-gradient-to-r from-amber-600 to-amber-500 text-white rounded-lg font-medium hover:from-amber-500 hover:to-amber-400 hover:shadow-lg hover:shadow-amber-500/30 transition-all duration-200 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span class="text-sm">Password Options</span>
                    <svg class="w-4 h-4 ml-2 group-hover:translate-y-0.5 transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="password-menu"
                    class="hidden absolute right-0 mt-2 w-80 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl overflow-hidden z-50">
                    <!-- Set New Password -->
                    <button type="button" onclick="openSetPasswordModal()"
                        class="group/item w-full text-left px-5 py-4 text-sm text-slate-300 hover:bg-slate-700/80 hover:text-white border-b border-slate-700/50 transition-all duration-150">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 bg-lime-500/10 rounded-lg flex items-center justify-center group-hover/item:bg-lime-500/20 transition-colors">
                                <svg class="w-5 h-5 text-lime-400 group-hover/item:scale-110 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-white mb-1">Set New Password</div>
                                <div class="text-xs text-slate-400">Manually set a new password</div>
                            </div>
                        </div>
                    </button>

                    <!-- Send Reset Link -->
                    <form action="{{ route('admin.users.send-reset-link', $user) }}" method="POST"
                        id="send-reset-link-form">
                        @csrf
                        <button type="button"
                            class="group/item w-full text-left px-5 py-4 text-sm text-slate-300 hover:bg-slate-700/80 hover:text-white border-b border-slate-700/50 transition-all duration-150"
                            onclick="confirmAction({title: 'Send Password Reset Link?', message: 'A password reset link will be sent to &lt;strong&gt;{{ $user->email }}&lt;/strong&gt;. The user can click the link to set a new password.', type: 'info', confirmText: 'Yes, Send Link', onConfirm: () => document.getElementById('send-reset-link-form').submit()})">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center group-hover/item:bg-blue-500/20 transition-colors">
                                    <svg class="w-5 h-5 text-blue-400 group-hover/item:scale-110 transition-transform"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-white mb-1">Send Reset Link</div>
                                    <div class="text-xs text-slate-400">User sets their own password</div>
                                </div>
                            </div>
                        </button>
                    </form>

                    <!-- Generate & Email Password -->
                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST"
                        id="generate-password-form">
                        @csrf
                        <button type="button"
                            class="group/item w-full text-left px-5 py-4 text-sm text-slate-300 hover:bg-slate-700/80 hover:text-white transition-all duration-150"
                            onclick="confirmAction({title: 'Generate Random Password?', message: 'A random password will be generated and sent to &lt;strong&gt;{{ $user->email }}&lt;/strong&gt;.&lt;br&gt;&lt;br&gt;&lt;strong&gt;Note:&lt;/strong&gt; This will replace their current password immediately.', type: 'warning', confirmText: 'Yes, Generate & Send', onConfirm: () => document.getElementById('generate-password-form').submit()})">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-amber-500/10 rounded-lg flex items-center justify-center group-hover/item:bg-amber-500/20 transition-colors">
                                    <svg class="w-5 h-5 text-amber-400 group-hover/item:scale-110 transition-transform"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-white mb-1">Generate & Email Password</div>
                                    <div class="text-xs text-slate-400">Auto-generate random password</div>
                                </div>
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            @if($user->id !== Auth::id())
            <!-- Toggle Leads Access Button (Employee Only) -->
            @if($user->role === 'employee')
            <form action="{{ route('admin.users.toggle-leads-access', $user) }}" method="POST" class="inline-block">
                @csrf
                <button type="submit"
                    class="group relative px-5 py-2.5 bg-gradient-to-r {{ $user->can_access_leads ? 'from-orange-600 to-orange-500' : 'from-lime-600 to-lime-500' }} text-white rounded-lg font-medium hover:{{ $user->can_access_leads ? 'from-orange-500 hover:to-orange-400 hover:shadow-orange-500/30' : 'from-lime-500 hover:to-lime-400 hover:shadow-lime-500/30' }} hover:shadow-lg transition-all duration-200 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm">{{ $user->can_access_leads ? 'Disable' : 'Enable' }} Leads Access</span>
                </button>
            </form>
            @endif

            <!-- Delete User Button -->
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block"
                id="delete-user-profile-form">
                @csrf
                @method('DELETE')
                <button type="button"
                    class="group relative px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-500 text-white rounded-lg font-medium hover:from-red-500 hover:to-red-400 hover:shadow-lg hover:shadow-red-500/30 transition-all duration-200 inline-flex items-center"
                    onclick="confirmAction({title: 'Delete User & Employee?', message: 'Are you sure you want to delete &lt;strong&gt;{{ $user->name }}&lt;/strong&gt;?&lt;br&gt;&lt;br&gt;This will permanently delete:&lt;br&gt;• User login account&lt;br&gt;• Employee record&lt;br&gt;• All attendance data&lt;br&gt;&lt;br&gt;&lt;strong&gt;This action cannot be undone!&lt;/strong&gt;', type: 'danger', confirmText: 'Yes, Delete Everything', onConfirm: () => document.getElementById('delete-user-profile-form').submit()})">
                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span class="text-sm">Delete User</span>
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- User Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            <div class="flex flex-col items-center text-center">
                <div class="w-24 h-24 bg-lime-500/20 rounded-full flex items-center justify-center mb-4">
                    <span class="text-lime-400 font-bold text-3xl">{{ substr($user->name, 0, 2) }}</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-1">{{ $user->name }}</h3>
                <p class="text-slate-400 text-sm mb-3">{{ $user->email }}</p>
                <span
                    class="px-3 py-1 text-sm font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-lime-500/20 text-lime-400' : 'bg-slate-700 text-slate-300' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-700 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400 text-sm">Member Since</span>
                    <span class="text-white text-sm">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400 text-sm">Last Updated</span>
                    <span class="text-white text-sm">{{ $user->updated_at->diffForHumans() }}</span>
                </div>
                @if($user->role === 'employee')
                <div class="flex items-center justify-between pt-3 border-t border-slate-700">
                    <span class="text-slate-400 text-sm">Leads Module Access</span>
                    <span class="flex items-center text-sm">
                        @if($user->can_access_leads)
                        <svg class="w-4 h-4 text-lime-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-lime-400 font-medium">Enabled</span>
                        @else
                        <svg class="w-4 h-4 text-slate-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-slate-500">Disabled</span>
                        @endif
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Jibble Profile -->
        <div class="lg:col-span-2 bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Jibble Employee Profile
            </h3>

            @if($jibbleEmployee)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-slate-900 rounded-lg p-4">
                    <p class="text-slate-400 text-sm mb-1">Employee Code</p>
                    <p class="text-white font-medium">{{ $jibbleEmployee->jibble_person_id }}</p>
                </div>
                <div class="bg-slate-900 rounded-lg p-4">
                    <p class="text-slate-400 text-sm mb-1">Company</p>
                    <p class="text-white font-medium">{{ $jibbleEmployee->company->name ?? 'N/A' }}</p>
                </div>
                <div class="bg-slate-900 rounded-lg p-4">
                    <p class="text-slate-400 text-sm mb-1">Department</p>
                    <p class="text-white font-medium">{{ $jibbleEmployee->department->name ?? 'N/A' }}</p>
                </div>
                <div class="bg-slate-900 rounded-lg p-4">
                    <p class="text-slate-400 text-sm mb-1">Status</p>
                    <span
                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $jibbleEmployee->status === 'active' ? 'bg-lime-500/20 text-lime-400' : 'bg-red-500/20 text-red-400' }}">
                        {{ $jibbleEmployee->status === 'active' ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.hrm.employees.show', $jibbleEmployee) }}"
                    class="text-blue-400 hover:text-blue-300 text-sm flex items-center">
                    View Full Employee Profile
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="text-slate-400">This user is not linked to a Jibble employee account.</p>
                <a href="{{ route('admin.hrm.employees.index') }}"
                    class="text-blue-400 hover:text-blue-300 text-sm mt-2 inline-block">
                    Manage Employees
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Recent Attendance -->
    @if($jibbleEmployee && $recentAttendance && $recentAttendance->count() > 0)
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Recent Attendance (Last 10 Days)
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase">Tracked Hours</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase">Payroll Hours</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase">Overtime</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase">Source</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @foreach($recentAttendance as $attendance)
                    <tr class="hover:bg-slate-700/50">
                        <td class="px-4 py-3 text-sm text-white">{{ $attendance->date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-sm text-slate-300">{{ number_format($attendance->tracked_hours, 2) }}h
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-300">{{ number_format($attendance->payroll_hours, 2) }}h
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="{{ $attendance->overtime_hours > 0 ? 'text-orange-400' : 'text-slate-500' }}">
                                {{ number_format($attendance->overtime_hours, 2) }}h
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full {{ $attendance->source === 'jibble' ? 'bg-blue-500/20 text-blue-400' : 'bg-slate-700 text-slate-300' }}">
                                {{ ucfirst($attendance->source) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.hrm.attendance.employee', $jibbleEmployee) }}"
                class="text-lime-400 hover:text-lime-300 text-sm flex items-center">
                View Full Attendance History
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
    @endif
</div>

<!-- Set Password Modal -->
<div id="setPasswordModal"
    class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-lg shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between p-6 border-b border-slate-700">
            <h3 class="text-xl font-semibold text-white">Set New Password</h3>
            <button onclick="closeSetPasswordModal()" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form action="{{ route('admin.users.set-password', $user) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <p class="text-sm text-slate-400 mb-4">Setting password for: <span class="text-white font-medium">{{
                        $user->name }} ({{ $user->email }})</span></p>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-2">New Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-lime-500 focus:border-transparent"
                    placeholder="Enter new password">
                <p class="text-xs text-slate-400 mt-1">Minimum 8 characters</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">Confirm
                    Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-lime-500 focus:border-transparent"
                    placeholder="Confirm new password">
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="notify_user" name="notify_user" value="1" checked
                    class="w-4 h-4 text-lime-500 bg-slate-900 border-slate-700 rounded focus:ring-lime-500">
                <label for="notify_user" class="ml-2 text-sm text-slate-300">
                    Send notification email to user
                </label>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-700">
                <button type="button" onclick="closeSetPasswordModal()"
                    class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-lime-500 text-slate-950 rounded-lg font-medium hover:bg-lime-400 transition">
                    Set Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Toggle password menu dropdown
    function togglePasswordMenu(menuId) {
        const menu = document.getElementById(menuId);
        menu.classList.toggle('hidden');
    }

    // Close menus when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.group')) {
            const menu = document.getElementById('password-menu');
            if (menu) {
                menu.classList.add('hidden');
            }
        }
    });

    // Open set password modal
    function openSetPasswordModal() {
        const modal = document.getElementById('setPasswordModal');
        modal.classList.remove('hidden');
        
        // Close dropdown menu
        const menu = document.getElementById('password-menu');
        if (menu) {
            menu.classList.add('hidden');
        }
    }

    // Close set password modal
    function closeSetPasswordModal() {
        const modal = document.getElementById('setPasswordModal');
        modal.classList.add('hidden');
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeSetPasswordModal();
        }
    });
</script>

<!-- Confirmation Modal Component -->
@include('components.confirm-modal')

@endsection