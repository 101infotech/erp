<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <a href="{{ route('employee.feedback.dashboard') }}"
                class="inline-flex items-center text-sm text-slate-400 hover:text-lime-400 transition mb-6">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Feedback Dashboard
            </a>

            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-slate-700 bg-gradient-to-r from-purple-900/30 to-blue-900/30">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-white">Weekly Feedback</h2>
                    </div>
                    <p class="text-slate-300 text-sm">Quick check-in to understand how you're doing. All responses are
                        confidential.</p>
                </div>

                <!-- Form -->
                <form action="{{ route('employee.feedback.store') }}" method="POST" class="p-6 space-y-8">
                    @csrf

                    <!-- Quick Ratings Section -->
                    <div
                        class="bg-gradient-to-r from-purple-500/10 to-blue-500/10 border border-purple-500/30 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-purple-500/20 rounded-lg">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">Quick Ratings</h3>
                        </div>

                        <!-- Stress Level -->
                        <div class="mb-6">
                            <label class="block text-slate-200 font-medium mb-3">
                                Stress Level <span class="text-red-400">*</span>
                            </label>
                            <div class="flex gap-3">
                                @for($i = 1; $i <= 5; $i++) <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="stress_level" value="{{ $i }}" class="peer sr-only"
                                        required {{ old('stress_level')==$i ? 'checked' : '' }}>
                                    <div
                                        class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-purple-500 peer-checked:bg-purple-500/20 rounded-lg p-4 text-center transition hover:border-purple-400 h-full min-h-24">
                                        <div class="text-2xl font-bold text-white mb-2">{{ $i }}</div>
                                        <div class="text-xs text-slate-400 peer-checked:text-purple-300">
                                            @if($i == 1) None
                                            @elseif($i == 2) Low
                                            @elseif($i == 3) Medium
                                            @elseif($i == 4) High
                                            @else Very High
                                            @endif
                                        </div>
                                    </div>
                                    </label>
                                    @endfor
                            </div>
                            @error('stress_level')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mental Wellbeing -->
                        <div class="mb-6">
                            <label class="block text-slate-200 font-medium mb-3">
                                Mental Wellbeing <span class="text-red-400">*</span>
                            </label>
                            <div class="flex gap-3">
                                @for($i = 1; $i <= 5; $i++) <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="mental_wellbeing" value="{{ $i }}" class="peer sr-only"
                                        required {{ old('mental_wellbeing')==$i ? 'checked' : '' }}>
                                    <div
                                        class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-green-500 peer-checked:bg-green-500/20 rounded-lg p-4 text-center transition hover:border-green-400 h-full min-h-24">
                                        <div class="text-2xl font-bold text-white mb-2">{{ $i }}</div>
                                        <div class="text-xs text-slate-400 peer-checked:text-green-300">
                                            @if($i == 1) Poor
                                            @elseif($i == 2) Fair
                                            @elseif($i == 3) Good
                                            @elseif($i == 4) Very Good
                                            @else Excellent
                                            @endif
                                        </div>
                                    </div>
                                    </label>
                                    @endfor
                            </div>
                            @error('mental_wellbeing')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Workload Level -->
                        <div class="mb-6">
                            <label class="block text-slate-200 font-medium mb-3">
                                Workload <span class="text-red-400">*</span>
                            </label>
                            <div class="flex gap-3">
                                @for($i = 1; $i <= 5; $i++) <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="workload_level" value="{{ $i }}" class="peer sr-only"
                                        required {{ old('workload_level')==$i ? 'checked' : '' }}>
                                    <div
                                        class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-blue-500 peer-checked:bg-blue-500/20 rounded-lg p-4 text-center transition hover:border-blue-400 h-full min-h-24">
                                        <div class="text-2xl font-bold text-white mb-2">{{ $i }}</div>
                                        <div class="text-xs text-slate-400 peer-checked:text-blue-300">
                                            @if($i == 1) Very Light
                                            @elseif($i == 2) Light
                                            @elseif($i == 3) Moderate
                                            @elseif($i == 4) Heavy
                                            @else Overwhelming
                                            @endif
                                        </div>
                                    </div>
                                    </label>
                                    @endfor
                            </div>
                            @error('workload_level')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Work Satisfaction -->
                        <div class="mb-6">
                            <label class="block text-slate-200 font-medium mb-3">
                                Job Satisfaction <span class="text-red-400">*</span>
                            </label>
                            <div class="flex gap-3">
                                @for($i = 1; $i <= 5; $i++) <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="work_satisfaction" value="{{ $i }}" class="peer sr-only"
                                        required {{ old('work_satisfaction')==$i ? 'checked' : '' }}>
                                    <div
                                        class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-cyan-500 peer-checked:bg-cyan-500/20 rounded-lg p-4 text-center transition hover:border-cyan-400 h-full min-h-24">
                                        <div class="text-2xl font-bold text-white mb-2">{{ $i }}</div>
                                        <div class="text-xs text-slate-400 peer-checked:text-cyan-300">
                                            @if($i == 1) Very Low
                                            @elseif($i == 2) Low
                                            @elseif($i == 3) Moderate
                                            @elseif($i == 4) High
                                            @else Very High
                                            @endif
                                        </div>
                                    </div>
                                    </label>
                                    @endfor
                            </div>
                            @error('work_satisfaction')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Team Collaboration -->
                        <div>
                            <label class="block text-slate-200 font-medium mb-3">
                                Team Collaboration <span class="text-red-400">*</span>
                            </label>
                            <div class="flex gap-3">
                                @for($i = 1; $i <= 5; $i++) <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="team_collaboration" value="{{ $i }}" class="peer sr-only"
                                        required {{ old('team_collaboration')==$i ? 'checked' : '' }}>
                                    <div
                                        class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-lime-500 peer-checked:bg-lime-500/20 rounded-lg p-4 text-center transition hover:border-lime-400 h-full min-h-24">
                                        <div class="text-2xl font-bold text-white mb-2">{{ $i }}</div>
                                        <div class="text-xs text-slate-400 peer-checked:text-lime-300">
                                            @if($i == 1) Poor
                                            @elseif($i == 2) Fair
                                            @elseif($i == 3) Good
                                            @elseif($i == 4) Very Good
                                            @else Excellent
                                            @endif
                                        </div>
                                    </div>
                                    </label>
                                    @endfor
                            </div>
                            @error('team_collaboration')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Weekly Feedback Section -->
                    <div
                        class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-500/30 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-green-500/20 rounded-lg">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">Your Weekly Feedback</h3>
                        </div>

                        <p class="text-slate-300 text-sm mb-4">Share your thoughts about this week - achievements,
                            challenges, concerns, or anything you'd like management to know. (Optional)</p>

                        <textarea name="achievements" rows="6"
                            placeholder="Example: This week I completed the new dashboard feature and helped onboard a new team member. I'm struggling a bit with the tight deadlines on Project X and could use some support with time management..."
                            class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-3 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500/50 transition @error('achievements') border-red-500 @enderror">{{ old('achievements') }}</textarea>
                        @error('achievements')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror

                        <!-- Hidden fields for backward compatibility -->
                        <input type="hidden" name="challenges_faced" value="See main feedback">
                        <input type="hidden" name="support_needed" value="">
                        <input type="hidden" name="complaints" value="">
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-3 justify-end pt-4 border-t border-slate-700">
                        <a href="{{ route('employee.feedback.dashboard') }}"
                            class="px-6 py-3 bg-slate-700 text-white rounded-lg hover:bg-slate-600 font-medium transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg hover:from-green-600 hover:to-emerald-600 font-semibold transition flex items-center gap-2 shadow-lg shadow-green-500/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Submit Weekly Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>