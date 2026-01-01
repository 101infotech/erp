@extends('admin.layouts.app')

@section('title', 'Feedback Analytics')
@section('page-title', 'Feedback Analytics & Insights')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.feedback.index') }}"
        class="inline-flex items-center text-sm text-slate-400 hover:text-lime-400 transition mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Feedback
    </a>

    <h2 class="text-2xl font-bold text-white mb-4">Weekly Feedback Analytics</h2>
</div>

<!-- Key Metrics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-500/20 to-blue-600/20 border border-blue-500/30 rounded-lg p-4">
        <p class="text-blue-200 text-sm font-medium">Submission Rate</p>
        <p class="text-3xl font-bold text-white mt-1">{{ $submissionRate }}%</p>
        <p class="text-blue-300 text-xs mt-2">{{ $submitted }} of {{ $totalEmployees }} submitted</p>
    </div>

    <div class="bg-gradient-to-br from-green-500/20 to-green-600/20 border border-green-500/30 rounded-lg p-4">
        <p class="text-green-200 text-sm font-medium">Feedbacks Received</p>
        <p class="text-3xl font-bold text-white mt-1">{{ $submitted }}</p>
        <p class="text-green-300 text-xs mt-2">This week</p>
    </div>

    <div class="bg-gradient-to-br from-purple-500/20 to-purple-600/20 border border-purple-500/30 rounded-lg p-4">
        <p class="text-purple-200 text-sm font-medium">Total Employees</p>
        <p class="text-3xl font-bold text-white mt-1">{{ $totalEmployees }}</p>
        <p class="text-purple-300 text-xs mt-2">In organization</p>
    </div>
</div>

<!-- Feedback Overview -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Average Ratings -->
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h3 class="text-lg font-semibold text-white">Team Ratings Overview</h3>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-sm">Average Stress Level</span>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-purple-400">{{ number_format($avgStress, 1) }}/5</span>
                    <div class="w-24 h-2 bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-500" style="width: {{ ($avgStress / 5) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-sm">Mental Wellbeing</span>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-green-400">{{ number_format($avgWellbeing, 1) }}/5</span>
                    <div class="w-24 h-2 bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500" style="width: {{ ($avgWellbeing / 5) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-sm">Workload</span>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-blue-400">{{ number_format($avgWorkload, 1) }}/5</span>
                    <div class="w-24 h-2 bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500" style="width: {{ ($avgWorkload / 5) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-sm">Job Satisfaction</span>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-cyan-400">{{ number_format($avgSatisfaction, 1) }}/5</span>
                    <div class="w-24 h-2 bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-cyan-500" style="width: {{ ($avgSatisfaction / 5) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-sm">Team Collaboration</span>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-lime-400">{{ number_format($avgCollaboration, 1) }}/5</span>
                    <div class="w-24 h-2 bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-lime-500" style="width: {{ ($avgCollaboration / 5) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-700">
            <p class="text-xs text-slate-500">Average ratings from all submitted feedback</p>
        </div>
    </div>

    <!-- Common Progress Points -->
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <h3 class="text-lg font-semibold text-white">Employee Feedback Summary</h3>
        </div>
        <div class="space-y-2 text-sm text-slate-300">
            <p class="line-clamp-6">{{ $allProgress ?? 'Feedback data being aggregated...' }}</p>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-700">
            <p class="text-xs text-slate-500">Latest employee feedback and comments</p>
        </div>
    </div>
</div>

<!-- Individual Feedbacks List -->
<div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
    <h3 class="text-lg font-semibold text-white mb-4">All Submitted Feedbacks</h3>

    <div class="space-y-4">
        @forelse($feedbacks as $feedback)
        <div class="bg-slate-900/50 rounded-lg p-5 border border-slate-700 hover:border-lime-500/30 transition">
            <div class="flex items-start justify-between mb-4">
                <h4 class="font-semibold text-white text-lg">{{ $feedback->user->name }}</h4>
                <span class="text-xs text-slate-500 bg-slate-800 px-2 py-1 rounded">{{
                    $feedback->submitted_at->format('M d, Y') }}</span>
            </div>

            <!-- Ratings Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mb-4">
                <div
                    class="bg-gradient-to-br from-purple-500/10 to-purple-600/10 border border-purple-500/30 rounded-lg p-3 text-center">
                    <p class="text-xs text-purple-300 mb-1 font-medium">Stress</p>
                    <p class="text-2xl font-bold text-purple-400">{{ $feedback->stress_level ?? '-' }}</p>
                    <p class="text-xs text-slate-400">/5</p>
                </div>
                <div
                    class="bg-gradient-to-br from-green-500/10 to-green-600/10 border border-green-500/30 rounded-lg p-3 text-center">
                    <p class="text-xs text-green-300 mb-1 font-medium">Wellbeing</p>
                    <p class="text-2xl font-bold text-green-400">{{ $feedback->mental_wellbeing ?? '-' }}</p>
                    <p class="text-xs text-slate-400">/5</p>
                </div>
                <div
                    class="bg-gradient-to-br from-blue-500/10 to-blue-600/10 border border-blue-500/30 rounded-lg p-3 text-center">
                    <p class="text-xs text-blue-300 mb-1 font-medium">Workload</p>
                    <p class="text-2xl font-bold text-blue-400">{{ $feedback->workload_level ?? '-' }}</p>
                    <p class="text-xs text-slate-400">/5</p>
                </div>
                <div
                    class="bg-gradient-to-br from-cyan-500/10 to-cyan-600/10 border border-cyan-500/30 rounded-lg p-3 text-center">
                    <p class="text-xs text-cyan-300 mb-1 font-medium">Satisfaction</p>
                    <p class="text-2xl font-bold text-cyan-400">{{ $feedback->work_satisfaction ?? '-' }}</p>
                    <p class="text-xs text-slate-400">/5</p>
                </div>
                <div
                    class="bg-gradient-to-br from-lime-500/10 to-lime-600/10 border border-lime-500/30 rounded-lg p-3 text-center">
                    <p class="text-xs text-lime-300 mb-1 font-medium">Collaboration</p>
                    <p class="text-2xl font-bold text-lime-400">{{ $feedback->team_collaboration ?? '-' }}</p>
                    <p class="text-xs text-slate-400">/5</p>
                </div>
            </div>

            <!-- Feedback Text -->
            @if($feedback->achievements)
            <div class="bg-slate-800/50 rounded-lg p-3 mb-3 border-l-4 border-green-500">
                <p class="text-xs text-slate-400 font-medium mb-1">Employee Feedback:</p>
                <p class="text-sm text-slate-300 line-clamp-2">{{ $feedback->achievements }}</p>
            </div>
            @endif

            <!-- View Button -->
            <div class="flex justify-end">
                <a href="{{ route('admin.feedback.show', $feedback) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-lime-500/20 text-lime-400 border border-lime-500/30 rounded-lg hover:bg-lime-500/30 transition text-sm font-medium">
                    View Full Details
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="text-slate-400 font-medium">No feedbacks submitted yet</p>
            <p class="text-slate-500 text-sm mt-1">Feedbacks will appear here when employees submit them</p>
        </div>
        @endforelse
    </div>
</div>

@endsection