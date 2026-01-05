@extends('admin.layouts.app')

@section('title', 'Create User Account')
@section('page-title', 'Create User Account for Employee')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center text-slate-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-white">Create User Account</h2>
                <p class="text-slate-400 mt-1">Create a login account for {{ $employee->name }}</p>
            </div>
        </div>
    </div>

    <!-- Employee Info Card -->
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4">Employee Information</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-slate-400">Name</p>
                <p class="text-white font-medium">{{ $employee->name }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-400">Position</p>
                <p class="text-white font-medium">{{ $employee->position ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-400">Company</p>
                <p class="text-white font-medium">{{ $employee->company->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-400">Department</p>
                <p class="text-white font-medium">{{ $employee->department->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-400">Current Email</p>
                <p class="text-white font-medium">{{ $employee->email ?? 'No email set' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-400">Jibble Status</p>
                <p class="text-white font-medium">
                    @if($employee->jibble_person_id)
                    <span class="text-lime-400">✓ Synced from Jibble</span>
                    @else
                    <span class="text-slate-400">Not synced</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Account Creation Form -->
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-white mb-6">Account Details</h3>

        <form method="POST" action="{{ route('admin.users.store-for-employee', $employee) }}" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                    Email Address <span class="text-red-400">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('email') border-red-500 @enderror"
                    placeholder="user@example.com">
                @error('email')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-400">This will be used for login and communication</p>
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-slate-300 mb-2">
                    Role <span class="text-red-400">*</span>
                </label>
                <select name="role" id="role" required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('role') border-red-500 @enderror">
                    <option value="user" {{ old('role')=='user' ? 'selected' : '' }}>User (Standard Access)</option>
                    <option value="admin" {{ old('role')=='admin' ? 'selected' : '' }}>Admin (Full Access)</option>
                </select>
                @error('role')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                    Password <span class="text-red-400">*</span>
                </label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('password') border-red-500 @enderror"
                    placeholder="Enter secure password">
                @error('password')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-400">Minimum 8 characters, include letters and numbers</p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">
                    Confirm Password <span class="text-red-400">*</span>
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500"
                    placeholder="Re-enter password">
            </div>

            <!-- Send Credentials -->
            <div class="flex items-center gap-3 p-4 bg-slate-700/50 rounded-lg border border-slate-600">
                <input type="checkbox" name="send_credentials" id="send_credentials" value="1"
                    class="w-4 h-4 text-lime-500 bg-slate-700 border-slate-600 rounded focus:ring-lime-500 focus:ring-2">
                <label for="send_credentials" class="text-sm text-slate-300">
                    Send login credentials to employee's email
                </label>
            </div>

            <!-- Info Note -->
            <div class="p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-blue-300">
                        <p class="font-semibold mb-1">Important</p>
                        <ul class="list-disc list-inside space-y-1 text-blue-200">
                            <li>This will create a login account and link it to the employee record</li>
                            <li>The employee will be able to access the ERP system with these credentials</li>
                            <li>Employee's email will be updated if different from current</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-700">
                <a href="{{ route('admin.users.index') }}"
                    class="px-6 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-lime-500 text-slate-950 font-semibold rounded-lg hover:bg-lime-400 transition">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection