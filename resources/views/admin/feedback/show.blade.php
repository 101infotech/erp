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
            <!-- How They're Feeling -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="bg-blue-500/20 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">How They're Feeling</h3>
                </div>
                <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                    <p class="text-slate-300 whitespace-pre-wrap">{{ $feedback->feelings }}</p>
                </div>
            </div>

            <!-- Work Progress -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="bg-green-500/20 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Work Progress This Week</h3>
                </div>
                <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                    <p class="text-slate-300 whitespace-pre-wrap">{{ $feedback->work_progress }}</p>
                </div>
            </div>

            <!-- Self-Improvement -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="bg-purple-500/20 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Areas for Self-Improvement</h3>
                </div>
                <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                    <p class="text-slate-300 whitespace-pre-wrap">{{ $feedback->self_improvements }}</p>
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