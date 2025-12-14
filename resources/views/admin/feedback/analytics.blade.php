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
    <!-- Common Feelings -->
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-semibold text-white">Team Sentiments</h3>
        </div>
        <div class="space-y-2 text-sm text-slate-300">
            <p class="line-clamp-6">{{ $allFeelings }}</p>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-700">
            <p class="text-xs text-slate-500">Summary of how employees are feeling this week</p>
        </div>
    </div>

    <!-- Common Progress Points -->
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <h3 class="text-lg font-semibold text-white">Team Progress</h3>
        </div>
        <div class="space-y-2 text-sm text-slate-300">
            <p class="line-clamp-6">{{ $allProgress }}</p>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-700">
            <p class="text-xs text-slate-500">Achievements and milestones accomplished</p>
        </div>
    </div>
</div>

<!-- Self-Improvement Areas -->
<div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6 mb-6">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
        </svg>
        <h3 class="text-lg font-semibold text-white">Self-Improvement Focus Areas</h3>
    </div>
    <div class="text-sm text-slate-300 space-y-3">
        <p>{{ $allImprovements }}</p>
    </div>
    <div class="mt-4 pt-4 border-t border-slate-700">
        <p class="text-xs text-slate-500">Areas where employees want to grow and improve</p>
    </div>
</div>

<!-- Individual Feedbacks List -->
<div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
    <h3 class="text-lg font-semibold text-white mb-4">All Submitted Feedbacks</h3>

    <div class="space-y-4">
        @forelse($feedbacks as $feedback)
        <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700 hover:border-lime-500/30 transition">
            <div class="flex items-start justify-between mb-3">
                <h4 class="font-medium text-white">{{ $feedback->user->name }}</h4>
                <span class="text-xs text-slate-500">{{ $feedback->submitted_at->format('M d, Y') }}</span>
            </div>

            <div class="space-y-2 text-sm text-slate-300">
                <p><span class="text-slate-400">Feeling:</span> {{ Str::limit($feedback->feelings, 80) }}</p>
                <p><span class="text-slate-400">Progress:</span> {{ Str::limit($feedback->work_progress, 80) }}</p>
                <p><span class="text-slate-400">Growth Area:</span> {{ Str::limit($feedback->self_improvements, 80) }}
                </p>
            </div>

            <div class="mt-3 pt-3 border-t border-slate-700">
                <a href="{{ route('admin.feedback.show', $feedback) }}"
                    class="text-xs text-lime-400 hover:text-lime-300 transition">
                    View Full Feedback →
                </a>
            </div>
        </div>
        @empty
        <p class="text-slate-400 text-center py-8">No feedbacks submitted yet</p>
        @endforelse
    </div>
</div>

@endsection