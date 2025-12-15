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
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.users.edit', $user) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                Edit User
            </a>

            <!-- Password Reset Dropdown -->
            <div class="relative group">
                <button type="button" onclick="togglePasswordMenu('password-menu')"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg font-medium hover:bg-yellow-700 transition inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    Password Options
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="password-menu"
                    class="hidden absolute right-0 mt-2 w-64 bg-slate-800 border border-slate-700 rounded-lg shadow-xl z-50">
                    <div class="py-1">
                        <button type="button" onclick="openSetPasswordModal()"
                            class="block w-full text-left px-4 py-3 text-sm text-slate-300 hover:bg-slate-700 hover:text-white border-b border-slate-700">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-lime-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                <div>
                                    <div class="font-medium">Set New Password</div>
                                    <div class="text-xs text-slate-400">Manually set a new password</div>
                                </div>
                            </span>
                        </button>
                        <button type="button"
                            onclick="sendResetLink('{{ route('admin.users.send-reset-link', $user) }}')"
                            class="block w-full text-left px-4 py-3 text-sm text-slate-300 hover:bg-slate-700 hover:text-white border-b border-slate-700">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <div class="font-medium">Send Reset Link</div>
                                    <div class="text-xs text-slate-400">User sets their own password</div>
                                </div>
                            </span>
                        </button>
                        <button type="button"
                            onclick="generatePassword('{{ route('admin.users.reset-password', $user) }}')"
                            class="block w-full text-left px-4 py-3 text-sm text-slate-300 hover:bg-slate-700 hover:text-white">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-yellow-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <div>
                                    <div class="font-medium">Generate & Email Password</div>
                                    <div class="text-xs text-slate-400">Auto-generate random password</div>
                                </div>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
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
                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $jibbleEmployee->is_active ? 'bg-lime-500/20 text-lime-400' : 'bg-red-500/20 text-red-400' }}">
                        {{ $jibbleEmployee->is_active ? 'Active' : 'Inactive' }}
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

<!-- Confirmation Dialogs -->
<x-confirm-dialog name="send-reset-link" title="Send Reset Link"
    message="Send password reset link to {{ $user->email }}?" type="info" confirmText="Send" form="sendResetLinkForm" />

<form id="sendResetLinkForm" method="POST" style="display: none;">
    @csrf
</form>

<x-confirm-dialog name="generate-password" title="Generate & Email Password"
    message="Generate random password and email to {{ $user->email }}?" type="warning" confirmText="Generate & Send"
    form="generatePasswordForm" />

<form id="generatePasswordForm" method="POST" style="display: none;">
    @csrf
</form>

<script>
    function sendResetLink(url) {
        document.getElementById('sendResetLinkForm').action = url;
        window.dispatchEvent(new CustomEvent('open-confirm', { detail: 'send-reset-link' }));
    }

    function generatePassword(url) {
        document.getElementById('generatePasswordForm').action = url;
        window.dispatchEvent(new CustomEvent('open-confirm', { detail: 'generate-password' }));
    }
</script>

@endsection