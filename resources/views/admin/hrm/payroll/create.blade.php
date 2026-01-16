@extends('admin.layouts.app')

@section('title', 'Generate Payroll')
@section('page-title', 'Generate Payroll')

@section('content')
<div class="px-6 md:px-8 py-6">
    <div class="max-w-4xl space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.hrm.payroll.index') }}" class="group">
                <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Generate Payroll</h1>
                <p class="text-slate-400 text-sm mt-1">Create payroll records for selected employees and period</p>
            </div>
        </div>

        <!-- Collision Warning -->
        @if(session('collision_error') && session('collisions'))
        <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-6">
            <div class="flex items-start">
                <svg class="h-6 w-6 text-red-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-red-400 mb-3">Date Collision Detected!</h3>
                    <p class="text-red-300 mb-4">The following employees already have payroll records that overlap with
                        the
                        selected period:</p>

                    <div class="space-y-4">
                        @foreach(session('collisions') as $collision)
                        <div class="bg-white/5 dark:bg-slate-800/50 border border-red-500/20 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h4 class="font-semibold text-slate-900 dark:text-white">
                                        {{ $collision['employee_name'] }}
                                        @if($collision['employee_code'])
                                        <span class="text-slate-400 text-sm">({{ $collision['employee_code'] }})</span>
                                        @endif
                                    </h4>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <p class="text-sm text-slate-300 font-medium">Existing Payroll Records:</p>
                                @foreach($collision['existing_payrolls'] as $existing)
                                <div class="bg-slate-900/50 rounded p-3 border border-slate-700">
                                    <div class="flex items-center justify-between">
                                        <div class="space-y-1">
                                            <p class="text-sm text-white">
                                                <span class="font-medium">Period (BS):</span>
                                                {{ $existing['period_start_bs'] }} to {{ $existing['period_end_bs'] }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                <span class="font-medium">Period (AD):</span>
                                                {{ $existing['period_start_ad'] }} to {{ $existing['period_end_ad'] }}
                                            </p>
                                            <p class="text-xs">
                                                <span
                                                    class="px-2 py-1 rounded text-xs font-medium
                                                {{ $existing['status'] == 'draft' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                                {{ $existing['status'] == 'approved' ? 'bg-green-500/20 text-green-400' : '' }}
                                                {{ $existing['status'] == 'paid' ? 'bg-blue-500/20 text-blue-400' : '' }}">
                                                    {{ ucfirst($existing['status']) }}
                                                </span>
                                            </p>
                                        </div>
                                        @if($existing['status'] == 'draft')
                                        <a href="{{ route('admin.hrm.payroll.show', $existing['id']) }}"
                                            class="px-3 py-1 bg-slate-700 hover:bg-slate-600 text-white text-sm rounded transition">
                                            View/Delete
                                        </a>
                                        @else
                                        <a href="{{ route('admin.hrm.payroll.show', $existing['id']) }}"
                                            class="px-3 py-1 bg-lime-500/20 hover:bg-lime-500/30 text-lime-400 text-sm rounded transition">
                                            View Details
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div
                        class="mt-4 p-3 bg-slate-50 dark:bg-slate-900/50 rounded border border-slate-200 dark:border-slate-700">
                        <p class="text-sm text-slate-700 dark:text-slate-300">
                            <span class="font-medium text-slate-900 dark:text-white">Solution:</span>
                            You can either delete the existing draft payroll records, or select a different date range
                            that
                            doesn't overlap.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Generation Form -->
        <form method="POST" action="{{ route('admin.hrm.payroll.store') }}" class="space-y-6">
            @csrf

            <div
                class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700 space-y-6">
                <!-- Employee Selection -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Select Employees <span class="text-red-400">*</span>
                        <span class="text-slate-500 dark:text-slate-400 text-xs ml-2">({{ count($employees) }} active
                            employees)</span>
                    </label>

                    @if(isset($employeesWithoutSalary) && $employeesWithoutSalary > 0)
                    <div class="mb-3 p-3 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start">
                                <svg class="h-5 w-5 text-yellow-400 mt-0.5 flex-shrink-0" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-300">
                                        <strong>{{ $employeesWithoutSalary }}</strong> employee(s) don't have basic
                                        salary
                                        configured.
                                        Payroll generation will fail for them. Employees without salary are marked with
                                        ⚠️
                                    </p>
                                </div>
                            </div>
                            <button type="button" onclick="openSalaryConfigModal()"
                                class="ml-4 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-slate-900 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Quick Fix
                            </button>
                        </div>
                    </div>
                    @endif

                    <div class="mb-2">
                        <button type="button" onclick="selectAllEmployees()"
                            class="text-sm text-lime-400 hover:text-lime-300">
                            Select All
                        </button>
                        <span class="text-slate-600 mx-2">|</span>
                        <button type="button" onclick="selectEmployeesWithSalary()"
                            class="text-sm text-lime-400 hover:text-lime-300">
                            Select Only With Salary
                        </button>
                        <span class="text-slate-600 mx-2">|</span>
                        <button type="button" onclick="deselectAllEmployees()"
                            class="text-sm text-slate-400 hover:text-slate-300">
                            Deselect All
                        </button>
                    </div>
                    <div
                        class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg p-4 max-h-64 overflow-y-auto">
                        @forelse($employees as $employee)
                        @php
                        $hasSalary = isset($employee->basic_salary_npr) && $employee->basic_salary_npr > 0;
                        @endphp
                        <label data-has-salary="{{ $hasSalary ? 'true' : 'false' }}"
                            class="flex items-center py-2 hover:bg-slate-100 dark:hover:bg-slate-800 px-2 rounded cursor-pointer {{ !$hasSalary ? 'opacity-60' : '' }}">
                            <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}"
                                class="w-4 h-4 text-lime-500 bg-slate-50 dark:bg-slate-900 border-slate-300 dark:border-slate-600 rounded focus:ring-lime-500 focus:ring-2">
                            <span class="ml-3 text-sm text-slate-900 dark:text-white">
                                {{ $employee->name ?? ($employee->full_name ?? 'Unnamed Employee') }}
                                @if($employee->company)
                                <span class="text-slate-500 dark:text-slate-400">- {{ $employee->company->name }}</span>
                                @endif
                                @if(!$hasSalary)
                                <span class="text-yellow-400 text-xs ml-2" title="No basic salary configured">⚠️ No
                                    Salary</span>
                                @else
                                <span class="text-slate-500 text-xs ml-2">(NPR {{
                                    number_format($employee->basic_salary_npr,
                                    2) }})</span>
                                @endif
                            </span>
                        </label>
                        @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-slate-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <p class="mt-4 text-slate-400 text-sm">No active employees found</p>
                            <p class="mt-1 text-slate-500 text-xs">
                                Please add active employees before generating payroll
                            </p>
                        </div>
                        @endforelse
                    </div>
                    @error('employee_ids')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Period Selection -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Period</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Start Date (AD) <span class="text-red-400">*</span>
                            </label>
                            <input type="date" name="period_start" value="{{ old('period_start') }}"
                                class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent"
                                required>
                            @error('period_start')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                End Date (AD) <span class="text-red-400">*</span>
                            </label>
                            <input type="date" name="period_end" value="{{ old('period_end') }}"
                                class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent"
                                required>
                            @error('period_end')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Payroll Configuration -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Payroll Configuration</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Month Total Days (BS Calendar)
                            </label>
                            <input type="number" name="month_total_days" value="{{ old('month_total_days') }}" min="29"
                                max="32" placeholder="Leave empty to auto-detect"
                                class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Nepali months have 29, 30, or 31
                                days. System will
                                auto-detect if not provided.</p>
                            @error('month_total_days')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Standard Working Hours per Day
                            </label>
                            <input type="number" step="0.5" name="standard_working_hours"
                                value="{{ old('standard_working_hours', '8.00') }}" min="1" max="24"
                                class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Default is 8 hours. Used to
                                calculate
                                hourly deductions.
                            </p>
                            @error('standard_working_hours')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-400">Important Notes</h3>
                            <div class="mt-2 text-sm text-blue-300 space-y-1">
                                <p>• Payroll will be generated in draft status for review and editing</p>
                                <p>• Daily pay is calculated based on BS month total days (29/30/31)</p>
                                <p>• System tracks working hours and suggests deductions for missing hours</p>
                                <p>• Paid leaves are counted as worked days (employees get paid)</p>
                                <p>• Overtime payment defaults to 0 and can be edited manually</p>
                                <p>• Anomalies will be automatically detected and must be reviewed before approval</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between">
                <a href="{{ route('admin.hrm.payroll.index') }}"
                    class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
                    Generate Payroll
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Salary Configuration Modal -->
    <div id="salaryConfigModal" class="fixed inset-0 z-50 hidden" style="background-color: rgba(0, 0, 0, 0.75);">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl"
                style="max-height: 90vh; display: flex; flex-direction: column;">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between"
                    style="flex-shrink: 0;">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Quick Salary Configuration
                    </h3>
                    <button type="button" onclick="closeSalaryConfigModal()"
                        class="text-slate-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body - Scrollable -->
                <div class="p-6" style="flex: 1; overflow-y: auto; min-height: 0;">
                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="ml-3 text-sm text-blue-300">
                                <p class="font-semibold mb-1">Configure salary for employees without basic salary</p>
                                <p class="text-blue-200/80">You can set individual salaries here or bulk update all at
                                    once.
                                    Changes are saved immediately.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Update Section -->
                    <div class="bg-slate-700/50 rounded-lg p-4 mb-6">
                        <h4 class="text-white font-semibold mb-3">Bulk Update All Employees</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-300 text-sm mb-2">Default Salary (NPR)</label>
                                <input type="number" id="bulkSalaryAmount" step="100" min="0" placeholder="e.g., 50000"
                                    class="w-full bg-slate-900 text-white border border-slate-600 rounded-lg px-4 py-2 focus:border-lime-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-300 text-sm mb-2">Salary Type</label>
                                <select id="bulkSalaryType"
                                    class="w-full bg-slate-900 text-white border border-slate-600 rounded-lg px-4 py-2 focus:border-lime-500 focus:outline-none">
                                    <option value="monthly">Monthly</option>
                                    <option value="hourly">Hourly</option>
                                    <option value="daily">Daily</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" onclick="bulkUpdateSalaries()"
                            class="mt-3 w-full px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
                            Apply to All Employees Without Salary
                        </button>
                    </div>

                    <!-- Individual Employee Configuration -->
                    <div>
                        <h4 class="text-white font-semibold mb-3">Individual Configuration</h4>
                        <div id="employeeSalaryList" class="space-y-3">
                            @foreach($employees as $employee)
                            @if(!isset($employee->basic_salary_npr) || $employee->basic_salary_npr <= 0) <div
                                class="bg-slate-700/30 rounded-lg p-4 employee-salary-row"
                                data-employee-id="{{ $employee->id }}">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <p class="text-white font-medium">{{ $employee->name }}</p>
                                        <p class="text-slate-400 text-sm">{{ $employee->company->name ?? 'No Company' }}
                                        </p>
                                    </div>
                                    <span class="text-yellow-400 text-xs">⚠️ No Salary</span>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="number" placeholder="Salary Amount" step="100" min="0"
                                        class="employee-salary-amount bg-slate-900 text-white border border-slate-600 rounded px-3 py-2 text-sm focus:border-lime-500 focus:outline-none"
                                        data-employee-id="{{ $employee->id }}">
                                    <select
                                        class="employee-salary-type bg-slate-900 text-white border border-slate-600 rounded px-3 py-2 text-sm focus:border-lime-500 focus:outline-none"
                                        data-employee-id="{{ $employee->id }}">
                                        <option value="monthly">Monthly</option>
                                        <option value="hourly">Hourly</option>
                                        <option value="daily">Daily</option>
                                    </select>
                                </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-700 flex items-center justify-between" style="flex-shrink: 0;">
                <button type="button" onclick="closeSalaryConfigModal()"
                    class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
                    Cancel
                </button>
                <button type="button" onclick="saveIndividualSalaries()"
                    class="px-6 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
                    Save Individual Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function selectAllEmployees() {
        document.querySelectorAll('input[name="employee_ids[]"]').forEach(cb => cb.checked = true);
    }
    
    function selectEmployeesWithSalary() {
        document.querySelectorAll('label[data-has-salary="true"] input[name="employee_ids[]"]').forEach(cb => cb.checked = true);
        document.querySelectorAll('label[data-has-salary="false"] input[name="employee_ids[]"]').forEach(cb => cb.checked = false);
    }
    
    function deselectAllEmployees() {
        document.querySelectorAll('input[name="employee_ids[]"]').forEach(cb => cb.checked = false);
    }
    
    function openSalaryConfigModal() {
        const modal = document.getElementById('salaryConfigModal');
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    function closeSalaryConfigModal() {
        const modal = document.getElementById('salaryConfigModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    async function bulkUpdateSalaries() {
        const amount = document.getElementById('bulkSalaryAmount').value;
        const type = document.getElementById('bulkSalaryType').value;
        
        if (!amount || amount <= 0) {
            alert('Please enter a valid salary amount');
            return;
        }
        
        const employeeIds = [];
        document.querySelectorAll('.employee-salary-row').forEach(row => {
            employeeIds.push(row.dataset.employeeId);
        });
        
        if (employeeIds.length === 0) {
            alert('No employees found without salary');
            return;
        }
        
        if (!confirm(`Are you sure you want to set ${type} salary of NPR ${amount} for ${employeeIds.length} employee(s)?`)) {
            return;
        }
        
        try {
            const response = await fetch('{{ route("admin.hrm.employees.bulk-update-salary") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    employee_ids: employeeIds,
                    salary_amount: parseFloat(amount),
                    salary_type: type
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                window.location.reload();
            } else {
                alert('Error: ' + (result.message || 'Failed to update salaries'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while updating salaries. Please try again.');
        }
    }
    
    async function saveIndividualSalaries() {
        const updates = [];
        
        document.querySelectorAll('.employee-salary-row').forEach(row => {
            const employeeId = row.dataset.employeeId;
            const amountInput = row.querySelector('.employee-salary-amount');
            const typeSelect = row.querySelector('.employee-salary-type');
            
            if (amountInput.value && amountInput.value > 0) {
                updates.push({
                    employee_id: employeeId,
                    salary_amount: parseFloat(amountInput.value),
                    salary_type: typeSelect.value
                });
            }
        });
        
        if (updates.length === 0) {
            alert('No salary amounts entered. Please enter at least one salary.');
            return;
        }
        
        try {
            const response = await fetch('{{ route("admin.hrm.employees.update-individual-salaries") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ updates })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                window.location.reload();
            } else {
                alert('Error: ' + (result.message || 'Failed to update salaries'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while updating salaries. Please try again.');
        }
    }
    
    // Close modal on outside click
    document.getElementById('salaryConfigModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeSalaryConfigModal();
        }
    });
    
    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSalaryConfigModal();
        }
    });
</script>
@endsection