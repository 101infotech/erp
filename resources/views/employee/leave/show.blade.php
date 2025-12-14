<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-4xl font-bold text-white">Leave Request Details</h1>
                <a href="{{ route('employee.leave.index') }}"
                    class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600">
                    ← Back to Leave
                </a>
            </div>

            <!-- Leave Details Card -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700 overflow-hidden">
                <!-- Header with Status -->
                <div class="bg-gradient-to-r from-lime-500/10 to-lime-600/10 border-b border-lime-500/30 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-white mb-2 capitalize">{{ $leave->leave_type }} Leave
                            </h2>
                            <p class="text-slate-300">Requested on {{ $leave->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            @if($leave->status === 'approved')
                            <span
                                class="px-4 py-2 bg-green-500/20 text-green-400 rounded-full text-sm font-semibold">Approved</span>
                            @elseif($leave->status === 'rejected')
                            <span
                                class="px-4 py-2 bg-red-500/20 text-red-400 rounded-full text-sm font-semibold">Rejected</span>
                            @elseif($leave->status === 'cancelled')
                            <span
                                class="px-4 py-2 bg-slate-500/20 text-slate-400 rounded-full text-sm font-semibold">Cancelled</span>
                            @else
                            <span
                                class="px-4 py-2 bg-yellow-500/20 text-yellow-400 rounded-full text-sm font-semibold">Pending</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Leave Information -->
                <div class="p-6 space-y-6">
                    <!-- Date Range -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-slate-400 text-sm mb-1">Start Date</p>
                            <p class="text-white font-semibold text-lg">{{ $leave->start_date->format('M d, Y') }}</p>
                            <p class="text-slate-500 text-sm">{{ $leave->start_date->format('l') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm mb-1">End Date</p>
                            <p class="text-white font-semibold text-lg">{{ $leave->end_date->format('M d, Y') }}</p>
                            <p class="text-slate-500 text-sm">{{ $leave->end_date->format('l') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm mb-1">Total Days</p>
                            <p class="text-lime-400 font-bold text-3xl">{{ $leave->total_days }}</p>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <p class="text-slate-400 text-sm mb-2">Reason</p>
                        <div class="bg-slate-700/30 rounded-lg p-4">
                            <p class="text-white">{{ $leave->reason }}</p>
                        </div>
                    </div>

                    <!-- Approval Information -->
                    @if($leave->status === 'approved' && $leave->approver)
                    <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-green-400 font-semibold">Approved by {{ $leave->approver->name }}</p>
                                <p class="text-green-300 text-sm">on {{ $leave->approved_at->format('M d, Y h:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($leave->status === 'rejected')
                    <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-400 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-red-400 font-semibold">Rejected {{ $leave->approver ? 'by ' .
                                    $leave->approver->name : '' }}</p>
                                @if($leave->approved_at)
                                <p class="text-red-300 text-sm mb-2">on {{ $leave->approved_at->format('M d, Y h:i A')
                                    }}</p>
                                @endif
                                @if($leave->rejection_reason)
                                <p class="text-red-300 text-sm mt-2"><strong>Reason:</strong> {{
                                    $leave->rejection_reason }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    @if($leave->status === 'pending')
                    <div class="flex items-center gap-4 pt-4 border-t border-slate-700">
                        <form method="POST" action="{{ route('employee.leave.cancel', $leave->id) }}" class="flex-1">
                            @csrf
                            <button type="submit"
                                class="w-full px-6 py-3 bg-red-500/20 text-red-400 border border-red-500/30 rounded-lg font-semibold hover:bg-red-500/30 transition"
                                onclick="return confirm('Are you sure you want to cancel this leave request?')">
                                Cancel Request
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>