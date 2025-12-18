@extends('admin.layouts.app')

@section('title', 'Employee Profile')
@section('page-title', 'Employee Profile')

@section('content')
<div class="space-y-6 max-w-7xl">
    <!-- Header with Actions -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-white">{{ $employee->name }}</h1>
            <p class="text-slate-400 mt-1">{{ $employee->position ?? 'Employee' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.hrm.attendance.employee', $employee) }}"
                class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Timesheet
            </a>
            <a href="{{ route('admin.hrm.employees.edit', $employee) }}"
                class="px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Profile
            </a>
            <a href="{{ route('admin.hrm.employees.index') }}"
                class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
                Back
            </a>
        </div>
    </div>

    <!-- Employee Card with Avatar -->
    <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
        <div class="p-6">
            <div class="flex items-start gap-6">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    @if($employee->avatar_url)
                    <img src="{{ $employee->avatar_url }}" alt="{{ $employee->name }}"
                        class="w-24 h-24 rounded-full border-4 border-slate-700">
                    @else
                    <div class="w-24 h-24 rounded-full bg-lime-500 flex items-center justify-center">
                        <span class="text-3xl font-bold text-slate-900">{{ substr($employee->name, 0, 1) }}</span>
                    </div>
                    @endif
                </div>

                <!-- Basic Info -->
                <div class="flex-1 grid grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Employee</p>
                        <p class="text-white font-semibold">
                            {{ $employee->full_name ?? $employee->name }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Email</p>
                        <p class="text-white font-semibold">{{ $employee->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Phone</p>
                        <p class="text-white font-semibold">{{ $employee->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Status</p>
                        @if($employee->status === 'active')
                        <span
                            class="px-2 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-400">Active</span>
                        @else
                        <span
                            class="px-2 py-1 text-xs font-semibold rounded-full bg-red-500/20 text-red-400">Inactive</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Company</p>
                        <p class="text-white font-semibold">{{ $employee->company->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Department</p>
                        <p class="text-white font-semibold">{{ $employee->department->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Designation</p>
                        <p class="text-white font-semibold">{{ $employee->position ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Hire Date</p>
                        <p class="text-white font-semibold">{{ $employee->hire_date ? $employee->hire_date->format('M d,
                            Y') : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Content -->
    <div class="bg-slate-800 rounded-lg border border-slate-700" x-data="{ activeTab: 'overview' }">
        <!-- Tab Navigation -->
        <div class="border-b border-slate-700">
            <nav class="flex space-x-1 px-6" aria-label="Tabs">
                <button @click="activeTab = 'overview'"
                    :class="activeTab === 'overview' ? 'border-lime-500 text-lime-500' : 'border-transparent text-slate-400 hover:text-slate-300'"
                    class="py-4 px-4 border-b-2 font-medium text-sm transition">
                    Overview
                </button>
                <button @click="activeTab = 'attendance'"
                    :class="activeTab === 'attendance' ? 'border-lime-500 text-lime-500' : 'border-transparent text-slate-400 hover:text-slate-300'"
                    class="py-4 px-4 border-b-2 font-medium text-sm transition">
                    Attendance
                </button>
                <button @click="activeTab = 'contract'"
                    :class="activeTab === 'contract' ? 'border-lime-500 text-lime-500' : 'border-transparent text-slate-400 hover:text-slate-300'"
                    class="py-4 px-4 border-b-2 font-medium text-sm transition">
                    Contract & Salary
                </button>
                <button @click="activeTab = 'banking'"
                    :class="activeTab === 'banking' ? 'border-lime-500 text-lime-500' : 'border-transparent text-slate-400 hover:text-slate-300'"
                    class="py-4 px-4 border-b-2 font-medium text-sm transition">
                    Banking & Tax
                </button>
                <button @click="activeTab = 'leaves'"
                    :class="activeTab === 'leaves' ? 'border-lime-500 text-lime-500' : 'border-transparent text-slate-400 hover:text-slate-300'"
                    class="py-4 px-4 border-b-2 font-medium text-sm transition">
                    Leaves
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Overview Tab -->
            <div x-show="activeTab === 'overview'" x-cloak>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-4">Personal Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Date of Birth</span>
                                <span class="text-white font-medium">{{ $employee->date_of_birth ?
                                    $employee->date_of_birth->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Gender</span>
                                <span class="text-white font-medium">{{ ucfirst($employee->gender ?? 'N/A') }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Blood Group</span>
                                <span class="text-white font-medium">{{ $employee->blood_group ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Marital Status</span>
                                <span class="text-white font-medium">{{ ucfirst($employee->marital_status ?? 'N/A')
                                    }}</span>
                            </div>
                            <div class="py-2">
                                <span class="text-slate-400 block mb-1">Address</span>
                                <span class="text-white font-medium">{{ $employee->address ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-4">Emergency Contact</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Contact Name</span>
                                <span class="text-white font-medium">{{ $employee->emergency_contact_name ?? 'N/A'
                                    }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Relationship</span>
                                <span class="text-white font-medium">{{ $employee->emergency_contact_relationship ??
                                    'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Phone Number</span>
                                <span class="text-white font-medium">{{ $employee->emergency_contact_phone ?? 'N/A'
                                    }}</span>
                            </div>
                        </div>

                        <h3 class="text-lg font-semibold text-white mt-6 mb-4">Employment Details</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Employee Type</span>
                                <span class="text-white font-medium">{{ ucfirst($employee->employment_type ?? 'N/A')
                                    }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Working Days</span>
                                <span class="text-white font-medium">{{ $employee->working_days_per_week ?? 'N/A' }}
                                    days/week</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Tab -->
            <div x-show="activeTab === 'attendance'" x-cloak>
                <h3 class="text-lg font-semibold text-white mb-4">Recent Attendance (Last 30 Days)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-700">
                        <thead>
                            <tr class="bg-slate-900">
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Date</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Tracked Time</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Payroll Time</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Break Time</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Overtime</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            @forelse($employee->attendanceDays()->orderBy('date', 'desc')->limit(30)->get() as $day)
                            <tr class="hover:bg-slate-700/50 transition">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-white">
                                    {{ $day->date->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-white">
                                    {{ number_format((float)($day->tracked_hours ?? 0), 2) }}h
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-white">
                                    {{ number_format((float)($day->payroll_hours ?? 0), 2) }}h
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-white">
                                    —
                                </td>
                                <td
                                    class="px-4 py-3 whitespace-nowrap text-sm {{ $day->overtime_hours > 0 ? 'text-orange-400' : 'text-slate-500' }}">
                                    {{ number_format((float)($day->overtime_hours ?? 0), 2) }}h
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.hrm.attendance.show', $day) }}"
                                        class="text-lime-400 hover:text-lime-300 font-medium">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400">
                                    No attendance records found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Contract & Salary Tab -->
            <div x-show="activeTab === 'contract'" x-cloak>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-4">Salary Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Salary Type</span>
                                <span class="text-white font-medium">{{ ucfirst($employee->salary_type ?? 'N/A')
                                    }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Basic Salary</span>
                                <span class="text-white font-medium">{{ $employee->salary_amount ? 'Rs. ' .
                                    number_format($employee->salary_amount, 2) : 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Allowances</span>
                                <span class="text-white font-medium">{{ $employee->allowances ? 'Rs. ' .
                                    number_format($employee->allowances, 2) : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-white mb-4">Contract Details</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Contract Start</span>
                                <span class="text-white font-medium">{{ $employee->contract_start_date ?
                                    $employee->contract_start_date->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Contract End</span>
                                <span class="text-white font-medium">{{ $employee->contract_end_date ?
                                    $employee->contract_end_date->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Probation Period</span>
                                <span class="text-white font-medium">{{ $employee->probation_period_months ?? 'N/A' }}
                                    months</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banking & Tax Tab -->
            <div x-show="activeTab === 'banking'" x-cloak>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-4">Bank Details</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Bank Name</span>
                                <span class="text-white font-medium">{{ $employee->bank_name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Account Number</span>
                                <span class="text-white font-medium">{{ $employee->bank_account_number ?? 'N/A'
                                    }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Branch</span>
                                <span class="text-white font-medium">{{ $employee->bank_branch ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-white mb-4">Tax Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">PAN Number</span>
                                <span class="text-white font-medium">{{ $employee->pan_number ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Tax Regime</span>
                                <span class="text-white font-medium">{{ $employee->tax_regime ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leaves Tab -->
            <div x-show="activeTab === 'leaves'" x-cloak>
                <h3 class="text-lg font-semibold text-white mb-4">Leave Balance</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-slate-900 rounded-lg p-4 border border-slate-700">
                        <p class="text-slate-400 text-sm">Sick Leave</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $employee->sick_leave_balance ?? 0 }} days</p>
                    </div>
                    <div class="bg-slate-900 rounded-lg p-4 border border-slate-700">
                        <p class="text-slate-400 text-sm">Casual Leave</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $employee->casual_leave_balance ?? 0 }} days
                        </p>
                    </div>
                    <div class="bg-slate-900 rounded-lg p-4 border border-slate-700">
                        <p class="text-slate-400 text-sm">Annual Leave</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $employee->annual_leave_balance ?? 0 }} days
                        </p>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-white mb-4">Recent Leave Requests</h3>
                <div class="text-center py-8 text-slate-400">
                    Leave management coming soon
                </div>
            </div>
        </div>
    </div>
</div>
@endsection