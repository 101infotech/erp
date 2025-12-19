<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Weekly Feedback</h1>
                <p class="text-slate-400">Share your weekly feelings, progress, and growth areas</p>
            </div>

            <!-- Mandatory Reminder -->
            <div
                class="bg-gradient-to-r from-lime-500/10 to-green-500/10 border border-lime-500/30 rounded-lg p-6 mb-8">
                <div class="flex items-start gap-4">
                    <div class="bg-lime-500/20 p-3 rounded-lg flex-shrink-0">
                        <svg class="w-6 h-6 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-1">Weekly Feedback Due</h3>
                        <p class="text-slate-300 mb-2">This feedback is due every Friday. Your honest input helps us
                            understand team dynamics and support your growth.</p>
                        @if($daysUntilFriday > 0)
                        <p class="text-lime-300 text-sm font-medium">⏰ {{ $daysUntilFriday }} day{{ $daysUntilFriday > 1
                            ? 's' : '' }} remaining</p>
                        @else
                        <p class="text-red-300 text-sm font-medium">⚠️ Due today!</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status Message -->
            @if($isSubmitted)
            <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-6 mb-8">
                <div class="flex items-start gap-4">
                    <div class="bg-green-500/20 p-3 rounded-lg flex-shrink-0">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-green-400">Feedback Submitted</h3>
                        <p class="text-slate-300 mt-1">You've already submitted your feedback for this week.</p>

                        <!-- AI Sentiment Analysis Display -->
                        @if($latestSentiment)
                        <div class="mt-4 p-4 bg-slate-900/50 border border-slate-700 rounded-lg">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-slate-400 text-xs font-semibold mb-1">SENTIMENT ANALYSIS</p>
                                    <p class="text-2xl font-bold text-white">{{ $latestSentiment->getSentimentLabel() }}
                                    </p>
                                    <p class="text-slate-400 text-xs mt-1">Overall: {{
                                        round($latestSentiment->overall_sentiment * 100) }}%</p>
                                </div>
                                <div>
                                    <p class="text-slate-400 text-xs font-semibold mb-1">TREND</p>
                                    <p class="text-2xl font-bold text-white">{{ $latestSentiment->getTrendIcon() }} {{
                                        ucfirst($latestSentiment->trend_direction) }}</p>
                                    @if($latestSentiment->trend_change)
                                    <p class="text-slate-400 text-xs mt-1">{{ $latestSentiment->trend_change > 0 ? '+' :
                                        '' }}{{ round($latestSentiment->trend_change * 100) }}% from last week</p>
                                    @endif
                                </div>
                            </div>
                            @if($latestSentiment->alert_reason)
                            <div class="mt-3 pt-3 border-t border-slate-700">
                                <p class="text-amber-300 text-sm flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $latestSentiment->alert_reason }}</span>
                                </p>
                            </div>
                            @endif
                        </div>
                        @endif

                        <div class="mt-4 flex gap-3">
                            <a href="{{ route('employee.feedback.show', $weeklyFeedback) }}"
                                class="px-4 py-2 bg-green-500/20 text-green-300 border border-green-500/30 rounded-lg hover:bg-green-500/30 transition text-sm font-medium">
                                View Your Feedback
                            </a>
                            <a href="{{ route('employee.feedback.history') }}"
                                class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition text-sm font-medium">
                                View History
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- Submit Form -->
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-white mb-6">Submit Your Weekly Feedback</h2>

                    <form method="POST" action="{{ route('employee.feedback.store') }}" class="space-y-6">
                        @csrf

                        <!-- How are you feeling? -->
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">
                                How Are You Feeling This Week? <span class="text-red-400">*</span>
                            </label>
                            <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-3 mb-3">
                                <p class="text-blue-300 text-sm">Share your emotional state, stress level, morale, and
                                    overall well-being at work.</p>
                            </div>
                            <textarea name="feelings" rows="4" required
                                class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500"
                                placeholder="e.g., I'm feeling energized this week. The team collaboration has been great, though I'm a bit stressed about the upcoming deadline...">{{ old('feelings', $weeklyFeedback?->feelings ?? '') }}</textarea>
                            @error('feelings')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Work Progress -->
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">
                                What Progress Did You Make This Week? <span class="text-red-400">*</span>
                            </label>
                            <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-3 mb-3">
                                <p class="text-green-300 text-sm">Share accomplishments, milestones, completed tasks, or
                                    projects you worked on (not a detailed report).</p>
                            </div>
                            <textarea name="work_progress" rows="4" required
                                class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500"
                                placeholder="e.g., Completed the UI design for feature X, helped resolve 5 customer issues, attended team sync meetings...">{{ old('work_progress', $weeklyFeedback?->work_progress ?? '') }}</textarea>
                            @error('work_progress')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Self-Improvement -->
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">
                                What Areas Can You Improve? <span class="text-red-400">*</span>
                            </label>
                            <div class="bg-purple-500/10 border border-purple-500/30 rounded-lg p-3 mb-3">
                                <p class="text-purple-300 text-sm">Share skills you want to develop, challenges you
                                    faced, or personal growth goals.</p>
                            </div>
                            <textarea name="self_improvements" rows="4" required
                                class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500"
                                placeholder="e.g., I want to improve my communication skills, need to work on time management, would like to learn more about X technology...">{{ old('self_improvements', $weeklyFeedback?->self_improvements ?? '') }}</textarea>
                            @error('self_improvements')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-3 justify-end pt-4 border-t border-slate-700">
                            <a href="{{ route('employee.dashboard') }}"
                                class="px-6 py-3 bg-slate-700 text-white rounded-lg hover:bg-slate-600 font-medium transition">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-6 py-3 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-semibold transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
        class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        {{ session('success') }}
    </div>
    @endif
</x-app-layout>