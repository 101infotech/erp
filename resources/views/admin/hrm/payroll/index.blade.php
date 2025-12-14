@extends('admin.layouts.app')

@section('title', 'Payroll Management')
@section('page-title', 'Payroll Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Payroll Management</h1>
            <p class="text-slate-400 mt-1">Generate and manage employee payroll records</p>
        </div>
        <a href="{{ route('admin.hrm.payroll.create') }}"
            class="px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
            Generate Payroll
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
        <form method="GET" action="{{ route('admin.hrm.payroll.index') }}"
            class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Status</label>
                <select name="status"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status')==='draft' ? 'selected' : '' }}>Draft</option>
                    <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved
                    </option>
                    <option value="paid" {{ request('status')==='paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>

            <!-- Employee Filter -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Employee</label>
                <select name="employee_id"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('employee_id')==$employee->id ? 'selected' : ''
                        }}>
                        {{ $employee->first_name }} {{ $employee->last_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Company Filter -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Company</label>
                <select name="company_id"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id')==$company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Period Filter -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Period From</label>
                <input type="date" name="period_start" value="{{ request('period_start') }}"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
            </div>

            <div class="flex items-end">
                <button type="submit"
                    class="w-full px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Payroll Records Table -->
    <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Period</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Gross Salary</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Net Salary</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($payrolls as $payroll)
                    <tr class="hover:bg-slate-750">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-white">{{ $payroll->employee->full_name }}</div>
                            <div class="text-sm text-slate-400">{{ $payroll->employee->employee_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                            {{ format_nepali_date($payroll->period_start_bs, 'j M Y') }} - {{
                            format_nepali_date($payroll->period_end_bs, 'j M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-white">
                            NPR {{ number_format($payroll->gross_salary, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-lime-400">
                            NPR {{ number_format($payroll->net_salary, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($payroll->status === 'draft')
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-500/20 text-yellow-400">Draft</span>
                            @elseif($payroll->status === 'approved')
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-500/20 text-blue-400">Approved</span>
                            @elseif($payroll->status === 'paid')
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-400">Paid</span>
                            @endif

                            @if(!empty($payroll->anomalies) && !$payroll->anomalies_reviewed)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-500/20 text-red-400 ml-1"
                                title="Has unreviewed anomalies">
                                ⚠ Anomalies
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <a href="{{ route('admin.hrm.payroll.show', $payroll->id) }}"
                                class="text-lime-400 hover:text-lime-300 font-medium">View</a>
                            @if($payroll->status === 'draft')
                            <span class="text-slate-600 mx-1">|</span>
                            <a href="{{ route('admin.hrm.payroll.edit', $payroll->id) }}"
                                class="text-blue-400 hover:text-blue-300 font-medium">Edit</a>
                            <span class="text-slate-600 mx-1">|</span>
                            <button type="button"
                                onclick="openDeleteModal({{ $payroll->id }}, '{{ $payroll->employee->name }}', '{{ $payroll->period_start_bs }}', '{{ $payroll->period_end_bs }}')"
                                class="text-red-400 hover:text-red-300 font-medium">Delete</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <svg class="mx-auto h-12 w-12 text-slate-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2">No payroll records found</p>
                            <a href="{{ route('admin.hrm.payroll.create') }}"
                                class="text-lime-400 hover:text-lime-300 mt-2 inline-block">Generate your first
                                payroll</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payrolls->hasPages())
        <div class="px-6 py-4 border-t border-slate-700">
            {{ $payrolls->links() }}
        </div>
        @endif
    </div>
</div>
</div>
@endsection
<!-- Delete Confirmation Modal -->
<div id="deletePayrollModal"
    class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-lg shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between p-6 border-b border-slate-700">
            <h3 class="text-xl font-semibold text-white">Delete Draft Payroll</h3>
            <button onclick="closeDeleteModal()" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-6">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-red-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-slate-300 mb-2">
                        Are you sure you want to delete this draft payroll?
                    </p>
                    <div class="bg-slate-900/50 rounded p-3 mb-3 border border-slate-700">
                        <p class="text-sm text-white"><span class="font-medium">Employee:</span> <span
                                id="deleteEmployeeName"></span></p>
                        <p class="text-sm text-slate-400"><span class="font-medium">Period:</span> <span
                                id="deletePeriod"></span></p>
                    </div>
                    <p class="text-sm text-red-400 font-medium">
                        ⚠️ This action cannot be undone.
                    </p>
                </div>
            </div>
            <form method="POST" action="" id="deletePayrollForm">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Payroll
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentPayrollId = null;

    // Delete modal functions
    function openDeleteModal(payrollId, employeeName, periodStart, periodEnd) {
        currentPayrollId = payrollId;
        document.getElementById('deleteEmployeeName').textContent = employeeName;
        document.getElementById('deletePeriod').textContent = periodStart + ' to ' + periodEnd;
        document.getElementById('deletePayrollForm').action = '/admin/hrm/payroll/' + payrollId;
        document.getElementById('deletePayrollModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deletePayrollModal').classList.add('hidden');
        currentPayrollId = null;
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>