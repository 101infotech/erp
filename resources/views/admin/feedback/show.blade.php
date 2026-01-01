@extends('admin.layouts.app')

@section('title', 'Feedback Details')
@section('page-title', 'Feedback Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.feedback.index') }}"
        class="inline-flex items-center text-sm text-slate-400 hover:text-lime-400 transition mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Feedback
    </a>

    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
        <!-- Employee Info -->
        <div class="p-6 border-b border-slate-700 bg-gradient-to-r from-slate-900 to-slate-800">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div
                        class="h-16 w-16 rounded-full bg-gradient-to-br from-lime-500 to-lime-600 flex items-center justify-center">
                        <span class="text-slate-900 font-bold text-2xl">{{ strtoupper(substr($feedback->user->name, 0,
                            1)) }}</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">{{ $feedback->user->name }}</h2>
                        <p class="text-slate-400 text-sm mt-1">{{ $feedback->user->email }}</p>
                        <p class="text-slate-500 text-xs mt-2">Submitted: {{ $feedback->submitted_at->format('M d, Y h:i
                            A') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span
                        class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-300 border border-green-500/30">
                        Submitted
                    </span>
                </div>
            </div>
        </div>

        <!-- Feedback Content -->
        <div class="p-6 space-y-8">
            <!-- Rating Scales Section -->
            <div class="bg-gradient-to-r from-purple-500/10 to-blue-500/10 border border-purple-500/30 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-purple-500/20 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Weekly Ratings</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Stress Level -->
                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                        <p class="text-slate-400 text-sm font-medium mb-2">Stress Level</p>
                        <div class="flex items-center gap-2">
                            <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                <span class="text-xl font-bold text-purple-400">{{ $feedback->stress_level ?? '-'
                                    }}</span>
                            </div>
                            <p class="text-slate-300 text-sm">
                                @if($feedback->stress_level == 1) None
                                @elseif($feedback->stress_level == 2) Low
                                @elseif($feedback->stress_level == 3) Medium
                                @elseif($feedback->stress_level == 4) High
                                @elseif($feedback->stress_level == 5) Very High
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Mental Wellbeing -->
                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                        <p class="text-slate-400 text-sm font-medium mb-2">Mental Wellbeing</p>
                        <div class="flex items-center gap-2">
                            <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                                <span class="text-xl font-bold text-green-400">{{ $feedback->mental_wellbeing ?? '-'
                                    }}</span>
                            </div>
                            <p class="text-slate-300 text-sm">
                                @if($feedback->mental_wellbeing == 1) Poor
                                @elseif($feedback->mental_wellbeing == 2) Fair
                                @elseif($feedback->mental_wellbeing == 3) Good
                                @elseif($feedback->mental_wellbeing == 4) Very Good
                                @elseif($feedback->mental_wellbeing == 5) Excellent
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Workload Level -->
                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                        <p class="text-slate-400 text-sm font-medium mb-2">Workload</p>
                        <div class="flex items-center gap-2">
                            <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <span class="text-xl font-bold text-blue-400">{{ $feedback->workload_level ?? '-'
                                    }}</span>
                            </div>
                            <p class="text-slate-300 text-sm">
                                @if($feedback->workload_level == 1) Very Light
                                @elseif($feedback->workload_level == 2) Light
                                @elseif($feedback->workload_level == 3) Moderate
                                @elseif($feedback->workload_level == 4) Heavy
                                @elseif($feedback->workload_level == 5) Overwhelming
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Job Satisfaction -->
                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                        <p class="text-slate-400 text-sm font-medium mb-2">Job Satisfaction</p>
                        <div class="flex items-center gap-2">
                            <div class="w-12 h-12 bg-cyan-500/20 rounded-lg flex items-center justify-center">
                                <span class="text-xl font-bold text-cyan-400">{{ $feedback->work_satisfaction ?? '-'
                                    }}</span>
                            </div>
                            <p class="text-slate-300 text-sm">
                                @if($feedback->work_satisfaction == 1) Very Low
                                @elseif($feedback->work_satisfaction == 2) Low
                                @elseif($feedback->work_satisfaction == 3) Moderate
                                @elseif($feedback->work_satisfaction == 4) High
                                @elseif($feedback->work_satisfaction == 5) Very High
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Team Collaboration -->
                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                        <p class="text-slate-400 text-sm font-medium mb-2">Team Collaboration</p>
                        <div class="flex items-center gap-2">
                            <div class="w-12 h-12 bg-lime-500/20 rounded-lg flex items-center justify-center">
                                <span class="text-xl font-bold text-lime-400">{{ $feedback->team_collaboration ?? '-'
                                    }}</span>
                            </div>
                            <p class="text-slate-300 text-sm">
                                @if($feedback->team_collaboration == 1) Poor
                                @elseif($feedback->team_collaboration == 2) Fair
                                @elseif($feedback->team_collaboration == 3) Good
                                @elseif($feedback->team_collaboration == 4) Very Good
                                @elseif($feedback->team_collaboration == 5) Excellent
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weekly Feedback Section -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="bg-green-500/20 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Employee Feedback</h3>
                </div>
                <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                    <p class="text-slate-300 whitespace-pre-wrap">{{ $feedback->achievements ?? 'No feedback provided'
                        }}</p>
                </div>
            </div>

            <!-- Admin Notes -->
            <div class="border-t border-slate-700 pt-8">
                <h3 class="text-lg font-semibold text-white mb-3">Admin Notes</h3>
                <form method="POST" action="{{ route('admin.feedback.add-notes', $feedback) }}" class="space-y-3">
                    @csrf
                    <textarea name="admin_notes" rows="5"
                        class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500"
                        placeholder="Add your response or notes here...">{{ $feedback->admin_notes }}</textarea>
                    <button type="submit"
                        class="px-4 py-2 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-medium transition">
                        Save Notes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
    class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
    {{ session('success') }}
</div>
@endif
@endsection