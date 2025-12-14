@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('employee.announcements.index') }}"
                class="inline-flex items-center gap-2 text-slate-400 hover:text-white transition mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Announcements
            </a>
        </div>

        <!-- Announcement Card -->
        <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700 overflow-hidden">
            <!-- Header -->
            <div class="p-6 border-b border-slate-700 bg-slate-800/80">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-white mb-3">{{ $announcement->title }}</h1>
                        <div class="flex items-center gap-4 text-sm text-slate-400">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ $announcement->creator->name }}</span>
                            </div>
                            <span>•</span>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $announcement->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                    @php
                    $priorityColors = [
                    'low' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                    'normal' => 'bg-green-500/20 text-green-400 border-green-500/30',
                    'high' => 'bg-red-500/20 text-red-400 border-red-500/30',
                    ];
                    @endphp
                    <span
                        class="px-3 py-1.5 text-sm font-semibold rounded-full border {{ $priorityColors[$announcement->priority] ?? '' }}">
                        {{ ucfirst($announcement->priority) }} Priority
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">
                <div class="prose prose-invert prose-slate max-w-none">
                    <div class="text-slate-300 text-lg leading-relaxed whitespace-pre-wrap">{{ $announcement->content }}
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-slate-700 bg-slate-800/30">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Posted {{ $announcement->created_at->diffForHumans() }}</span>
                    </div>
                    @if($announcement->recipient_type === 'all')
                    <div class="flex items-center gap-2 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Sent to All Staff</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection