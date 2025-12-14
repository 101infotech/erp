<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <a href="{{ route('employee.feedback.history') }}"
                class="inline-flex items-center text-sm text-slate-400 hover:text-lime-400 transition mb-6">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to History
            </a>

            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-slate-700 bg-gradient-to-r from-slate-900 to-slate-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-white">Your Weekly Feedback</h2>
                            <p class="text-slate-400 text-sm mt-1">Submitted {{ $feedback->submitted_at->format('M d, Y
                                h:i A') }}</p>
                        </div>
                        <span
                            class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-300 border border-green-500/30">
                            Submitted
                        </span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-8">
                    <!-- Feelings -->
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="bg-blue-500/20 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">How You Were Feeling</h3>
                        </div>
                        <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                            <p class="text-slate-300 whitespace-pre-wrap">{{ $feedback->feelings }}</p>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="bg-green-500/20 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Progress Made</h3>
                        </div>
                        <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                            <p class="text-slate-300 whitespace-pre-wrap">{{ $feedback->work_progress }}</p>
                        </div>
                    </div>

                    <!-- Self-Improvement -->
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="bg-purple-500/20 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Areas for Improvement</h3>
                        </div>
                        <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                            <p class="text-slate-300 whitespace-pre-wrap">{{ $feedback->self_improvements }}</p>
                        </div>
                    </div>

                    <!-- Management Response (if available) -->
                    @if($feedback->admin_notes)
                    <div class="border-t border-slate-700 pt-8">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="bg-amber-500/20 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Management Feedback</h3>
                        </div>
                        <div
                            class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 rounded-lg p-4 border border-amber-500/30">
                            <p class="text-slate-200 whitespace-pre-wrap">{{ $feedback->admin_notes }}</p>
                        </div>
                    </div>
                    @else
                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                        <p class="text-slate-400 text-sm">No management feedback yet. Check back later!</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>