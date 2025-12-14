<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-4xl font-bold text-white">Payslip Details</h1>
                <a href="{{ route('employee.payroll.index') }}"
                    class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600">
                    ← Back to Payroll
                </a>
            </div>

            <!-- Payslip Card -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700 overflow-hidden mb-6">
                <!-- Header -->
                <div class="bg-gradient-to-r from-lime-500/10 to-lime-600/10 border-b border-lime-500/30 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-white mb-2">{{ $payroll->employee->full_name }}</h2>
                            <p class="text-slate-300">{{ $payroll->employee->code }} | {{
                                $payroll->employee->department->name ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-slate-400 text-sm mb-1">Pay Period</p>
                            <p class="text-white font-semibold">{{
                                \Carbon\Carbon::parse($payroll->period_start)->format('M d') }} - {{
                                \Carbon\Carbon::parse($payroll->period_end)->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Earnings Section -->
                <div class="p-6 border-b border-slate-700">
                    <h3 class="text-lg font-bold text-white mb-4">Earnings</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-300">Base Salary</span>
                            <span class="text-white font-semibold">NPR {{ number_format($payroll->gross_salary, 2)
                                }}</span>
                        </div>
                        @if($payroll->bonus_amount > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-slate-300">Bonus</span>
                            <span class="text-green-400 font-semibold">+ NPR {{ number_format($payroll->bonus_amount, 2)
                                }}</span>
                        </div>
                        @endif
                        @if($payroll->overtime_pay > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-slate-300">Overtime Pay</span>
                            <span class="text-green-400 font-semibold">+ NPR {{ number_format($payroll->overtime_pay, 2)
                                }}</span>
                        </div>
                        @endif
                        <div class="h-px bg-slate-600 my-2"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-white font-bold">Gross Salary</span>
                            <span class="text-lime-400 font-bold text-xl">NPR {{ number_format($payroll->gross_salary +
                                $payroll->bonus_amount + $payroll->overtime_pay, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Deductions Section -->
                <div class="p-6 border-b border-slate-700">
                    <h3 class="text-lg font-bold text-white mb-4">Deductions</h3>
                    <div class="space-y-3">
                        @if($payroll->tax_amount > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-slate-300">Income Tax</span>
                            <span class="text-red-400 font-semibold">- NPR {{ number_format($payroll->tax_amount, 2)
                                }}</span>
                        </div>
                        @endif
                        @if($payroll->ssf_employee > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-slate-300">SSF (Employee)</span>
                            <span class="text-red-400 font-semibold">- NPR {{ number_format($payroll->ssf_employee, 2)
                                }}</span>
                        </div>
                        @endif
                        @if($payroll->other_deductions > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-slate-300">Other Deductions</span>
                            <span class="text-red-400 font-semibold">- NPR {{ number_format($payroll->other_deductions,
                                2) }}</span>
                        </div>
                        @endif
                        <div class="h-px bg-slate-600 my-2"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-white font-bold">Total Deductions</span>
                            <span class="text-red-400 font-bold text-xl">NPR {{
                                number_format($payroll->total_deductions, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Net Pay Section -->
                <div class="bg-gradient-to-r from-lime-500/20 to-lime-600/20 p-6">
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-white">Net Salary</span>
                        <span class="text-4xl font-bold text-lime-400">NPR {{ number_format($payroll->net_salary, 2)
                            }}</span>
                    </div>
                    <p class="text-slate-300 text-sm mt-2">Payment Status: <span class="font-semibold capitalize">{{
                            $payroll->status }}</span></p>
                    @if($payroll->payment_date)
                    <p class="text-slate-300 text-sm">Paid on: {{
                        \Carbon\Carbon::parse($payroll->payment_date)->format('M d, Y') }}</p>
                    @endif
                </div>
            </div>

            <!-- Tax Breakdown -->
            @if($payroll->tax_amount > 0 && isset($taxBreakdown))
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700 p-6 mb-6">
                <h3 class="text-lg font-bold text-white mb-4">Income Tax Breakdown (Annual)</h3>
                <div class="space-y-3 mb-4">
                    @foreach($taxBreakdown['slabs'] as $slab)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-300">{{ $slab['range'] }} @ {{ $slab['rate'] }}</span>
                        <span class="text-white font-medium">{{ $slab['tax'] }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="h-px bg-slate-600 my-3"></div>
                <div class="flex justify-between items-center">
                    <span class="text-white font-semibold">Total Annual Tax</span>
                    <span class="text-lime-400 font-bold">{{ $taxBreakdown['total_tax'] }}</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-slate-400 text-sm">Effective Tax Rate</span>
                    <span class="text-slate-300 text-sm">{{ $taxBreakdown['effective_rate'] }}</span>
                </div>
            </div>
            @endif

            <!-- Download Button -->
            @if($payroll->payslip_pdf_path)
            <div class="text-center">
                <a href="{{ route('employee.payroll.download', $payroll->id) }}"
                    class="inline-flex items-center px-6 py-3 bg-lime-500 text-slate-900 rounded-lg font-semibold hover:bg-lime-400 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download Payslip PDF
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>