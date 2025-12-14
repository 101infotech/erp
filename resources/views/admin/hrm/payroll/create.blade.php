@extends('admin.layouts.app')

@section('title', 'Generate Payroll')
@section('page-title', 'Generate Payroll')

@section('content')
<div class="space-y-6 max-w-4xl">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-white">Generate Payroll</h1>
        <p class="text-slate-400 mt-1">Create payroll records for selected employees and period</p>
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
                <p class="text-red-300 mb-4">The following employees already have payroll records that overlap with the
                    selected period:</p>

                <div class="space-y-4">
                    @foreach(session('collisions') as $collision)
                    <div class="bg-slate-800/50 border border-red-500/20 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="font-semibold text-white">
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

                <div class="mt-4 p-3 bg-slate-900/50 rounded border border-slate-700">
                    <p class="text-sm text-slate-300">
                        <span class="font-medium text-white">Solution:</span>
                        You can either delete the existing draft payroll records, or select a different date range that
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

        <div class="bg-slate-800 rounded-lg p-6 border border-slate-700 space-y-6">
            <!-- Employee Selection -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Select Employees <span class="text-red-400">*</span>
                </label>
                <div class="mb-2">
                    <button type="button" onclick="selectAllEmployees()"
                        class="text-sm text-lime-400 hover:text-lime-300">
                        Select All
                    </button>
                    <span class="text-slate-600 mx-2">|</span>
                    <button type="button" onclick="deselectAllEmployees()"
                        class="text-sm text-slate-400 hover:text-slate-300">
                        Deselect All
                    </button>
                </div>
                <div class="bg-slate-900 border border-slate-700 rounded-lg p-4 max-h-64 overflow-y-auto">
                    @foreach($employees as $employee)
                    <label class="flex items-center py-2 hover:bg-slate-800 px-2 rounded cursor-pointer">
                        <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}"
                            class="w-4 h-4 text-lime-500 bg-slate-900 border-slate-600 rounded focus:ring-lime-500 focus:ring-2">
                        <span class="ml-3 text-sm text-white">
                            {{ $employee->name ?? ($employee->full_name ?? 'Unnamed Employee') }}
                            @if($employee->company)
                            <span class="text-slate-400">- {{ $employee->company->name }}</span>
                            @endif
                        </span>
                    </label>
                    @endforeach
                </div>
                @error('employee_ids')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Period Selection -->
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Period</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Start Date (AD) <span class="text-red-400">*</span>
                        </label>
                        <input type="date" name="period_start" value="{{ old('period_start') }}"
                            class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent"
                            required>
                        @error('period_start')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            End Date (AD) <span class="text-red-400">*</span>
                        </label>
                        <input type="date" name="period_end" value="{{ old('period_end') }}"
                            class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent"
                            required>
                        @error('period_end')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Payroll Configuration -->
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Payroll Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Month Total Days (BS Calendar)
                        </label>
                        <input type="number" name="month_total_days" value="{{ old('month_total_days') }}" min="29"
                            max="32" placeholder="Leave empty to auto-detect"
                            class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                        <p class="text-xs text-slate-400 mt-1">Nepali months have 29, 30, or 31 days. System will
                            auto-detect if not provided.</p>
                        @error('month_total_days')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Standard Working Hours per Day
                        </label>
                        <input type="number" step="0.5" name="standard_working_hours"
                            value="{{ old('standard_working_hours', '8.00') }}" min="1" max="24"
                            class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                        <p class="text-xs text-slate-400 mt-1">Default is 8 hours. Used to calculate hourly deductions.
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

<script>
    function selectAllEmployees() {
            document.querySelectorAll('input[name="employee_ids[]"]').forEach(cb => cb.checked = true);
        }
        
        function deselectAllEmployees() {
            document.querySelectorAll('input[name="employee_ids[]"]').forEach(cb => cb.checked = false);
        }
</script>
@endsection