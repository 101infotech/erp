<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <a href="{{ route('employee.feedback.dashboard') }}"
                class="inline-flex items-center text-sm text-slate-400 hover:text-lime-400 transition mb-6">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Feedback
            </a>

            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-slate-700 bg-gradient-to-r from-slate-900 to-slate-800">
                    <h2 class="text-2xl font-bold text-white">Submit Your Weekly Feedback</h2>
                    <p class="text-slate-400 text-sm mt-1">Share your thoughts, progress, and areas for growth</p>
                </div>

                <!-- Form -->
                <form action="{{ route('employee.feedback.store') }}" method="POST" class="p-6 space-y-8">
                    @csrf

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
                            <label class="text-lg font-semibold text-white">How Are You Feeling?</label>
                        </div>
                        <p class="text-slate-400 text-sm mb-3">Share your emotions, mood, or general well-being this
                            week.</p>
                        <textarea name="feelings" rows="5" placeholder="I've been feeling... (at least 10 characters)"
                            class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-3 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/50 transition @error('feelings') border-red-500 @enderror"
                            required>{{ old('feelings') }}</textarea>
                        @error('feelings')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
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
                            <label class="text-lg font-semibold text-white">What Progress Did You Make?</label>
                        </div>
                        <p class="text-slate-400 text-sm mb-3">Describe what you accomplished, completed, or improved
                            this week - casual and conversational.</p>
                        <textarea name="work_progress" rows="5"
                            placeholder="This week I made progress on... (at least 10 characters)"
                            class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-3 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500/50 transition @error('work_progress') border-red-500 @enderror"
                            required>{{ old('work_progress') }}</textarea>
                        @error('work_progress')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Self-Improvements -->
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="bg-purple-500/20 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                            </div>
                            <label class="text-lg font-semibold text-white">What Can You Improve?</label>
                        </div>
                        <p class="text-slate-400 text-sm mb-3">What skills would you like to develop or what areas could
                            you work on next week?</p>
                        <textarea name="self_improvements" rows="5"
                            placeholder="Next week I want to focus on... (at least 10 characters)"
                            class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-3 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/50 transition @error('self_improvements') border-red-500 @enderror"
                            required>{{ old('self_improvements') }}</textarea>
                        @error('self_improvements')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-4 pt-6 border-t border-slate-700">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-lime-500 to-green-600 hover:from-lime-600 hover:to-green-700 text-slate-950 font-semibold py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-lime-500/50">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Submit Feedback
                            </span>
                        </button>
                        <a href="{{ route('employee.feedback.dashboard') }}"
                            class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>