@extends('admin.layouts.app')

@section('title', 'New Leave Request')
@section('page-title', 'New Leave Request')

@section('content')
<div class="space-y-6 max-w-4xl">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.hrm.leaves.index') }}" class="group">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">New Leave Request</h1>
            <p class="text-slate-400 text-sm mt-1">Submit a leave request for an employee</p>
        </div>
    </div>

    <!-- Leave Request Form -->
    <form method="POST" action="{{ route('admin.hrm.leaves.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700 space-y-6">
            <!-- Employee Selection -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Employee <span class="text-red-400">*</span>
                </label>
                <select name="employee_id" id="employee_id" onchange="updateLeaveBalances()" required
                    class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" data-annual="{{ $employee->paid_leave_annual ?? 0 }}"
                        data-sick="{{ $employee->paid_leave_sick ?? 0 }}"
                        data-casual="{{ $employee->paid_leave_casual ?? 0 }}" {{ old('employee_id')==$employee->id ?
                        'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                    @endforeach
                </select>
                @error('employee_id')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Leave Balance Display -->
            <div id="leave-balances"
                class="hidden bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-3">Available Leave Balance</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-400" id="balance-annual">0.0</p>
                        <p class="text-xs text-slate-600 dark:text-slate-400">Annual</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-red-400" id="balance-sick">0.0</p>
                        <p class="text-xs text-slate-400">Sick</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-400" id="balance-casual">0.0</p>
                        <p class="text-xs text-slate-400">Casual</p>
                    </div>
                </div>
            </div>

            <!-- Leave Type -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Leave Type <span class="text-red-400">*</span>
                </label>
                <select name="leave_type" id="leave_type" required
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    <option value="">Select Type</option>
                    <option value="annual" {{ old('leave_type')==='annual' ? 'selected' : '' }} data-balance="annual">
                        Annual Leave (<span id="annual-count">0.0</span> available)
                    </option>
                    <option value="sick" {{ old('leave_type')==='sick' ? 'selected' : '' }} data-balance="sick">Sick
                        Leave (<span id="sick-count">0.0</span> available)</option>
                    <option value="casual" {{ old('leave_type')==='casual' ? 'selected' : '' }} data-balance="casual">
                        Casual Leave (<span id="casual-count">0.0</span> available)
                    </option>
                    <option value="unpaid" {{ old('leave_type')==='unpaid' ? 'selected' : '' }}>Unpaid Leave (Unlimited)
                    </option>
                </select>
                @error('leave_type')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Start Date <span class="text-red-400">*</span>
                    </label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                        class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    @error('start_date')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        End Date <span class="text-red-400">*</span>
                    </label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                        class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    @error('end_date')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Reason -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Reason <span class="text-red-400">*</span>
                </label>
                <textarea name="reason" rows="4" required maxlength="500"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent"
                    placeholder="Please provide reason for leave">{{ old('reason') }}</textarea>
                <p class="text-xs text-slate-400 mt-1">Maximum 500 characters</p>
                @error('reason')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
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
                            <p>• Weekends (Saturdays) are automatically excluded from leave calculation</p>
                            <p>• Leave balance will be deducted upon approval</p>
                            <p>• Unpaid leave does not require available balance</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between">
            <a href="{{ route('admin.hrm.leaves.index') }}"
                class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
                Submit Leave Request
            </button>
        </div>
    </form>
</div>

<script>
    function updateLeaveBalances() {
            const select = document.getElementById('employee_id');
            const balancesDiv = document.getElementById('leave-balances');
            
            if (select.value) {
                const option = select.options[select.selectedIndex];
                const annual = parseFloat(option.dataset.annual || 0).toFixed(1);
                const sick = parseFloat(option.dataset.sick || 0).toFixed(1);
                const casual = parseFloat(option.dataset.casual || 0).toFixed(1);
                
                // Update balance display
                document.getElementById('balance-annual').textContent = annual;
                document.getElementById('balance-sick').textContent = sick;
                document.getElementById('balance-casual').textContent = casual;
                
                // Update leave type dropdown counts
                document.getElementById('annual-count').textContent = annual;
                document.getElementById('sick-count').textContent = sick;
                document.getElementById('casual-count').textContent = casual;
                
                balancesDiv.classList.remove('hidden');
            } else {
                balancesDiv.classList.add('hidden');
                // Reset counts
                document.getElementById('annual-count').textContent = '0.0';
                document.getElementById('sick-count').textContent = '0.0';
                document.getElementById('casual-count').textContent = '0.0';
            }
        }

        // Update on page load if employee is pre-selected
        document.addEventListener('DOMContentLoaded', updateLeaveBalances);
</script>
@endsection