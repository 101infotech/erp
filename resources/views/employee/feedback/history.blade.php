<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-white">My Feedback History</h1>
                    <p class="text-slate-400 text-sm mt-1">Track your submitted weekly feedback and responses</p>
                </div>
                <a href="{{ route('employee.feedback.dashboard') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-950 font-semibold rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Submit New Feedback
                </a>
            </div>

            <!-- Sentiment Trend Chart (if available) -->
            @if($sentimentTrend && $sentimentTrend->count() > 0)
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden mb-8 p-6">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                    </svg>
                    Your Sentiment Trend
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($sentimentTrend as $trend)
                    <div class="bg-slate-900/50 border border-slate-700 rounded-lg p-4">
                        <p class="text-slate-400 text-xs font-semibold mb-2">{{ $trend->created_at->format('M d') }}</p>
                        <div class="flex items-end justify-between mb-2">
                            <div>
                                <p class="text-2xl font-bold text-white">{{ round($trend->overall_sentiment * 100) }}%
                                </p>
                                <p class="text-xs text-slate-400 mt-1">{{ $trend->getSentimentLabel() }}</p>
                            </div>
                            <span class="text-2xl">{{ $trend->getTrendIcon() }}</span>
                        </div>
                        @if($trend->needs_manager_attention)
                        <div class="text-amber-300 text-xs font-semibold">⚠️ Manager Review</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($feedbacks->count() > 0)
            <!-- Feedback Table -->
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-700">
                        <thead class="bg-slate-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Week Period</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Feelings</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Progress</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Improvements</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Submitted</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-slate-800/50 divide-y divide-slate-700">
                            @foreach($feedbacks as $feedback)
                            <tr class="hover:bg-slate-700/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-white">
                                        {{ $feedback->created_at->startOfWeek()->format('M d') }} - {{
                                        $feedback->created_at->endOfWeek()->format('M d, Y') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-300 max-w-xs truncate">{{
                                        Str::limit($feedback->feelings, 50) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-300 max-w-xs truncate">{{
                                        Str::limit($feedback->work_progress, 50) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-300 max-w-xs truncate">{{
                                        Str::limit($feedback->self_improvements, 50) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-xs text-slate-400">{{ $feedback->submitted_at->format('M d, Y')
                                        }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($feedback->admin_notes)
                                    <span
                                        class="px-3 py-1 text-xs font-medium rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        Reviewed
                                    </span>
                                    @else
                                    <span
                                        class="px-3 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-300 border border-green-500/30">
                                        Submitted
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('employee.feedback.show', $feedback->id) }}"
                                        class="text-lime-400 hover:text-lime-300 transition">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($feedbacks->hasPages())
                <div class="px-6 py-4 border-t border-slate-700">
                    {{ $feedbacks->links() }}
                </div>
                @endif
            </div>
            @else
            <!-- Empty State -->
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-12 text-center">
                <div class="inline-block p-4 bg-slate-700/50 rounded-full mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">No Feedback Submitted Yet</h3>
                <p class="text-slate-400 mb-6">You haven't submitted any weekly feedback yet. Start by submitting your
                    first feedback!</p>
                <a href="{{ route('employee.feedback.dashboard') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-lime-500 to-green-600 hover:from-lime-600 hover:to-green-700 text-slate-950 font-semibold rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Submit Feedback Now
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>