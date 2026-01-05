@extends('admin.layouts.app')

@section('title', 'Edit Employee')
@section('page-title', 'Edit Employee')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-white">Edit Employee Profile</h1>
        <a href="{{ route('admin.hrm.employees.show', $employee) }}"
            class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
            Back to Profile
        </a>
    </div>

    <form method="POST" action="{{ route('admin.hrm.employees.update', $employee) }}" x-data="{ activeTab: 'basic' }">
        @csrf
        @method('PUT')

        <!-- Tabbed Navigation -->
        <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
            <div class="border-b border-slate-200 dark:border-slate-700">
                <nav class="flex space-x-1 px-6" aria-label="Tabs">
                    <button type="button" @click="activeTab = 'basic'"
                        :class="activeTab === 'basic' ? 'border-lime-500 text-lime-500' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-300'"
                        class="py-4 px-4 border-b-2 font-medium text-sm transition">
                        Basic Info
                    </button>
                    <button type="button" @click="activeTab = 'personal'"
                        :class="activeTab === 'personal' ? 'border-lime-500 text-lime-500' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-300'"
                        class="py-4 px-4 border-b-2 font-medium text-sm transition">
                        Personal & Emergency
                    </button>
                    <button type="button" @click="activeTab = 'employment'"
                        :class="activeTab === 'employment' ? 'border-lime-500 text-lime-500' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-300'"
                        class="py-4 px-4 border-b-2 font-medium text-sm transition">
                        Employment Details
                    </button>
                    <button type="button" @click="activeTab = 'contract'"
                        :class="activeTab === 'contract' ? 'border-lime-500 text-lime-500' : 'border-transparent text-slate-400 hover:text-slate-300'"
                        class="py-4 px-4 border-b-2 font-medium text-sm transition">
                        Contract & Salary
                    </button>
                    <button type="button" @click="activeTab = 'banking'"
                        :class="activeTab === 'banking' ? 'border-lime-500 text-lime-500' : 'border-transparent text-slate-400 hover:text-slate-300'"
                        class="py-4 px-4 border-b-2 font-medium text-sm transition">
                        Banking & Tax
                    </button>
                    <button type="button" @click="activeTab = 'leaves'"
                        :class="activeTab === 'leaves' ? 'border-lime-500 text-lime-500' : 'border-transparent text-slate-400 hover:text-slate-300'"
                        class="py-4 px-4 border-b-2 font-medium text-sm transition">
                        Leave Balances
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- Basic Info Tab -->
                <div x-show="activeTab === 'basic'" x-cloak>
                    <h3 class="text-lg font-semibold text-white mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-slate-400 mb-2">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name', $employee->name) }}" required
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                            @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Email *</label>
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}" required
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                            @error('email')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Status *</label>
                            <select name="status" required
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                                <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : ''
                                    }}>Active</option>
                                <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected'
                                    : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Company *</label>
                            <select name="company_id" id="company_id" required
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $employee->company_id) ==
                                    $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Department</label>
                            <select name="department_id" id="department_id"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}" data-company-id="{{ $department->company_id }}" {{
                                    old('department_id', $employee->department_id) == $department->id ? 'selected' : ''
                                    }}>
                                    {{ $department->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Designation/Position</label>
                            <input type="text" name="position" value="{{ old('position', $employee->position) }}"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Hire Date</label>
                            <input type="date" name="hire_date"
                                value="{{ old('hire_date', $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Personal & Emergency Tab -->
                <div x-show="activeTab === 'personal'" x-cloak>
                    <h3 class="text-lg font-semibold text-white mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-slate-400 mb-2">Date of Birth</label>
                            <input type="date" name="date_of_birth"
                                value="{{ old('date_of_birth', $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Gender</label>
                            <select name="gender"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : ''
                                    }}>Male</option>
                                <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : ''
                                    }}>Female</option>
                                <option value="other" {{ old('gender', $employee->gender) == 'other' ? 'selected' : ''
                                    }}>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Blood Group</label>
                            <input type="text" name="blood_group"
                                value="{{ old('blood_group', $employee->blood_group) }}" placeholder="e.g., A+, B-, O+"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Marital Status</label>
                            <select name="marital_status"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                                <option value="">Select Status</option>
                                <option value="single" {{ old('marital_status', $employee->marital_status) == 'single' ?
                                    'selected' : '' }}>Single</option>
                                <option value="married" {{ old('marital_status', $employee->marital_status) == 'married'
                                    ? 'selected' : '' }}>Married</option>
                                <option value="divorced" {{ old('marital_status', $employee->marital_status) ==
                                    'divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="widowed" {{ old('marital_status', $employee->marital_status) == 'widowed'
                                    ? 'selected' : '' }}>Widowed</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-slate-400 mb-2">Address</label>
                            <textarea name="address" rows="3"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">{{ old('address', $employee->address) }}</textarea>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-white mb-4">Emergency Contact</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-slate-400 mb-2">Contact Name</label>
                            <input type="text" name="emergency_contact_name"
                                value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Relationship</label>
                            <input type="text" name="emergency_contact_relationship"
                                value="{{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) }}"
                                placeholder="e.g., Spouse, Parent, Sibling"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Contact Phone</label>
                            <input type="text" name="emergency_contact_phone"
                                value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Employment Details Tab -->
                <div x-show="activeTab === 'employment'" x-cloak>
                    <h3 class="text-lg font-semibold text-white mb-4">Employment Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-slate-400 mb-2">Employment Type</label>
                            <select name="employment_type"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                                <option value="">Select Type</option>
                                <option value="full-time" {{ old('employment_type', $employee->employment_type) ==
                                    'full-time' ? 'selected' : '' }}>Full-Time</option>
                                <option value="part-time" {{ old('employment_type', $employee->employment_type) ==
                                    'part-time' ? 'selected' : '' }}>Part-Time</option>
                                <option value="contract" {{ old('employment_type', $employee->employment_type) ==
                                    'contract' ? 'selected' : '' }}>Contract</option>
                                <option value="intern" {{ old('employment_type', $employee->employment_type) == 'intern'
                                    ? 'selected' : '' }}>Intern</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Working Days per Week</label>
                            <input type="number" name="working_days_per_week"
                                value="{{ old('working_days_per_week', $employee->working_days_per_week) }}" min="1"
                                max="7"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Contract & Salary Tab -->
                <div x-show="activeTab === 'contract'" x-cloak>
                    <h3 class="text-lg font-semibold text-white mb-4">Salary Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-slate-400 mb-2">Salary Type</label>
                            <select name="salary_type"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                                <option value="">Select Type</option>
                                <option value="monthly" {{ old('salary_type', $employee->salary_type) == 'monthly' ?
                                    'selected' : '' }}>Monthly</option>
                                <option value="hourly" {{ old('salary_type', $employee->salary_type) == 'hourly' ?
                                    'selected' : '' }}>Hourly</option>
                                <option value="daily" {{ old('salary_type', $employee->salary_type) == 'daily' ?
                                    'selected' : '' }}>Daily</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Basic Salary Amount</label>
                            <input type="number" name="salary_amount"
                                value="{{ old('salary_amount', $employee->salary_amount) }}" step="0.01" min="0"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Allowances</label>
                            <input type="number" name="allowances"
                                value="{{ old('allowances', $employee->allowances) }}" step="0.01" min="0"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-white mb-4">Contract Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-slate-400 mb-2">Contract Start Date</label>
                            <input type="date" name="contract_start_date"
                                value="{{ old('contract_start_date', $employee->contract_start_date ? $employee->contract_start_date->format('Y-m-d') : '') }}"
                                class="bg-slate-900 text-white border-slate-700 focus:border-lime-500 rounded-lg w-full px-4 py-2" />
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Contract End Date</label>
                            <input type="date" name="contract_end_date"
                                value="{{ old('contract_end_date', $employee->contract_end_date ? $employee->contract_end_date->format('Y-m-d') : '') }}"
                                class="bg-slate-900 text-white border-slate-700 focus:border-lime-500 rounded-lg w-full px-4 py-2" />
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Probation Period (months)</label>
                            <input type="number" name="probation_period_months"
                                value="{{ old('probation_period_months', $employee->probation_period_months) }}" min="0"
                                max="12"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Banking & Tax Tab -->
                <div x-show="activeTab === 'banking'" x-cloak>
                    <h3 class="text-lg font-semibold text-white mb-4">Bank Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-slate-400 mb-2">Bank Name</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Account Number</label>
                            <input type="text" name="bank_account_number"
                                value="{{ old('bank_account_number', $employee->bank_account_number) }}"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Branch</label>
                            <input type="text" name="bank_branch"
                                value="{{ old('bank_branch', $employee->bank_branch) }}"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-white mb-4">Tax Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-slate-400 mb-2">PAN Number</label>
                            <input type="text" name="pan_number" value="{{ old('pan_number', $employee->pan_number) }}"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Tax Regime</label>
                            <input type="text" name="tax_regime" value="{{ old('tax_regime', $employee->tax_regime) }}"
                                placeholder="e.g., Old, New"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Leave Balances Tab -->
                <div x-show="activeTab === 'leaves'" x-cloak>
                    <h3 class="text-lg font-semibold text-white mb-4">Leave Balances</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-slate-400 mb-2">Sick Leave Balance (days)</label>
                            <input type="number" name="sick_leave_balance"
                                value="{{ old('sick_leave_balance', $employee->sick_leave_balance ?? 0) }}" min="0"
                                step="0.5"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Casual Leave Balance (days)</label>
                            <input type="number" name="casual_leave_balance"
                                value="{{ old('casual_leave_balance', $employee->casual_leave_balance ?? 0) }}" min="0"
                                step="0.5"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-2">Annual Leave Balance (days)</label>
                            <input type="number" name="annual_leave_balance"
                                value="{{ old('annual_leave_balance', $employee->annual_leave_balance ?? 0) }}" min="0"
                                step="0.5"
                                class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                        <p class="text-slate-400 text-sm">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Leave balances are typically managed through leave policies and requests. Manual adjustments
                            should be used carefully.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('admin.hrm.employees.show', $employee) }}"
                class="px-6 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2.5 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Update Employee
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const companySelect = document.getElementById('company_id');
        const departmentSelect = document.getElementById('department_id');
        
        // Store all department options
        const allDepartmentOptions = Array.from(departmentSelect.querySelectorAll('option[data-company-id]'));
        
        // Function to filter departments based on selected company
        function filterDepartments() {
            const selectedCompanyId = companySelect.value;
            
            // Remove all department options except the first "Select Department" option
            departmentSelect.querySelectorAll('option[data-company-id]').forEach(option => {
                option.remove();
            });
            
            if (selectedCompanyId) {
                // Add only departments that belong to the selected company
                allDepartmentOptions.forEach(option => {
                    if (option.getAttribute('data-company-id') === selectedCompanyId) {
                        departmentSelect.appendChild(option.cloneNode(true));
                    }
                });
            } else {
                // If no company selected, show all departments
                allDepartmentOptions.forEach(option => {
                    departmentSelect.appendChild(option.cloneNode(true));
                });
            }
            
            // Reset department selection if current selection is not in filtered list
            const currentDepartmentId = departmentSelect.value;
            const validOption = Array.from(departmentSelect.options).find(opt => opt.value === currentDepartmentId);
            if (!validOption && departmentSelect.options.length > 1) {
                departmentSelect.value = '';
            }
        }
        
        // Filter departments on page load
        filterDepartments();
        
        // Filter departments when company changes
        companySelect.addEventListener('change', function() {
            filterDepartments();
        });
    });
</script>
@endpush

@endsection