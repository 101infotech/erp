@extends('admin.layouts.app')

@section('title', 'Edit Payroll')
@section('page-title', 'Edit Payroll')

@section('content')
<div class="space-y-6 max-w-4xl">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.hrm.payroll.index') }}" class="group">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Edit Payroll</h1>
            <p class="text-slate-400 text-sm mt-1">{{ $payroll->employee->full_name }} - {{ $payroll->period_start_bs }}
                to {{
                $payroll->period_end_bs }}</p>
        </div>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="{{ route('admin.hrm.payroll.update', $payroll->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Overtime Payment -->
        <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
            <h2 class="text-xl font-semibold text-white mb-4">Overtime Payment</h2>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Overtime Payment Amount (NPR)
                </label>
                <input type="number" step="0.01" min="0" name="overtime_payment"
                    value="{{ old('overtime_payment', $payroll->overtime_payment) }}"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                <p class="text-xs text-slate-400 mt-1">Overtime hours recorded: {{
                    number_format($payroll->overtime_hours, 1) }} hours</p>
                @error('overtime_payment')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Bonus -->
        <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
            <h2 class="text-xl font-semibold text-white mb-4">Bonus</h2>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Bonus Amount (NPR)
                </label>
                <input type="number" step="0.01" min="0" name="bonus_amount" value="{{ old('bonus_amount', 0) }}"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                <p class="text-xs text-slate-400 mt-1">Add performance bonus or any additional bonus amount</p>
                @error('bonus_amount')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Bonus Reason
                </label>
                <input type="text" name="bonus_reason" value="{{ old('bonus_reason', '') }}"
                    placeholder="e.g., Performance bonus, Festival bonus"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                @error('bonus_reason')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Allowances -->
        <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
            <h2 class="text-xl font-semibold text-white mb-4">Allowances</h2>
            <div id="allowances-container" class="space-y-3">
                @forelse(old('allowances', $payroll->allowances ?? []) as $index => $allowance)
                <div class="allowance-row flex gap-3">
                    <input type="text" name="allowances[{{ $index }}][name]" value="{{ $allowance['name'] ?? '' }}"
                        placeholder="Allowance Name"
                        class="flex-1 bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2">
                    <input type="number" step="0.01" min="0" name="allowances[{{ $index }}][amount]"
                        value="{{ $allowance['amount'] ?? 0 }}" placeholder="Amount"
                        class="w-32 bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2">
                    <button type="button" onclick="this.parentElement.remove()"
                        class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                        Remove
                    </button>
                </div>
                @empty
                <p class="text-slate-400 text-sm">No allowances added</p>
                @endforelse
            </div>
            <button type="button" onclick="addAllowance()"
                class="mt-3 px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
                Add Allowance
            </button>
        </div>

        <!-- Deductions -->
        <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
            <h2 class="text-xl font-semibold text-white mb-4">Deductions</h2>
            <div id="deductions-container" class="space-y-3">
                @forelse(old('deductions', $payroll->deductions ?? []) as $index => $deduction)
                <div class="deduction-row flex gap-3">
                    <input type="text" name="deductions[{{ $index }}][name]" value="{{ $deduction['name'] ?? '' }}"
                        placeholder="Deduction Name"
                        class="flex-1 bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2">
                    <input type="number" step="0.01" min="0" name="deductions[{{ $index }}][amount]"
                        value="{{ $deduction['amount'] ?? 0 }}" placeholder="Amount"
                        class="w-32 bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2">
                    <button type="button" onclick="this.parentElement.remove()"
                        class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                        Remove
                    </button>
                </div>
                @empty
                <p class="text-slate-400 text-sm">No deductions added</p>
                @endforelse
            </div>
            <button type="button" onclick="addDeduction()"
                class="mt-3 px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
                Add Deduction
            </button>
        </div>

        <!-- Tax Override -->
        <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
            <h2 class="text-xl font-semibold text-white mb-4">Tax Override</h2>
            <div class="space-y-4">
                <div class="bg-slate-900 border border-slate-700 rounded-lg p-4">
                    <p class="text-sm text-slate-400">Current Tax Amount (Calculated)</p>
                    <p class="text-xl font-semibold text-white">NPR {{ number_format($payroll->tax_amount, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Override Tax Amount (NPR) - Optional
                    </label>
                    <input type="number" step="0.01" min="0" name="tax_amount" value="{{ old('tax_amount') }}"
                        placeholder="Leave empty to keep calculated amount"
                        class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    @error('tax_amount')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Tax Override Reason <span class="text-red-400">*</span> (Required if overriding)
                    </label>
                    <textarea name="tax_override_reason" rows="3"
                        class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent"
                        placeholder="Explain why the tax amount is being overridden">{{ old('tax_override_reason') }}</textarea>
                    @error('tax_override_reason')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Working Hours Review -->
        <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
            <h2 class="text-xl font-semibold text-white mb-4">Working Hours Review</h2>
            <div class="space-y-4">
                <!-- Verbal Leave Days Adjustment -->
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4">
                    <label class="block text-sm font-medium text-blue-300 mb-2">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Verbal/Informal Leave Days
                    </label>
                    <input type="number" min="0" name="verbal_leave_days"
                        value="{{ old('verbal_leave_days', $payroll->verbal_leave_days ?? 0) }}"
                        class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-blue-200 mt-2">
                        <strong>For leaves approved verbally but not formally recorded:</strong><br>
                        Enter the number of days that were approved verbally. These days will be automatically excluded
                        from required hours calculation and won't count as missing hours.
                    </p>
                    <div class="mt-2 text-xs text-blue-100 bg-blue-500/10 rounded p-2">
                        <p><strong>Current Leave Status:</strong></p>
                        <ul class="list-disc ml-4 mt-1 space-y-0.5">
                            <li>Formally Recorded Paid Leave: {{ $payroll->paid_leave_days_used }} days</li>
                            <li>Formally Recorded Unpaid Leave: {{ $payroll->unpaid_leave_days }} days</li>
                            <li>Verbal/Informal Leave: {{ $payroll->verbal_leave_days ?? 0 }} days</li>
                            <li class="font-semibold">Absent Days (no leave): {{ $payroll->absent_days }} days</li>
                        </ul>
                    </div>
                    @error('verbal_leave_days')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hours Summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-slate-900 border border-slate-700 rounded-lg p-4">
                        <p class="text-sm text-slate-400">Required Hours</p>
                        <p class="text-xl font-semibold text-white">{{
                            number_format($payroll->total_working_hours_required, 1) }} hrs</p>
                        <p class="text-xs text-slate-500 mt-1">Excludes all leaves & weekends</p>
                    </div>
                    <div class="bg-slate-900 border border-slate-700 rounded-lg p-4">
                        <p class="text-sm text-slate-400">Actual Hours Worked</p>
                        <p class="text-xl font-semibold text-white">{{ number_format($payroll->total_hours_worked, 1) }}
                            hrs</p>
                    </div>
                    <div class="bg-slate-900 border border-slate-700 rounded-lg p-4">
                        <p class="text-sm text-slate-400">Missing Hours</p>
                        <p
                            class="text-xl font-semibold {{ $payroll->total_working_hours_missing > 0 ? 'text-red-400' : 'text-lime-400' }}">
                            {{ number_format($payroll->total_working_hours_missing, 1) }} hrs
                        </p>
                    </div>
                </div>

                @if($payroll->total_working_hours_missing > 0)
                <!-- Hourly Deduction -->
                <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-4">
                    <p class="text-sm text-yellow-400 mb-2">System Suggested Deduction</p>
                    <p class="text-2xl font-semibold text-yellow-300">NPR {{
                        number_format($payroll->hourly_deduction_suggested, 2) }}</p>
                    <p class="text-xs text-yellow-200 mt-1">Based on missing {{
                        number_format($payroll->total_working_hours_missing, 1) }} hours</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Hourly Deduction Amount (NPR)
                        </label>
                        <input type="number" step="0.01" min="0" name="hourly_deduction_amount"
                            value="{{ old('hourly_deduction_amount', $payroll->hourly_deduction_amount ?: $payroll->hourly_deduction_suggested) }}"
                            class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                        <p class="text-xs text-slate-400 mt-1">You can edit this amount or set to 0 to waive the
                            deduction</p>
                        @error('hourly_deduction_amount')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="hourly_deduction_approved" value="1" id="hourly_deduction_approved"
                            {{ old('hourly_deduction_approved', $payroll->hourly_deduction_approved) ? 'checked' : '' }}
                        class="w-4 h-4 text-lime-500 bg-slate-900 border-slate-600 rounded focus:ring-lime-500
                        focus:ring-2">
                        <label for="hourly_deduction_approved" class="ml-2 text-sm text-slate-300">
                            I approve this hourly deduction (uncheck to waive completely)
                        </label>
                    </div>
                </div>
                @else
                <div class="bg-lime-500/10 border border-lime-500/20 rounded-lg p-4">
                    <p class="text-lime-400">✓ No missing hours - No deduction required</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Advance Payment -->
        <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
            <h2 class="text-xl font-semibold text-white mb-4">Advance Payment</h2>
            <div class="space-y-4">
                @if($payroll->advance_payment > 0)
                <div class="bg-orange-500/10 border border-orange-500/20 rounded-lg p-4">
                    <p class="text-sm text-orange-400">Current Advance Recorded</p>
                    <p class="text-2xl font-semibold text-orange-300">NPR {{ number_format($payroll->advance_payment, 2)
                        }}</p>
                    @if($payroll->advance_payment_details)
                    <div class="mt-2 text-xs text-orange-200">
                        <p>Reason: {{ $payroll->advance_payment_details['reason'] ?? 'N/A' }}</p>
                        <p>Date: {{ $payroll->advance_payment_details['date'] ?? 'N/A' }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Advance Payment Amount (NPR)
                    </label>
                    <input type="number" step="0.01" min="0" name="advance_payment"
                        value="{{ old('advance_payment', $payroll->advance_payment) }}"
                        class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    <p class="text-xs text-slate-400 mt-1">Enter the advance salary amount taken by employee</p>
                    @error('advance_payment')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Reason for Advance
                    </label>
                    <input type="text" name="advance_payment_reason"
                        value="{{ old('advance_payment_reason', $payroll->advance_payment_details['reason'] ?? '') }}"
                        placeholder="e.g., Personal emergency, Medical expenses"
                        class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    @error('advance_payment_reason')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Date of Advance
                    </label>
                    <input type="date" name="advance_payment_date"
                        value="{{ old('advance_payment_date', $payroll->advance_payment_details['date'] ?? '') }}"
                        class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    @error('advance_payment_date')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between">
            <a href="{{ route('admin.hrm.payroll.show', $payroll->id) }}"
                class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
                Update Payroll
            </button>
        </div>
    </form>
</div>

<script>
    let allowanceIndex = {{ count(old('allowances', $payroll->allowances ?? [])) }};
        let deductionIndex = {{ count(old('deductions', $payroll->deductions ?? [])) }};

        function addAllowance() {
            const container = document.getElementById('allowances-container');
            const emptyMessage = container.querySelector('p.text-slate-400');
            if (emptyMessage) emptyMessage.remove();
            
            const row = document.createElement('div');
            row.className = 'allowance-row flex gap-3';
            row.innerHTML = `
                <input type="text" name="allowances[${allowanceIndex}][name]" 
                       placeholder="Allowance Name"
                       class="flex-1 bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2">
                <input type="number" step="0.01" min="0" name="allowances[${allowanceIndex}][amount]" 
                       placeholder="Amount"
                       class="w-32 bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2">
                <button type="button" onclick="this.parentElement.remove()" 
                        class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                    Remove
                </button>
            `;
            container.appendChild(row);
            allowanceIndex++;
        }

        function addDeduction() {
            const container = document.getElementById('deductions-container');
            const emptyMessage = container.querySelector('p.text-slate-400');
            if (emptyMessage) emptyMessage.remove();
            
            const row = document.createElement('div');
            row.className = 'deduction-row flex gap-3';
            row.innerHTML = `
                <input type="text" name="deductions[${deductionIndex}][name]" 
                       placeholder="Deduction Name"
                       class="flex-1 bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2">
                <input type="number" step="0.01" min="0" name="deductions[${deductionIndex}][amount]" 
                       placeholder="Amount"
                       class="w-32 bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2">
                <button type="button" onclick="this.parentElement.remove()" 
                        class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                    Remove
                </button>
            `;
            container.appendChild(row);
            deductionIndex++;
        }
</script>
@endsection