<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header with Actions -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-white">{{ $employee->name }}</h1>
                    <p class="text-slate-400 mt-1">{{ $employee->position ?? 'Employee' }} • {{ $employee->employee_code
                        ?? 'N/A' }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('employee.profile.edit') }}"
                        class="px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Profile
                    </a>
                    <a href="{{ route('employee.dashboard') }}"
                        class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Employee Card with Avatar -->
            <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden mb-6">
                <div class="p-6">
                    <div class="flex items-start gap-6">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            @if($employee->avatar)
                            <img src="{{ asset('storage/' . $employee->avatar) }}" alt="{{ $employee->name }}"
                                class="w-24 h-24 rounded-full border-4 border-slate-700 object-cover">
                            @else
                            <div class="w-24 h-24 rounded-full bg-lime-500 flex items-center justify-center">
                                <span class="text-3xl font-bold text-slate-900">{{ substr($employee->name, 0, 1)
                                    }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Basic Info -->
                        <div class="flex-1 grid grid-cols-4 gap-4">
                            <div>
                                <p class="text-xs text-slate-400 mb-1">Employee</p>
                                <p class="text-white font-semibold">
                                    {{ $employee->full_name ?? $employee->name }}
                                    <span class="text-xs text-slate-400 ml-2">({{ $employee->employee_code ?? 'N/A'
                                        }})</span>
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
                                <p class="text-white font-semibold">{{ $employee->hire_date ?
                                    $employee->hire_date->format('M d, Y') : 'N/A' }}</p>
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
                                        <span class="text-white font-medium">{{ ucfirst($employee->gender ?? 'N/A')
                                            }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-700">
                                        <span class="text-slate-400">Blood Group</span>
                                        <span class="text-white font-medium">{{ $employee->blood_group ?? 'N/A'
                                            }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-700">
                                        <span class="text-slate-400">Marital Status</span>
                                        <span class="text-white font-medium">{{ ucfirst($employee->marital_status ??
                                            'N/A') }}</span>
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
                                        <span class="text-white font-medium">{{ $employee->emergency_contact_name ??
                                            'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-700">
                                        <span class="text-slate-400">Relationship</span>
                                        <span class="text-white font-medium">{{
                                            $employee->emergency_contact_relationship ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-700">
                                        <span class="text-slate-400">Phone Number</span>
                                        <span class="text-white font-medium">{{ $employee->emergency_contact_phone ??
                                            'N/A' }}</span>
                                    </div>
                                </div>

                                <h3 class="text-lg font-semibold text-white mt-6 mb-4">Employment Details</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between py-2 border-b border-slate-700">
                                        <span class="text-slate-400">Employee Type</span>
                                        <span class="text-white font-medium">{{ ucfirst($employee->employment_type ??
                                            'N/A') }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-700">
                                        <span class="text-slate-400">Working Hours/Day</span>
                                        <span class="text-white font-medium">{{ $employee->working_hours_per_day ??
                                            'N/A' }} hours</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-700">
                                        <span class="text-slate-400">Working Days</span>
                                        <span class="text-white font-medium">{{ $employee->working_days_per_week ??
                                            'N/A' }} days/week</span>
                                    </div>
                                </div>
                            </div>
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
                                        <span class="text-white font-medium">{{ $employee->salary_amount ? 'NPR ' .
                                            number_format($employee->salary_amount, 2) : 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-700">
                                        <span class="text-slate-400">Allowances</span>
                                        <span class="text-white font-medium">{{ $employee->allowances ? 'NPR ' .
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
                                        <span class="text-white font-medium">{{ $employee->probation_period_months ??
                                            'N/A' }} months</span>
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
                                        <span class="text-white font-medium">{{ $employee->bank_branch ?? 'N/A'
                                            }}</span>
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>