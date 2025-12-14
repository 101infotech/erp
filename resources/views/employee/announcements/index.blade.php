@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Announcements</h1>
            <p class="text-slate-400">Stay updated with company news and important information</p>
        </div>

        <!-- Announcements List -->
        <div class="space-y-4">
            @forelse($announcements as $announcement)
            <div
                class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700 hover:border-slate-600 transition">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h2 class="text-xl font-bold text-white">{{ $announcement->title }}</h2>
                            @php
                            $priorityColors = [
                            'low' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                            'normal' => 'bg-green-500/20 text-green-400 border-green-500/30',
                            'high' => 'bg-red-500/20 text-red-400 border-red-500/30',
                            ];
                            @endphp
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full border {{ $priorityColors[$announcement->priority] ?? '' }}">
                                {{ ucfirst($announcement->priority) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-slate-400">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ $announcement->creator->name }}</span>
                            </div>
                            <span>•</span>
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $announcement->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-slate-300 mb-4 line-clamp-3">
                    {{ Str::limit($announcement->content, 200) }}
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-700">
                    <span class="text-sm text-slate-400">
                        {{ $announcement->created_at->format('M d, Y h:i A') }}
                    </span>
                    <a href="{{ route('employee.announcements.show', $announcement) }}"
                        class="px-4 py-2 bg-lime-500/20 text-lime-400 rounded-lg hover:bg-lime-500/30 text-sm font-medium transition inline-flex items-center gap-2">
                        Read More
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
            @empty
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-12 border border-slate-700 text-center">
                <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
                <p class="text-slate-400 text-lg font-medium">No announcements yet</p>
                <p class="text-slate-500 text-sm mt-1">Check back later for updates</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($announcements->hasPages())
        <div class="mt-6">
            {{ $announcements->links() }}
        </div>
        @endif
    </div>
</div>
@endsection