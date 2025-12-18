<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header with Navigation -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('employee.dashboard') }}" class="text-slate-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-white">{{ $employee->full_name ?? $employee->name }}</h1>
                        <p class="text-slate-400 mt-1">{{ $employee->position ?? 'Employee' }} • Employee Code: {{ $employee->code ?? 'N/A' }}</p>
                    </div>
                </div>
                <a href="{{ route('employee.dashboard') }}"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-lg transition inline-flex items-center gap-2 border border-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 15l-3-3m0 0l3-3m-3 3h18" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>

            <!-- Profile Header Card -->
            <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl border border-slate-700 overflow-hidden mb-8">
                <div class="p-8">
                    <div class="flex items-start gap-8">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            @if($employee->avatar)
                            <img src="{{ asset('storage/' . $employee->avatar) }}" alt="{{ $employee->name }}"
                                class="w-32 h-32 rounded-2xl border-4 border-slate-600 object-cover shadow-lg">
                            @else
                            <div class="w-32 h-32 rounded-2xl bg-gradient-to-br from-lime-500 to-green-600 flex items-center justify-center border-4 border-slate-600 shadow-lg">
                                <span class="text-5xl font-bold text-white">{{ substr($employee->full_name ?? $employee->name, 0, 1) }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Key Info Grid -->
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Name & Code -->
                            <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Full Name</p>
                                <p class="text-white font-bold text-lg">{{ $employee->full_name ?? $employee->name }}</p>
                                <p class="text-xs text-slate-500 mt-1">ID: {{ $employee->code ?? 'N/A' }}</p>
                            </div>

                            <!-- Position -->
                            <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Position</p>
                                <p class="text-white font-bold text-lg">{{ $employee->position ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $employee->employment_type ? ucfirst($employee->employment_type) : 'N/A' }}</p>
                            </div>

                            <!-- Department -->
                            <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Department</p>
                                <p class="text-white font-bold text-lg">{{ $employee->department->name ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $employee->company->name ?? 'N/A' }}</p>
                            </div>

                            <!-- Hire Date -->
                            <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Hire Date</p>
                                <p class="text-white font-bold text-lg">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'N/A' }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $employee->hire_date ? $employee->hire_date->diffForHumans() : 'N/A' }}</p>
                            </div>

                            <!-- Status -->
                            <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Status</p>
                                @if(($employee->user->status ?? 'active') === 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-500/20 text-green-400">
                                    <svg class="w-3 h-3 mr-1 fill-current" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                                    Active
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-500/20 text-red-400">
                                    <svg class="w-3 h-3 mr-1 fill-current" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
                                    Inactive
                                </span>
                                @endif
                            </div>

                            <!-- Reports To -->
                            <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Reports To</p>
                                <p class="text-white font-bold text-lg">{{ $employee->manager->full_name ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $employee->manager->position ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabbed Content -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700 overflow-hidden" x-data="{ activeTab: 'personal' }">
                <!-- Tab Navigation -->
                <div class="border-b border-slate-700 bg-slate-900/50">
                    <nav class="flex flex-wrap px-6 gap-2" aria-label="Profile Tabs">
                        <button @click="activeTab = 'personal'"
                            :class="activeTab === 'personal' ? 'border-lime-500 text-lime-400 bg-slate-900/50' : 'border-transparent text-slate-400 hover:text-slate-300'"
                            class="py-4 px-4 border-b-2 font-medium text-sm transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Personal Information
                        </button>
                        <button @click="activeTab = 'employment'"
                            :class="activeTab === 'employment' ? 'border-lime-500 text-lime-400 bg-slate-900/50' : 'border-transparent text-slate-400 hover:text-slate-300'"
                            class="py-4 px-4 border-b-2 font-medium text-sm transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.582a2 2 0 00-1.897 1.13A6 6 0 0112 21m0 0h6.582a2 2 0 001.897-1.13A6 6 0 0112 21m0 0H6.418A2 2 0 004.52 22.13a6 6 0 00.879-1.13m8 0a2 2 0 00-1.897-1.13h-5.582a2 2 0 00-1.897 1.13" />
                            </svg>
                            Employment Details
                        </button>
                        <button @click="activeTab = 'compensation'"
                            :class="activeTab === 'compensation' ? 'border-lime-500 text-lime-400 bg-slate-900/50' : 'border-transparent text-slate-400 hover:text-slate-300'"
                            class="py-4 px-4 border-b-2 font-medium text-sm transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Compensation & Tax
                        </button>
                        <button @click="activeTab = 'contact'"
                            :class="activeTab === 'contact' ? 'border-lime-500 text-lime-400 bg-slate-900/50' : 'border-transparent text-slate-400 hover:text-slate-300'"
                            class="py-4 px-4 border-b-2 font-medium text-sm transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Contact Information
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-8">
                    <!-- Personal Information Tab -->
                    <div x-show="activeTab === 'personal'" x-cloak class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Personal Details -->
                            <div>
                                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                    <div class="w-1 h-6 bg-lime-500 rounded"></div>
                                    Personal Details
                                </h3>
                                <div class="space-y-4">
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Date of Birth</p>
                                        <p class="text-white font-medium">{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Gender</p>
                                        <p class="text-white font-medium">{{ $employee->gender ? ucfirst($employee->gender) : 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Blood Group</p>
                                        <p class="text-white font-medium">{{ $employee->blood_group ?? 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Marital Status</p>
                                        <p class="text-white font-medium">{{ $employee->marital_status ? ucfirst($employee->marital_status) : 'Not Provided' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Address & Emergency Contact -->
                            <div>
                                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                    <div class="w-1 h-6 bg-blue-500 rounded"></div>
                                    Additional Information
                                </h3>
                                <div class="space-y-4">
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Address</p>
                                        <p class="text-white font-medium">{{ $employee->address ?? 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Emergency Contact Name</p>
                                        <p class="text-white font-medium">{{ $employee->emergency_contact_name ?? 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Emergency Contact Relationship</p>
                                        <p class="text-white font-medium">{{ $employee->emergency_contact_relationship ?? 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Emergency Contact Phone</p>
                                        <p class="text-white font-medium">{{ $employee->emergency_contact_phone ?? 'Not Provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Details Tab -->
                    <div x-show="activeTab === 'employment'" x-cloak class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Employment Info -->
                            <div>
                                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                    <div class="w-1 h-6 bg-lime-500 rounded"></div>
                                    Employment Information
                                </h3>
                                <div class="space-y-4">
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Employment Type</p>
                                        <p class="text-white font-medium">{{ $employee->employment_type ? ucfirst($employee->employment_type) : 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Working Hours Per Day</p>
                                        <p class="text-white font-medium">{{ $employee->working_hours_per_day ?? 'Not Provided' }} hours</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Working Days Per Week</p>
                                        <p class="text-white font-medium">{{ $employee->working_days_per_week ?? 'Not Provided' }} days</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Reporting Manager</p>
                                        <p class="text-white font-medium">{{ $employee->manager->full_name ?? 'Not Assigned' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Contract Details -->
                            <div>
                                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                    <div class="w-1 h-6 bg-blue-500 rounded"></div>
                                    Contract Information
                                </h3>
                                <div class="space-y-4">
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Contract Start Date</p>
                                        <p class="text-white font-medium">{{ $employee->contract_start_date ? $employee->contract_start_date->format('M d, Y') : 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Contract End Date</p>
                                        <p class="text-white font-medium">{{ $employee->contract_end_date ? $employee->contract_end_date->format('M d, Y') : 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Probation Period</p>
                                        <p class="text-white font-medium">{{ $employee->probation_period_months ?? 'Not Provided' }} months</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Hire Date</p>
                                        <p class="text-white font-medium">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'Not Provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compensation & Tax Tab -->
                    <div x-show="activeTab === 'compensation'" x-cloak class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Salary Information -->
                            <div>
                                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                    <div class="w-1 h-6 bg-green-500 rounded"></div>
                                    Salary Information
                                </h3>
                                <div class="space-y-4">
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Salary Type</p>
                                        <p class="text-white font-medium">{{ $employee->salary_type ? ucfirst($employee->salary_type) : 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Base Salary</p>
                                        <p class="text-white font-bold text-lg">{{ $employee->base_salary ? 'NPR ' . number_format($employee->base_salary, 2) : 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Allowances</p>
                                        <p class="text-white font-medium">{{ $employee->allowances ? 'NPR ' . number_format($employee->allowances, 2) : 'Not Provided' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tax Information -->
                            <div>
                                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                    <div class="w-1 h-6 bg-purple-500 rounded"></div>
                                    Tax Information
                                </h3>
                                <div class="space-y-4">
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">PAN Number</p>
                                        <p class="text-white font-medium">{{ $employee->pan_number ?? 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Tax Regime</p>
                                        <p class="text-white font-medium">{{ $employee->tax_regime ?? 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Member Since</p>
                                        <p class="text-white font-medium">{{ $employee->created_at ? $employee->created_at->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Tab -->
                    <div x-show="activeTab === 'contact'" x-cloak class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Emails and Phones -->
                            <div>
                                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                    <div class="w-1 h-6 bg-lime-500 rounded"></div>
                                    Contact Details
                                </h3>
                                <div class="space-y-4">
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Email</p>
                                        <p class="text-white font-medium break-all">{{ $employee->user->email ?? 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Phone Number</p>
                                        <p class="text-white font-medium">{{ $employee->phone ?? 'Not Provided' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Bank Details -->
                            <div>
                                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                    <div class="w-1 h-6 bg-blue-500 rounded"></div>
                                    Bank Details
                                </h3>
                                <div class="space-y-4">
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Bank Name</p>
                                        <p class="text-white font-medium">{{ $employee->bank_name ?? 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Account Number</p>
                                        <p class="text-white font-medium">{{ $employee->bank_account_number ?? 'Not Provided' }}</p>
                                    </div>
                                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Branch</p>
                                        <p class="text-white font-medium">{{ $employee->bank_branch ?? 'Not Provided' }}</p>
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