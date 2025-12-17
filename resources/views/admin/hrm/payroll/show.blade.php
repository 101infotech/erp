@extends('admin.layouts.app')

@section('title', 'Payslip Details')
@section('page-title', 'Payslip Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white">Payslip Details</h1>
            <p class="text-slate-400 mt-1">{{ $payroll->employee->full_name }} - {{ $payroll->period_start_bs }} to
                {{ $payroll->period_end_bs }}</p>
        </div>
        <div class="flex flex-wrap gap-2 items-start">
            @if($payroll->payslip_pdf_path || $payroll->status !== 'draft')
            <a href="{{ route('admin.hrm.payroll.download-pdf', $payroll->id) }}"
                class="px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Download PDF
            </a>
            @endif
            @if($payroll->status === 'draft')
            <a href="{{ route('admin.hrm.payroll.edit', $payroll->id) }}"
                class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition whitespace-nowrap">
                Edit
            </a>
            @if(empty($payroll->anomalies) || $payroll->anomalies_reviewed)
            <button type="button" onclick="openModal('approvePayrollModal')"
                class="px-4 py-2.5 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition whitespace-nowrap">
                Approve
            </button>
            @endif
            <button type="button" onclick="openModal('deletePayrollModal')"
                class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete
            </button>
            @elseif($payroll->status === 'approved')
            <button type="button" onclick="openModal('markAsPaidModal')"
                class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition whitespace-nowrap">
                Mark as Paid
            </button>
            @if($payroll->employee->email)
            <button type="button" onclick="openModal('sendPayslipApprovedModal')"
                class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                @if($payroll->sent_at)
                Resend to Employee
                @else
                Send to Employee
                @endif
            </button>
            @endif
            @elseif($payroll->status === 'paid')
            @if($payroll->employee->email)
            <button type="button" onclick="openModal('sendPayslipPaidModal')"
                class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                @if($payroll->sent_at)
                Resend to Employee
                @else
                Send to Employee
                @endif
            </button>
            @endif
            @endif
            <a href="{{ route('admin.hrm.payroll.index') }}"
                class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition whitespace-nowrap">
                Back to List
            </a>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="flex items-center gap-3">
        @if($payroll->status === 'draft')
        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-500/20 text-yellow-400">Draft</span>
        @elseif($payroll->status === 'approved')
        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-500/20 text-blue-400">Approved</span>
        @elseif($payroll->status === 'paid')
        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-500/20 text-green-400">Paid</span>
        @endif

        @if($payroll->tax_overridden)
        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-orange-500/20 text-orange-400">Tax
            Override</span>
        @endif

        @if($payroll->sent_at)
        <span
            class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-500/20 text-purple-400 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Sent {{ $payroll->sent_at->diffForHumans() }}
        </span>
        @endif
    </div>

    <!-- Anomalies Warning -->
    @if(!empty($payroll->anomalies) && !$payroll->anomalies_reviewed && $payroll->status === 'draft')
    <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4">
        <div class="flex justify-between items-start">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-400">Attendance Anomalies Detected</h3>
                    <p class="mt-1 text-sm text-red-300">This payroll has {{ count($anomalies) }} attendance
                        anomalies that need review before approval.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.hrm.payroll.review-anomalies', $payroll->id) }}">
                @csrf
                <button type="submit"
                    class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded transition">
                    Mark as Reviewed
                </button>
            </form>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Attendance Summary -->
            <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
                <h2 class="text-xl font-semibold text-white mb-4">Attendance Summary</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center bg-slate-900/50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-lime-400">{{ number_format($payroll->total_hours_worked, 1) }}
                        </p>
                        <p class="text-sm text-slate-400">Total Hours</p>
                    </div>
                    <div class="text-center bg-slate-900/50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-lime-400">{{ $payroll->total_days_worked }}</p>
                        <p class="text-sm text-slate-400">Days Worked</p>
                    </div>
                    <div class="text-center bg-slate-900/50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-orange-400">{{ number_format($payroll->overtime_hours, 1) }}
                        </p>
                        <p class="text-sm text-slate-400">OT Hours</p>
                    </div>
                    <div class="text-center bg-slate-900/50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-red-400">{{ $payroll->absent_days }}</p>
                        <p class="text-sm text-slate-400">Absent Days</p>
                    </div>
                    <div class="text-center bg-slate-900/50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-yellow-400">{{ number_format($payroll->unpaid_leave_days, 1)
                            }}</p>
                        <p class="text-sm text-slate-400">Unpaid Leave</p>
                    </div>
                    <div class="text-center bg-slate-900/50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-blue-400">{{ $payroll->paid_leave_days_used ?? 0 }}</p>
                        <p class="text-sm text-slate-400">Paid Leave</p>
                    </div>
                    @if(($payroll->verbal_leave_days ?? 0) > 0)
                    <div class="text-center bg-slate-900/50 rounded-lg p-3 col-span-2 border border-cyan-500/30">
                        <p class="text-2xl font-bold text-cyan-400">{{ $payroll->verbal_leave_days }}</p>
                        <p class="text-sm text-cyan-300">Verbal/Informal Leave Days</p>
                        <p class="text-xs text-cyan-200 mt-1">(Excluded from required hours)</p>
                    </div>
                    @endif
                </div>
                <!-- Salary Calculation Breakdown -->
                <div class="mt-4 pt-4 border-t border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-300 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Salary Calculation Breakdown
                    </h3>
                    <div class="bg-blue-500/5 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-400">Monthly Contract Salary</span>
                            <span class="text-white font-semibold">NPR {{
                                number_format($payroll->employee->basic_salary_npr ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-400">Days in Month</span>
                            <span class="text-slate-300 font-medium">{{ $payroll->month_total_days ?? 0 }}</span>
                        </div>
                        @if($payroll->per_day_rate > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-400">Per Day Rate</span>
                            <span class="text-slate-300 font-medium">NPR {{ number_format($payroll->per_day_rate, 2)
                                }}</span>
                        </div>
                        @endif

                        <div class="h-px bg-slate-600 my-2"></div>

                        <!-- Days Breakdown -->
                        <div class="bg-slate-900/50 rounded px-3 py-2 space-y-1 text-sm">
                            <p class="font-semibold text-blue-300 mb-2">Days Breakdown:</p>
                            @php
                            $daysWorked = $payroll->total_days_worked ?? 0;
                            $absentDays = $payroll->absent_days ?? 0;
                            $paidLeaveDays = $payroll->paid_leave_days_used ?? 0;
                            $unpaidLeaveDays = $payroll->unpaid_leave_days ?? 0;
                            $totalAccountedDays = $daysWorked + $absentDays + $paidLeaveDays + $unpaidLeaveDays;
                            $weekendDays = max(0, ($payroll->month_total_days ?? 0) - $totalAccountedDays);
                            @endphp
                            <div class="flex justify-between">
                                <span class="text-slate-400">Days Worked:</span>
                                <span class="text-green-400">{{ $daysWorked }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Absent Days:</span>
                                <span class="text-red-400">{{ $absentDays }}</span>
                            </div>
                            @if($paidLeaveDays > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-400">Paid Leave Days:</span>
                                <span class="text-blue-400">{{ $paidLeaveDays }}</span>
                            </div>
                            @endif
                            @if($unpaidLeaveDays > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-400">Unpaid Leave Days:</span>
                                <span class="text-orange-400">{{ $unpaidLeaveDays }}</span>
                            </div>
                            @endif
                            @if(($payroll->verbal_leave_days ?? 0) > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-400">Verbal Leave Days:</span>
                                <span class="text-cyan-400 font-medium">{{ $payroll->verbal_leave_days }}</span>
                            </div>
                            @endif
                            @if($weekendDays > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-400">Weekends/Saturdays (Paid):</span>
                                <span class="text-purple-400">{{ $weekendDays }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="h-px bg-slate-600 my-2"></div>

                        @if($payroll->total_payable_days > 0)
                        <div class="flex justify-between items-center bg-slate-900/50 rounded px-3 py-2">
                            <span class="text-sm font-semibold text-white">Total Payable Days</span>
                            <span class="text-lime-400 font-bold">{{ $payroll->total_payable_days }} days</span>
                        </div>
                        <div class="text-xs text-slate-500 italic px-2 py-1">
                            = {{ $daysWorked }} (worked) + {{ $paidLeaveDays }} (paid leave) + {{ $weekendDays }}
                            (weekends) = {{ $payroll->total_payable_days }} days
                        </div>
                        @endif
                        <div class="h-px bg-slate-600 my-2"></div>
                        <div class="flex justify-between items-center bg-slate-900/50 rounded px-3 py-2">
                            <span class="text-white font-semibold">Calculated Base Salary</span>
                            <span class="text-lime-400 font-bold">NPR {{ number_format($payroll->basic_salary, 2)
                                }}</span>
                        </div>
                        <div class="text-xs text-slate-500 italic px-2">
                            Formula: {{ number_format($payroll->per_day_rate ?? 0, 2) }} × {{
                            $payroll->total_payable_days ?? 0 }} = NPR {{ number_format($payroll->basic_salary, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Left Column -->
        <div class="space-y-6">
            <!-- Employee & Period Info -->
            <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
                <h2 class="text-xl font-semibold text-white mb-4">Employee Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-slate-400">Employee Name</p>
                        <p class="text-white font-medium">{{ $payroll->employee->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Employee Code</p>
                        <p class="text-white font-medium">{{ $payroll->employee->employee_code }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Company</p>
                        <p class="text-white font-medium">{{ $payroll->employee->company->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Department</p>
                        <p class="text-white font-medium">{{ $payroll->employee->department->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Period</p>
                        <p class="text-white font-medium">{{ format_nepali_date($payroll->period_start_bs, 'j F Y') }} -
                            {{
                            format_nepali_date($payroll->period_end_bs, 'j F Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Salary Breakdown -->
            <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
                <h2 class="text-xl font-semibold text-white mb-4">Salary Breakdown</h2>
                <div class="space-y-3">
                    <!-- Earnings -->
                    <div class="border-b border-slate-700 pb-3">
                        <div class="flex justify-between text-slate-300">
                            <span>Basic Salary</span>
                            <span class="font-medium">NPR {{ number_format($payroll->basic_salary, 2) }}</span>
                        </div>
                    </div>

                    <div class="border-b border-slate-700 pb-3">
                        <div class="flex justify-between text-slate-300">
                            <span>Overtime Payment (Manual)</span>
                            <span class="font-medium">NPR {{ number_format($payroll->overtime_payment, 2) }}</span>
                        </div>
                    </div>

                    @if(!empty($payroll->allowances))
                    <div class="border-b border-slate-700 pb-3">
                        <p class="text-slate-400 text-sm mb-2">Allowances:</p>
                        @foreach($payroll->allowances as $allowance)
                        <div class="flex justify-between text-slate-300 text-sm ml-4">
                            <span>{{ $allowance['name'] ?? 'Allowance' }}</span>
                            <span>NPR {{ number_format($allowance['amount'], 2) }}</span>
                        </div>
                        @endforeach
                        <div class="flex justify-between text-slate-300 font-medium mt-2">
                            <span>Total Allowances</span>
                            <span>NPR {{ number_format($payroll->allowances_total, 2) }}</span>
                        </div>
                    </div>
                    @endif

                    <div class="border-b border-slate-700 pb-3">
                        <div class="flex justify-between text-lime-400 font-semibold">
                            <span>Gross Salary</span>
                            <span>NPR {{ number_format($payroll->gross_salary, 2) }}</span>
                        </div>
                    </div>

                    <!-- Deductions -->
                    <div class="border-b border-slate-700 pb-3">
                        <div class="flex justify-between text-slate-300">
                            <span>Tax Amount @if($payroll->tax_overridden)<span
                                    class="text-orange-400 text-xs">(Override)</span>@endif</span>
                            <span class="font-medium text-red-400">- NPR {{ number_format($payroll->tax_amount, 2)
                                }}</span>
                        </div>
                        @if($payroll->tax_overridden && $payroll->tax_override_reason)
                        <p class="text-xs text-slate-400 mt-1 ml-4">Reason: {{ $payroll->tax_override_reason }}</p>
                        @endif
                    </div>

                    @if(!empty($payroll->deductions))
                    <div class="border-b border-slate-700 pb-3">
                        <p class="text-slate-400 text-sm mb-2">Other Deductions:</p>
                        @foreach($payroll->deductions as $deduction)
                        <div class="flex justify-between text-slate-300 text-sm ml-4">
                            <span>{{ $deduction['name'] ?? 'Deduction' }}</span>
                            <span class="text-red-400">- NPR {{ number_format($deduction['amount'], 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($payroll->unpaid_leave_deduction > 0)
                    <div class="border-b border-slate-700 pb-3">
                        <div class="flex justify-between text-slate-300">
                            <span>Unpaid Leave Deduction ({{ $payroll->unpaid_leave_days }} days)</span>
                            <span class="font-medium text-red-400">- NPR {{
                                number_format($payroll->unpaid_leave_deduction, 2) }}</span>
                        </div>
                        @if($payroll->per_day_rate > 0 && $payroll->unpaid_leave_days > 0)
                        <p class="text-xs text-slate-500 mt-1 ml-4">
                            {{ number_format($payroll->per_day_rate, 2) }} × {{ $payroll->unpaid_leave_days }} days
                        </p>
                        @endif
                    </div>
                    @endif

                    @if($payroll->hourly_deduction_approved && $payroll->hourly_deduction_amount > 0)
                    <div class="border-b border-slate-700 pb-3">
                        <div class="flex justify-between text-slate-300">
                            <span>Hourly Deduction ({{ number_format($payroll->total_working_hours_missing, 1) }} hrs
                                missing)</span>
                            <span class="font-medium text-red-400">- NPR {{
                                number_format($payroll->hourly_deduction_amount, 2) }}</span>
                        </div>
                    </div>
                    @endif

                    @if($payroll->advance_payment > 0)
                    <div class="border-b border-slate-700 pb-3">
                        <div class="flex justify-between text-slate-300">
                            <span>Advance Payment Deduction</span>
                            <span class="font-medium text-red-400">- NPR {{ number_format($payroll->advance_payment, 2)
                                }}</span>
                        </div>
                        @if($payroll->advance_payment_details && isset($payroll->advance_payment_details['reason']))
                        <p class="text-xs text-slate-400 mt-1 ml-4">Reason: {{
                            $payroll->advance_payment_details['reason'] }}</p>
                        @endif
                    </div>
                    @endif

                    <!-- Net Salary -->
                    <div class="pt-3">
                        <div class="flex justify-between text-xl font-bold">
                            <span class="text-white">Net Salary</span>
                            <span class="text-lime-400">NPR {{ number_format($payroll->net_salary, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Working Hours Review -->
            @if($payroll->total_working_hours_required > 0)
            <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
                <h2 class="text-xl font-semibold text-white mb-4">Working Hours Review</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-slate-900 border border-slate-700 rounded-lg p-3 text-center">
                            <p class="text-sm text-slate-400">Required</p>
                            <p class="text-lg font-bold text-white">{{
                                number_format($payroll->total_working_hours_required, 1) }} hrs</p>
                        </div>
                        <div class="bg-slate-900 border border-slate-700 rounded-lg p-3 text-center">
                            <p class="text-sm text-slate-400">Actual</p>
                            <p class="text-lg font-bold text-lime-400">{{ number_format($payroll->total_hours_worked, 1)
                                }} hrs</p>
                        </div>
                        <div class="bg-slate-900 border border-slate-700 rounded-lg p-3 text-center">
                            <p class="text-sm text-slate-400">Missing</p>
                            <p
                                class="text-lg font-bold {{ $payroll->total_working_hours_missing > 0 ? 'text-red-400' : 'text-lime-400' }}">
                                {{ number_format($payroll->total_working_hours_missing, 1) }} hrs
                            </p>
                        </div>
                    </div>

                    @if($payroll->hourly_deduction_approved && $payroll->hourly_deduction_amount > 0)
                    <div class="bg-orange-500/10 border border-orange-500/20 rounded-lg p-3">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-orange-400">Hourly Deduction Applied</p>
                                <p class="text-xs text-orange-300 mt-1">
                                    Based on {{ number_format($payroll->total_working_hours_missing, 1) }} missing hours
                                </p>
                            </div>
                            <p class="text-lg font-bold text-orange-400">NPR {{
                                number_format($payroll->hourly_deduction_amount, 2) }}</p>
                        </div>
                    </div>
                    @elseif($payroll->total_working_hours_missing > 0)
                    <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-3">
                        <div class="flex flex-col gap-3">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-yellow-400">Suggested Deduction</p>
                                    <p class="text-xs text-yellow-300 mt-1">Not applied by admin</p>
                                </div>
                                <p class="text-lg font-bold text-yellow-400">NPR {{
                                    number_format($payroll->hourly_deduction_suggested, 2) }}</p>
                            </div>

                            @if($payroll->status === 'draft')
                            <div class="flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('admin.hrm.payroll.update', $payroll->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="hourly_deduction_amount"
                                        value="{{ $payroll->hourly_deduction_suggested }}">
                                    <input type="hidden" name="hourly_deduction_approved" value="1">
                                    <button type="submit"
                                        class="px-3 py-2 bg-yellow-500 text-slate-900 font-semibold rounded hover:bg-yellow-400 transition">
                                        Apply suggested deduction
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.hrm.payroll.update', $payroll->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="hourly_deduction_approved" value="0">
                                    <button type="submit"
                                        class="px-3 py-2 bg-slate-700 text-slate-100 font-semibold rounded hover:bg-slate-600 transition">
                                        Keep deduction at 0
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="bg-lime-500/10 border border-lime-500/20 rounded-lg p-3 text-center">
                        <p class="text-lime-400">✓ All required hours completed</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Anomalies List -->
            @if(!empty($anomalies) && $anomalies->count() > 0)
            <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
                <h3 class="text-lg font-semibold text-white mb-4">Attendance Anomalies ({{ $anomalies->count() }})
                </h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($anomalies as $anomaly)
                    <div
                        class="border-l-4 @if($anomaly->severity === 'high') border-red-500 @elseif($anomaly->severity === 'medium') border-yellow-500 @else border-blue-500 @endif pl-3 py-2">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-sm font-medium text-white">{{ ucfirst(str_replace('_', ' ',
                                $anomaly->anomaly_type)) }}</span>
                            <span
                                class="text-xs px-2 py-0.5 rounded @if($anomaly->severity === 'high') bg-red-500/20 text-red-400 @elseif($anomaly->severity === 'medium') bg-yellow-500/20 text-yellow-400 @else bg-blue-500/20 text-blue-400 @endif">
                                {{ ucfirst($anomaly->severity) }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($anomaly->date)->format('d M Y')
                            }}</p>
                        <p class="text-sm text-slate-300 mt-1">{{ $anomaly->description }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Payroll Modal -->
<x-professional-modal id="deletePayrollModal" title="Delete Draft Payroll" icon="trash" iconColor="red"
    maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Are you sure you want to delete this draft payroll? This action cannot be undone.</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Employee:</span> {{ $payroll->employee->name }}</p>
            <p class="text-sm text-slate-400"><span class="font-medium">Period:</span> {{ $payroll->period_start_bs }}
                to {{ $payroll->period_end_bs }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button type="button" onclick="closeModal('deletePayrollModal')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
            Cancel
        </button>
        <form method="POST" action="{{ route('admin.hrm.payroll.destroy', $payroll->id) }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete Payroll
            </button>
        </form>
    </x-slot>
</x-professional-modal>

<!-- Mark as Paid Modal -->
<x-professional-modal id="markAsPaidModal" title="Mark Payroll as Paid" icon="check" iconColor="green"
    maxWidth="max-w-md">
    <form method="POST" action="{{ route('admin.hrm.payroll.mark-as-paid', $payroll->id) }}">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Payment Method <span
                        class="text-red-400">*</span></label>
                <select name="payment_method" required
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Select payment method</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="cheque">Cheque</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Transaction Reference (Optional)</label>
                <input type="text" name="transaction_reference" placeholder="Enter transaction reference"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" onclick="closeModal('markAsPaidModal')"
                class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
                Cancel
            </button>
            <button type="submit"
                class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Confirm Payment
            </button>
        </x-slot>
    </form>
</x-professional-modal>

<!-- Approve Payroll Modal -->
<x-professional-modal id="approvePayrollModal" title="Approve Payroll"
    subtitle="This action will mark the payroll as approved." icon="check" iconColor="green" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Are you sure you want to approve this payroll?</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Employee:</span> {{ $payroll->employee->full_name }}
            </p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Period:</span> {{ $payroll->period_start_bs
                }} to {{ $payroll->period_end_bs }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('approvePayrollModal')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Cancel</button>
        <form method="POST" action="{{ route('admin.hrm.payroll.approve', $payroll->id) }}" class="inline">
            @csrf
            <button type="submit"
                class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Approve
            </button>
        </form>
    </x-slot>
</x-professional-modal>

<!-- Send Payslip (Approved Status) Modal -->
<x-professional-modal id="sendPayslipApprovedModal" title="Send Payslip to Employee"
    subtitle="Payslip will be sent to the employee email" icon="check" iconColor="blue" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Send payslip to employee email?</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Email:</span> {{ $payroll->employee->email }}</p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Employee:</span> {{
                $payroll->employee->full_name }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('sendPayslipApprovedModal')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Cancel</button>
        <form method="POST" action="{{ route('admin.hrm.payroll.mark-as-sent', $payroll->id) }}" class="inline">
            @csrf
            <button type="submit"
                class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Send
            </button>
        </form>
    </x-slot>
</x-professional-modal>

<!-- Send Payslip (Paid Status) Modal -->
<x-professional-modal id="sendPayslipPaidModal" title="Send Payslip to Employee"
    subtitle="Payslip will be sent to the employee email" icon="check" iconColor="blue" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Send payslip to employee email?</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Email:</span> {{ $payroll->employee->email }}</p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Employee:</span> {{
                $payroll->employee->full_name }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('sendPayslipPaidModal')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Cancel</button>
        <form method="POST" action="{{ route('admin.hrm.payroll.mark-as-sent', $payroll->id) }}" class="inline">
            @csrf
            <button type="submit"
                class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Send
            </button>
        </form>
    </x-slot>
</x-professional-modal>
@endsection