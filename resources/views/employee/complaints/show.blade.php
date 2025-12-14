<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <a href="{{ route('employee.complaints.index') }}"
                class="inline-flex items-center text-sm text-slate-400 hover:text-lime-400 transition mb-6">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to My Feedback
            </a>

            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-slate-700">
                    <h2 class="text-2xl font-bold text-white mb-3">{{ $complaint->subject }}</h2>
                    <div class="flex items-center gap-3">
                        @php
                        $statusColors = [
                        'pending' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                        'reviewing' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                        'resolved' => 'bg-green-500/20 text-green-300 border-green-500/30',
                        'dismissed' => 'bg-red-500/20 text-red-300 border-red-500/30',
                        ];
                        $priorityColors = [
                        'high' => 'bg-red-500/20 text-red-300 border-red-500/30',
                        'medium' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                        'low' => 'bg-green-500/20 text-green-300 border-green-500/30',
                        ];
                        @endphp
                        <span
                            class="px-3 py-1 text-xs font-medium rounded-full border {{ $statusColors[$complaint->status] ?? '' }}">
                            {{ ucfirst($complaint->status) }}
                        </span>
                        @if($complaint->category)
                        <span class="px-3 py-1 text-xs font-medium bg-slate-700 text-slate-300 rounded-full">
                            {{ ucfirst($complaint->category) }}
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-6">
                    <!-- Description -->
                    <div>
                        <h3 class="text-sm font-semibold text-slate-300 mb-3">Your Feedback</h3>
                        <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                            <p class="text-slate-300 whitespace-pre-wrap">{{ $complaint->description }}</p>
                        </div>
                    </div>

                    <!-- Admin Response (if any) -->
                    @if($complaint->admin_notes)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-300 mb-3">Management Response</h3>
                        <div
                            class="bg-gradient-to-r from-lime-500/10 to-green-500/10 border border-lime-500/30 rounded-lg p-4">
                            <p class="text-slate-300 whitespace-pre-wrap">{{ $complaint->admin_notes }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Metadata -->
                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                        <h3 class="text-sm font-semibold text-slate-300 mb-3">Information</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Submitted:</span>
                                <span class="text-white">{{ $complaint->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                            @if($complaint->resolved_at)
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Resolved:</span>
                                <span class="text-white">{{ $complaint->resolved_at->format('M d, Y h:i A') }}</span>
                            </div>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Reference ID:</span>
                                <span class="text-white">#{{ $complaint->id }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Current Status:</span>
                                <span class="text-white">{{ ucfirst($complaint->status) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Information -->
                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-blue-200 text-sm font-medium mb-1">What happens next?</p>
                                <p class="text-blue-300/80 text-sm">
                                    @if($complaint->status === 'pending')
                                    Your feedback is waiting to be reviewed by management. You'll be notified when
                                    there's an update.
                                    @elseif($complaint->status === 'reviewing')
                                    Your feedback is currently being reviewed. Management is looking into your concern.
                                    @elseif($complaint->status === 'resolved')
                                    Your feedback has been addressed and resolved. Thank you for sharing your thoughts!
                                    @else
                                    This feedback has been reviewed and closed.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>