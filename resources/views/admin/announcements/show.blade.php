@extends('admin.layouts.app')

@section('title', 'View Announcement')
@section('page-title', 'View Announcement')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('admin.announcements.index') }}"
                class="px-3 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition">
                ← Back
            </a>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-white">Announcement Details</h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.announcements.edit', $announcement) }}"
                    class="px-4 py-2 bg-amber-500/20 text-amber-400 rounded-lg hover:bg-amber-500/30 font-medium transition">
                    Edit
                </a>
                <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this announcement?')"
                        class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 font-medium transition">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Announcement Card -->
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
        <!-- Header -->
        <div class="flex items-start justify-between mb-6 pb-6 border-b border-slate-700">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-white mb-2">{{ $announcement->title }}</h1>
                <div class="flex items-center gap-4 text-sm text-slate-400">
                    <span>By {{ $announcement->creator->name }}</span>
                    <span>•</span>
                    <span>{{ $announcement->created_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
            <div class="flex gap-2">
                @php
                $priorityColors = [
                'low' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                'normal' => 'bg-green-500/20 text-green-400 border-green-500/30',
                'high' => 'bg-red-500/20 text-red-400 border-red-500/30',
                ];
                @endphp
                <span
                    class="px-3 py-1 text-xs font-semibold rounded-full border {{ $priorityColors[$announcement->priority] ?? '' }}">
                    {{ ucfirst($announcement->priority) }} Priority
                </span>
                @if($announcement->is_published)
                <span
                    class="px-3 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-400 border border-green-500/30">
                    Published
                </span>
                @else
                <span
                    class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-500/20 text-slate-400 border border-slate-500/30">
                    Draft
                </span>
                @endif
            </div>
        </div>

        <!-- Content -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-white mb-3">Content</h3>
            <div class="text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $announcement->content }}</div>
        </div>

        <!-- Recipients Info -->
        <div class="mb-6 p-4 bg-slate-700/30 rounded-lg">
            <h3 class="text-lg font-semibold text-white mb-3">Recipients</h3>
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-slate-300">{{ $announcement->getRecipientsList() }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="text-slate-300">
                        @if($announcement->send_email)
                        <span class="text-green-400">Email notifications sent</span>
                        @else
                        <span class="text-slate-500">Email notifications disabled</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Metadata -->
        <div class="pt-4 border-t border-slate-700">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-slate-400">Created:</span>
                    <span class="text-white ml-2">{{ $announcement->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div>
                    <span class="text-slate-400">Last Updated:</span>
                    <span class="text-white ml-2">{{ $announcement->updated_at->format('M d, Y h:i A') }}</span>
                </div>
                @if($announcement->published_at)
                <div>
                    <span class="text-slate-400">Published:</span>
                    <span class="text-white ml-2">{{ $announcement->published_at->format('M d, Y h:i A') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection