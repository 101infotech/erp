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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-white">Weekly Feedback</h2>
                    </div>
                    <p class="text-slate-300 text-sm">Quick check-in to understand how you're doing. All responses are confidential.</p>
                </div>

                <!-- Form -->
                <form action="{{ route('employee.feedback.store') }}" method="POST" class="p-6 space-y-8">
                    @csrf

                    <!-- Quick Ratings Section -->
                    <div class="bg-gradient-to-r from-purple-500/10 to-blue-500/10 border border-purple-500/30 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-purple-500/20 rounded-lg">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
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
                                @for($i = 1; $i <= 5; $i++)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="stress_level" value="{{ $i }}" class="peer sr-only" required {{ old('stress_level') == $i ? 'checked' : '' }}>
                                    <div class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-purple-500 peer-checked:bg-purple-500/20 rounded-lg p-3 text-center transition hover:border-purple-400">
                                        <div class="text-lg font-bold text-white mb-1">{{ $i }}</div>
                                        <div class="text-xs text-slate-400 peer-checked:text-purple-300">
                                            @if($i == 1) Low
                                            @elseif($i == 5) High
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
                                @for($i = 1; $i <= 5; $i++)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="mental_wellbeing" value="{{ $i }}" class="peer sr-only" required {{ old('mental_wellbeing') == $i ? 'checked' : '' }}>
                                    <div class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-green-500 peer-checked:bg-green-500/20 rounded-lg p-3 text-center transition hover:border-green-400">
                                        <div class="text-lg font-bold text-white mb-1">{{ $i }}</div>
                                        <div class="text-xs text-slate-400 peer-checked:text-green-300">
                                            @if($i == 1) Poor
                                            @elseif($i == 5) Great
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
                                @for($i = 1; $i <= 5; $i++)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="workload_level" value="{{ $i }}" class="peer sr-only" required {{ old('workload_level') == $i ? 'checked' : '' }}>
                                    <div class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-blue-500 peer-checked:bg-blue-500/20 rounded-lg p-3 text-center transition hover:border-blue-400">
                                        <div class="text-lg font-bold text-white mb-1">{{ $i }}</div>
                                        <div class="text-xs text-slate-400 peer-checked:text-blue-300">
                                            @if($i == 1) Light
                                            @elseif($i == 5) Heavy
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
                                @for($i = 1; $i <= 5; $i++)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="work_satisfaction" value="{{ $i }}" class="peer sr-only" required {{ old('work_satisfaction') == $i ? 'checked' : '' }}>
                                    <div class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-cyan-500 peer-checked:bg-cyan-500/20 rounded-lg p-3 text-center transition hover:border-cyan-400">
                                        <div class="text-lg font-bold text-white mb-1">{{ $i }}</div>
                                        <div class="text-xs text-slate-400 peer-checked:text-cyan-300">
                                            @if($i == 1) Low
                                            @elseif($i == 5) High
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
                                @for($i = 1; $i <= 5; $i++)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="team_collaboration" value="{{ $i }}" class="peer sr-only" required {{ old('team_collaboration') == $i ? 'checked' : '' }}>
                                    <div class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-lime-500 peer-checked:bg-lime-500/20 rounded-lg p-3 text-center transition hover:border-lime-400">
                                        <div class="text-lg font-bold text-white mb-1">{{ $i }}</div>
                                        <div class="text-xs text-slate-400 peer-checked:text-lime-300">
                                            @if($i == 1) Poor
                                            @elseif($i == 5) Excellent
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
                    <div class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-500/30 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-green-500/20 rounded-lg">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">Your Weekly Feedback</h3>
                        </div>
                        
                        <p class="text-slate-300 text-sm mb-4">Share your thoughts about this week - achievements, challenges, concerns, or anything you'd like management to know. (Optional, but encouraged)</p>
                        
                        <textarea name="achievements" rows="6" 
                            placeholder="Example: This week I completed the new dashboard feature and helped onboard a new team member. I'm struggling a bit with the tight deadlines on Project X and could use some support with time management..."
                            class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-3 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500/50 transition @error('achievements') border-red-500 @enderror">{{ old('achievements') }}</textarea>
                        @error('achievements')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                        
                        <!-- Hidden fields for backward compatibility -->
                        <input type="hidden" name="challenges_faced" value="See achievements field">
                        <input type="hidden" name="support_needed" value="">
                        <input type="hidden" name="complaints" value="">
                        </h3>

                        <!-- Stress Level -->
                        <div class="mb-6">
                            <label class="block text-slate-200 font-medium mb-3">
                                How stressed have you felt this week? <span class="text-red-400">*</span>
                            </label>
                            <div class="flex gap-4">
                                @for($i = 1; $i <= 5; $i++)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="stress_level" value="{{ $i }}" class="peer sr-only" required {{ old('stress_level') == $i ? 'checked' : '' }}>
                                    <div class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-purple-500 peer-checked:bg-purple-500/20 rounded-lg p-4 text-center transition hover:border-purple-400">
                                        <div class="text-2xl mb-1">
                                            @if($i == 1) 😌
                                            @elseif($i == 2) 🙂
                                            @elseif($i == 3) 😐
                                            @elseif($i == 4) 😟
                                            @else 😰
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-400 peer-checked:text-white">
                                            @if($i == 1) Not at all
                                            @elseif($i == 2) A little
                                            @elseif($i == 3) Moderate
                                            @elseif($i == 4) Quite a bit
                                            @else Very much
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
                                How would you rate your overall mental wellbeing? <span class="text-red-400">*</span>
                            </label>
                            <div class="flex gap-4">
                                @for($i = 1; $i <= 5; $i++)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="mental_wellbeing" value="{{ $i }}" class="peer sr-only" required {{ old('mental_wellbeing') == $i ? 'checked' : '' }}>
                                    <div class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-green-500 peer-checked:bg-green-500/20 rounded-lg p-4 text-center transition hover:border-green-400">
                                        <div class="text-2xl mb-1">
                                            @if($i == 1) 😢
                                            @elseif($i == 2) 😕
                                            @elseif($i == 3) 😊
                                            @elseif($i == 4) 😄
                                            @else 🤩
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-400 peer-checked:text-white">{{ $i }}</div>
                                    </div>
                                </label>
                                @endfor
                            </div>
                            @error('mental_wellbeing')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Work Performance Section -->
                    <div class="bg-gradient-to-r from-blue-500/10 to-cyan-500/10 border border-blue-500/30 rounded-xl p-6">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                            <span class="text-2xl">💼</span> Work Performance
                        </h3>

                        <!-- Workload Level -->
                        <div class="mb-6">
                            <label class="block text-slate-200 font-medium mb-3">
                                How would you rate your workload this week? <span class="text-red-400">*</span>
                            </label>
                            <div class="flex gap-4">
                                @for($i = 1; $i <= 5; $i++)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="workload_level" value="{{ $i }}" class="peer sr-only" required {{ old('workload_level') == $i ? 'checked' : '' }}>
                                    <div class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-blue-500 peer-checked:bg-blue-500/20 rounded-lg p-4 text-center transition hover:border-blue-400">
                                        <div class="text-xs text-slate-400 peer-checked:text-white font-medium">
                                            @if($i == 1) Very Light
                                            @elseif($i == 2) Light
                                            @elseif($i == 3) Just Right
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
                                How satisfied are you with your work this week? <span class="text-red-400">*</span>
                            </label>
                            <div class="flex gap-4">
                                @for($i = 1; $i <= 5; $i++)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="work_satisfaction" value="{{ $i }}" class="peer sr-only" required {{ old('work_satisfaction') == $i ? 'checked' : '' }}>
                                    <div class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-cyan-500 peer-checked:bg-cyan-500/20 rounded-lg p-4 text-center transition hover:border-cyan-400">
                                        <div class="text-2xl mb-1">⭐</div>
                                        <div class="text-xs text-slate-400 peer-checked:text-white">{{ $i }}</div>
                                    </div>
                                </label>
                                @endfor
                            </div>
                            @error('work_satisfaction')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Team Collaboration -->
                        <div class="mb-6">
                            <label class="block text-slate-200 font-medium mb-3">
                                How effective was team collaboration this week? <span class="text-red-400">*</span>
                            </label>
                            <div class="flex gap-4">
                                @for($i = 1; $i <= 5; $i++)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="team_collaboration" value="{{ $i }}" class="peer sr-only" required {{ old('team_collaboration') == $i ? 'checked' : '' }}>
                                    <div class="bg-slate-900/50 border-2 border-slate-600 peer-checked:border-lime-500 peer-checked:bg-lime-500/20 rounded-lg p-4 text-center transition hover:border-lime-400">
                                        <div class="text-2xl mb-1">🤝</div>
                                        <div class="text-xs text-slate-400 peer-checked:text-white">{{ $i }}</div>
                                    </div>
                                </label>
                                @endfor
                            </div>
                            @error('team_collaboration')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Weekly Reflection -->
                    <div class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-500/30 rounded-xl p-6">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                            <span class="text-2xl">✨</span> Weekly Reflection
                        </h3>

                        <!-- Achievements -->
                        <div class="mb-6">
                            <label class="block text-slate-200 font-medium mb-2">
                                What went well this week? (Achievements/Wins) <span class="text-red-400">*</span>
                            </label>
                            <textarea name="achievements" rows="4" 
                                placeholder="Share your accomplishments, completed tasks, or positive moments... (minimum 10 characters)"
                                class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-3 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500/50 transition @error('achievements') border-red-500 @enderror"
                                required>{{ old('achievements') }}</textarea>
                            @error('achievements')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Challenges -->
                        <div class="mb-6">
                            <label class="block text-slate-200 font-medium mb-2">
                                What challenges did you face this week? <span class="text-red-400">*</span>
                            </label>
                            <textarea name="challenges_faced" rows="4" 
                                placeholder="Describe any obstacles, difficulties, or roadblocks you encountered... (minimum 10 characters)"
                                class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-3 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500/50 transition @error('challenges_faced') border-red-500 @enderror"
                                required>{{ old('challenges_faced') }}</textarea>
                            @error('challenges_faced')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Support Needed -->
                        <div class="mb-6">
                            <label class="block text-slate-200 font-medium mb-2">
                                What support do you need from management? (Optional)
                            </label>
                            <textarea name="support_needed" rows="3" 
                                placeholder="Let us know how we can better support you..."
                                class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-3 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/50 transition @error('support_needed') border-red-500 @enderror">{{ old('support_needed') }}</textarea>
                            @error('support_needed')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Complaints Section -->
                    <div class="bg-gradient-to-r from-red-500/10 to-orange-500/10 border border-red-500/30 rounded-xl p-6">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                            <span class="text-2xl">💬</span> Concerns or Complaints
                        </h3>
                        <div>
                            <label class="block text-slate-200 font-medium mb-2">
                                Do you have any concerns, complaints, or feedback about work conditions, processes, or team dynamics? (Optional & Confidential)
                            </label>
                            <textarea name="complaints" rows="4" 
                                placeholder="Your feedback is confidential and will help us improve the work environment..."
                                class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-3 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500/50 transition @error('complaints') border-red-500 @enderror">{{ old('complaints') }}</textarea>
                            @error('complaints')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center gap-4">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-lime-500 to-green-600 hover:from-lime-600 hover:to-green-700 text-white font-bold py-4 px-8 rounded-xl transition transform hover:scale-105 shadow-lg shadow-lime-500/20">
                            Submit Weekly Feedback
                        </button>
                        <a href="{{ route('employee.feedback.dashboard') }}"
                            class="px-6 py-4 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-xl transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
