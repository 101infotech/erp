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

                    <!-- AI Sentiment Analysis (if available) -->
                    @if($sentimentAnalysis)
                    <div class="border-t border-slate-700 pt-8">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="bg-emerald-500/20 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10.5 1.5H5.75A2.75 2.75 0 003 4.25v11.5A2.75 2.75 0 005.75 18h8.5A2.75 2.75 0 0017 15.25V4.25A2.75 2.75 0 0014.25 1.5h-3.75a.75.75 0 000 1.5h3.75c.69 0 1.25.56 1.25 1.25v11.5c0 .69-.56 1.25-1.25 1.25h-8.5c-.69 0-1.25-.56-1.25-1.25V4.25c0-.69.56-1.25 1.25-1.25h3.75a.75.75 0 000-1.5z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">AI Sentiment Analysis</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Overall Sentiment -->
                            <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                                <p class="text-slate-400 text-xs font-semibold mb-2">OVERALL SENTIMENT</p>
                                <p class="text-3xl font-bold text-white">{{ round($sentimentAnalysis->overall_sentiment
                                    * 100) }}%</p>
                                <p class="text-slate-300 text-sm mt-2">{{ $sentimentAnalysis->getSentimentLabel() }}</p>
                            </div>

                            <!-- Sentiment Breakdown -->
                            <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                                <p class="text-slate-400 text-xs font-semibold mb-3">BREAKDOWN</p>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-slate-400">Feelings</span>
                                        <span class="text-sm font-semibold text-slate-200">{{
                                            round($sentimentAnalysis->feelings_sentiment * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-800 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full"
                                            style="width: {{ round($sentimentAnalysis->feelings_sentiment * 100) }}%">
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center mt-3">
                                        <span class="text-sm text-slate-400">Progress</span>
                                        <span class="text-sm font-semibold text-slate-200">{{
                                            round($sentimentAnalysis->progress_sentiment * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-800 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full"
                                            style="width: {{ round($sentimentAnalysis->progress_sentiment * 100) }}%">
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center mt-3">
                                        <span class="text-sm text-slate-400">Improvement</span>
                                        <span class="text-sm font-semibold text-slate-200">{{
                                            round($sentimentAnalysis->improvement_sentiment * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-800 rounded-full h-2">
                                        <div class="bg-purple-500 h-2 rounded-full"
                                            style="width: {{ round($sentimentAnalysis->improvement_sentiment * 100) }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Trend Analysis -->
                            <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                                <p class="text-slate-400 text-xs font-semibold mb-2">TREND</p>
                                <p class="text-2xl font-bold text-white">{{ $sentimentAnalysis->getTrendIcon() }} {{
                                    ucfirst($sentimentAnalysis->trend_direction) }}</p>
                                @if($sentimentAnalysis->trend_change)
                                <p class="text-slate-300 text-sm mt-2">{{ $sentimentAnalysis->trend_change > 0 ? '📈 +'
                                    : '📉 ' }}{{ round($sentimentAnalysis->trend_change * 100) }}% from last week</p>
                                @endif
                            </div>

                            <!-- Analysis Info -->
                            <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                                <p class="text-slate-400 text-xs font-semibold mb-2">ANALYSIS INFO</p>
                                <div class="space-y-1 text-sm">
                                    <p class="text-slate-300"><span class="text-slate-500">Model:</span> {{
                                        $sentimentAnalysis->ai_model }}</p>
                                    <p class="text-slate-300"><span class="text-slate-500">Time:</span> {{
                                        $sentimentAnalysis->processing_time_ms }}ms</p>
                                    <p class="text-slate-300"><span class="text-slate-500">Date:</span> {{
                                        $sentimentAnalysis->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Alert Message -->
                        @if($sentimentAnalysis->needs_manager_attention)
                        <div
                            class="mt-4 p-4 bg-amber-500/10 border border-amber-500/30 rounded-lg flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p class="text-amber-300 text-sm">{{ $sentimentAnalysis->alert_reason }}</p>
                        </div>
                        @endif
                    </div>
                    @endif

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