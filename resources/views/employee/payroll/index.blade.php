<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">My Payroll</h1>
                    <p class="text-slate-400">View your salary history and payslips</p>
                </div>
                <a href="{{ route('employee.dashboard') }}"
                    class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600">
                    ← Back to Dashboard
                </a>
            </div>

            @if(isset($message))
            <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-6">
                <p class="text-yellow-400">{{ $message }}</p>
            </div>
            @else
            <!-- Employee Info Card -->
            @if($employee)
            <div class="bg-gradient-to-r from-lime-500/10 to-lime-600/10 border border-lime-500/30 rounded-xl p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-slate-400 text-sm mb-1">Employee Name</p>
                        <p class="text-white font-semibold">{{ $employee->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-sm mb-1">Employee Code</p>
                        <p class="text-white font-semibold">{{ $employee->code }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-sm mb-1">Department</p>
                        <p class="text-white font-semibold">{{ $employee->department->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-sm mb-1">Base Salary</p>
                        <p class="text-lime-400 font-bold text-xl">NPR {{ number_format($employee->base_salary ?? 0, 2)
                            }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Payroll Records -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-700">
                    <h2 class="text-xl font-bold text-white">Salary History</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                    @forelse($payrolls as $payroll)
                    <div class="bg-slate-700/30 rounded-xl p-6 border border-slate-600 hover:border-lime-500/50 transition cursor-pointer"
                        onclick="window.location='{{ route('employee.payroll.show', $payroll->id) }}'">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($payroll->status === 'paid') bg-green-500/20 text-green-400
                                @elseif($payroll->status === 'approved') bg-blue-500/20 text-blue-400
                                @elseif($payroll->status === 'pending') bg-yellow-500/20 text-yellow-400
                                @else bg-slate-500/20 text-slate-400
                                @endif">
                                {{ ucfirst($payroll->status) }}
                            </span>
                            @if($payroll->payslip_pdf_path)
                            <a href="{{ route('employee.payroll.download', $payroll->id) }}"
                                class="text-lime-400 hover:text-lime-300" onclick="event.stopPropagation()">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </a>
                            @endif
                        </div>

                        <p class="text-slate-400 text-sm mb-2">Period</p>
                        <p class="text-white font-medium mb-4">{{
                            \Carbon\Carbon::parse($payroll->period_start)->format('M d') }} - {{
                            \Carbon\Carbon::parse($payroll->period_end)->format('M d, Y') }}</p>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span class="text-slate-400 text-sm">Gross Salary</span>
                                <span class="text-white font-medium">NPR {{ number_format($payroll->gross_salary, 2)
                                    }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400 text-sm">Deductions</span>
                                <span class="text-red-400 font-medium">- NPR {{
                                    number_format($payroll->total_deductions, 2) }}</span>
                            </div>
                            <div class="h-px bg-slate-600 my-2"></div>
                            <div class="flex justify-between">
                                <span class="text-slate-300 font-semibold">Net Salary</span>
                                <span class="text-lime-400 font-bold text-lg">NPR {{ number_format($payroll->net_salary,
                                    2) }}</span>
                            </div>
                        </div>

                        <button
                            class="w-full px-4 py-2 bg-slate-600 hover:bg-slate-500 text-white rounded-lg text-sm font-medium transition">
                            View Details →
                        </button>
                    </div>
                    @empty
                    <div class="col-span-3 py-12 text-center text-slate-400">
                        No payroll records found
                    </div>
                    @endforelse
                </div>

                @if($payrolls->hasPages())
                <div class="p-6 border-t border-slate-700">
                    {{ $payrolls->links() }}
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</x-app-layout>