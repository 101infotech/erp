@extends('admin.layouts.app')

@section('title', 'Leave Management')
@section('page-title', 'Leave Management')

@section('content')
<div class="px-6 md:px-8 space-y-5 py-4">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-white">Leave Management</h1>
            <p class="text-sm text-slate-400 mt-1">Manage employee leave requests and approvals</p>
        </div>
        <a href="{{ route('admin.hrm.leaves.create') }}"
            class="px-3 py-1.5 bg-lime-500 hover:bg-lime-600 text-slate-900 text-sm font-semibold rounded-lg transition">
            New Leave Request
        </a>
    </div>

    <!-- Status Tabs -->
    <div class="border-b border-slate-700">
        <nav class="flex space-x-6">
            <a href="{{ route('admin.hrm.leaves.index', ['status' => 'pending']) }}"
                class="pb-3 px-1 border-b-2 font-medium text-xs {{ $status === 'pending' ? 'border-lime-500 text-lime-400' : 'border-transparent text-slate-400 hover:text-slate-300' }}">
                Pending
            </a>
            <a href="{{ route('admin.hrm.leaves.index', ['status' => 'approved']) }}"
                class="pb-3 px-1 border-b-2 font-medium text-xs {{ $status === 'approved' ? 'border-lime-500 text-lime-400' : 'border-transparent text-slate-400 hover:text-slate-300' }}">
                Approved
            </a>
            <a href="{{ route('admin.hrm.leaves.index', ['status' => 'rejected']) }}"
                class="pb-3 px-1 border-b-2 font-medium text-xs {{ $status === 'rejected' ? 'border-lime-500 text-lime-400' : 'border-transparent text-slate-400 hover:text-slate-300' }}">
                Rejected
            </a>
            <a href="{{ route('admin.hrm.leaves.index', ['status' => 'all']) }}"
                class="pb-3 px-1 border-b-2 font-medium text-xs {{ $status === 'all' ? 'border-lime-500 text-lime-400' : 'border-transparent text-slate-400 hover:text-slate-300' }}">
                All
            </a>
        </nav>
    </div>

    <!-- Filters -->
    <div class="bg-slate-800 rounded-lg p-3 border border-slate-700">
        <form method="GET" action="{{ route('admin.hrm.leaves.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="hidden" name="status" value="{{ $status }}">

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Employee</label>
                <select name="employee_id"
                    class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-lg px-2.5 py-1.5">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('employee_id')==$employee->id ? 'selected' : ''
                        }}>
                        {{ $employee->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Leave Type</label>
                <select name="leave_type"
                    class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-lg px-2.5 py-1.5">
                    <option value="">All Types</option>
                    <option value="annual" {{ request('leave_type')==='annual' ? 'selected' : '' }}>Annual</option>
                    <option value="sick" {{ request('leave_type')==='sick' ? 'selected' : '' }}>Sick</option>
                    <option value="casual" {{ request('leave_type')==='casual' ? 'selected' : '' }}>Casual</option>
                    <option value="unpaid" {{ request('leave_type')==='unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-lg px-2.5 py-1.5">
            </div>

            <div class="flex items-end">
                <button type="submit"
                    class="w-full px-3 py-1.5 bg-lime-500 hover:bg-lime-600 text-slate-900 text-sm font-semibold rounded-lg transition">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Leave Requests Table -->
    <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-900">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Employee</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Leave Type</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Period</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Days</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Reason</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($leaves as $leave)
                    <tr class="hover:bg-slate-750">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-white">{{ $leave->employee->full_name }}</div>
                            <div class="text-sm text-slate-400">{{ $leave->employee->employee_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($leave->leave_type === 'annual') bg-blue-500/20 text-blue-400
                                        @elseif($leave->leave_type === 'sick') bg-red-500/20 text-red-400
                                        @elseif($leave->leave_type === 'casual') bg-green-500/20 text-green-400
                                        @else bg-slate-500/20 text-slate-400
                                        @endif">
                                {{ ucfirst($leave->leave_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-300">
                            {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }} - {{
                            \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-medium text-white">
                            {{ number_format($leave->total_days, 1) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-300 max-w-xs truncate">
                            {{ $leave->reason }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($leave->status === 'pending')
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-500/20 text-yellow-400">Pending</span>
                            @elseif($leave->status === 'approved')
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-400">Approved</span>
                            @elseif($leave->status === 'rejected')
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full bg-red-500/20 text-red-400">Rejected</span>
                            @elseif($leave->status === 'cancelled')
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-500/20 text-slate-400">Cancelled</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <a href="{{ route('admin.hrm.leaves.show', $leave->id) }}"
                                class="text-lime-400 hover:text-lime-300 font-medium">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <svg class="mx-auto h-12 w-12 text-slate-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-2">No leave requests found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($leaves->hasPages())
        <div class="px-6 py-4 border-t border-slate-700">
            {{ $leaves->links() }}
        </div>
        @endif
    </div>
</div>
@endsection