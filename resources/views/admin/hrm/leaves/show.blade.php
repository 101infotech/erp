@extends('admin.layouts.app')

@section('title', 'Leave Request Details')
@section('page-title', 'Leave Request Details')

@section('content')
<div class="space-y-6 max-w-4xl">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-white">Leave Request Details</h1>
            <p class="text-slate-400 mt-1">{{ $leave->employee->full_name }}</p>
        </div>
        <a href="{{ route('admin.hrm.leaves.index') }}"
            class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
            Back to List
        </a>
    </div>

    <!-- Status & Type Badges -->
    <div class="flex items-center gap-3">
        @if($leave->status === 'pending')
        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-500/20 text-yellow-400">Pending</span>
        @elseif($leave->status === 'approved')
        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-500/20 text-green-400">Approved</span>
        @elseif($leave->status === 'rejected')
        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-500/20 text-red-400">Rejected</span>
        @elseif($leave->status === 'cancelled')
        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-slate-500/20 text-slate-400">Cancelled</span>
        @endif

        <span class="px-3 py-1 text-sm font-semibold rounded-full 
                @if($leave->leave_type === 'annual') bg-blue-500/20 text-blue-400
                @elseif($leave->leave_type === 'sick') bg-red-500/20 text-red-400
                @elseif($leave->leave_type === 'casual') bg-green-500/20 text-green-400
                @else bg-slate-500/20 text-slate-400
                @endif">
            {{ ucfirst($leave->leave_type) }} Leave
        </span>
    </div>

    <!-- Leave Details -->
    <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Leave Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-slate-600 dark:text-slate-400">Employee</p>
                <p class="text-slate-900 dark:text-white font-medium">{{ $leave->employee->full_name }} ({{
                    $leave->employee->employee_code }})</p>
            </div>
            <div>
                <p class="text-sm text-slate-600 dark:text-slate-400">Leave Type</p>
                <p class="text-slate-900 dark:text-white font-medium">{{ ucfirst($leave->leave_type) }} Leave</p>
            </div>
            <div>
                <p class="text-sm text-slate-600 dark:text-slate-400">Start Date</p>
                <p class="text-slate-900 dark:text-white font-medium">{{
                    \Carbon\Carbon::parse($leave->start_date)->format('d M Y, l')
                    }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600 dark:text-slate-400">End Date</p>
                <p class="text-slate-900 dark:text-white font-medium">{{
                    \Carbon\Carbon::parse($leave->end_date)->format('d M Y, l') }}
                </p>
            </div>
            <div>
                <p class="text-sm text-slate-600 dark:text-slate-400">Total Days</p>
                <p class="text-slate-900 dark:text-white font-medium text-lg">{{ number_format($leave->total_days, 1) }}
                    days</p>
            </div>
            <div>
                <p class="text-sm text-slate-600 dark:text-slate-400">Requested On</p>
                <p class="text-slate-900 dark:text-white font-medium">{{ $leave->created_at->format('d M Y, h:i A') }}
                </p>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">Reason</p>
            <p class="text-slate-900 dark:text-white">{{ $leave->reason }}</p>
        </div>
    </div>

    <!-- Approval Information -->
    @if($leave->status !== 'pending')
    <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">
            @if($leave->status === 'approved') 
                Approval Information
            @elseif($leave->status === 'cancelled') 
                Cancelled Information
            @else 
                {{ ucfirst($leave->status) }} Information
            @endif
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    @if($leave->status === 'cancelled')
                        Cancelled By
                    @elseif($leave->status === 'rejected')
                        Rejected By
                    @else
                        {{ $leave->status === 'approved' ? 'Approved' : ucfirst($leave->status) }} By
                    @endif
                </p>
                <p class="text-slate-900 dark:text-white font-medium">
                    @if($leave->status === 'cancelled')
                        {{ $leave->canceller->name ?? 'N/A' }}
                    @elseif($leave->status === 'rejected')
                        {{ $leave->rejecter->name ?? 'N/A' }}
                    @else
                        {{ $leave->approver->name ?? 'N/A' }}
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    @if($leave->status === 'cancelled')
                        Cancelled At
                    @elseif($leave->status === 'rejected')
                        Rejected At
                    @else
                        {{ $leave->status === 'approved' ? 'Approved' : ucfirst($leave->status) }} At
                    @endif
                </p>
                <p class="text-slate-900 dark:text-white font-medium">
                    @if($leave->status === 'cancelled')
                        {{ $leave->cancelled_at ? $leave->cancelled_at->format('d M Y, h:i A') : 'N/A' }}
                    @elseif($leave->status === 'rejected')
                        {{ $leave->rejected_at ? $leave->rejected_at->format('d M Y, h:i A') : 'N/A' }}
                    @else
                        {{ $leave->approved_at ? $leave->approved_at->format('d M Y, h:i A') : 'N/A' }}
                    @endif
                </p>
            </div>
        </div>

        @if($leave->status === 'rejected' && $leave->rejection_reason)
        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">Rejection Reason</p>
            <p class="text-red-400">{{ $leave->rejection_reason }}</p>
        </div>
        @endif
    </div>
    @endif

    <!-- Action Buttons -->
    @if($leave->status === 'pending')
    <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Actions</h2>
        <div class="flex gap-4">
            <!-- Approve Button -->
            <button type="button" onclick="openModal('approveLeaveModal')"
                class="flex-1 px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition">
                Approve Leave Request
            </button>

            <!-- Reject Button -->
            <button type="button" onclick="openModal('rejectModal')"
                class="flex-1 px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition">
                Reject Leave Request
            </button>
        </div>
    </div>
    @endif

    @if(in_array($leave->status, ['pending', 'approved']))
    <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
        <button type="button" onclick="openModal('cancelLeaveAdminModal')"
            class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
            Cancel Leave Request
        </button>
    </div>
    @endif
</div>

<!-- Reject Modal -->
<x-professional-modal id="rejectModal" title="Reject Leave Request" icon="question" iconColor="red" maxWidth="max-w-md">
    <form method="POST" action="{{ route('admin.hrm.leaves.reject', $leave->id) }}">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Rejection Reason <span class="text-red-400">*</span>
                </label>
                <textarea name="rejection_reason" rows="4" required maxlength="500"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    placeholder="Please provide reason for rejection"></textarea>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" onclick="closeModal('rejectModal')"
                class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
                Cancel
            </button>
            <button type="submit"
                class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Confirm Rejection
            </button>
        </x-slot>
    </form>
</x-professional-modal>

<!-- Approve Leave Request Modal -->
<x-professional-modal id="approveLeaveModal" title="Approve Leave Request" subtitle="Mark this leave as approved"
    icon="check" iconColor="green" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Are you sure you want to approve this leave request?</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Employee:</span> {{ $leave->employee->full_name }}
            </p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Leave Type:</span> {{
                ucfirst(str_replace('_', ' ', $leave->leave_type)) }}</p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Days:</span> {{ $leave->total_days }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('approveLeaveModal')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Keep
            Pending</button>
        <form method="POST" action="{{ route('admin.hrm.leaves.approve', $leave->id) }}" class="inline">
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

<!-- Cancel Leave Request Modal (Admin) -->
<x-professional-modal id="cancelLeaveAdminModal" title="Cancel Leave Request"
    subtitle="This action will restore the leave balance" icon="warning" iconColor="red" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Are you sure you want to cancel this leave request? This will restore the leave
            balance if it was approved.</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Employee:</span> {{ $leave->employee->full_name }}
            </p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Status:</span> {{ ucfirst($leave->status)
                }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('cancelLeaveAdminModal')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Keep</button>
        <form method="POST" action="{{ route('admin.hrm.leaves.cancel', $leave->id) }}" class="inline">
            @csrf
            <button type="submit"
                class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel Request
            </button>
        </form>
    </x-slot>
</x-professional-modal>
@endsection