{{--
Pagination Component

Page navigation with previous/next and numbered links.

Props:
- currentPage: int (required) - Current page number
- totalPages: int (required) - Total number of pages
- baseUrl: string (required) - Base URL for pagination links
- showNumbers: boolean (default: true) - Show page numbers
- showEnds: boolean (default: true) - Show first/last buttons

Usage:
<x-ui.pagination :currentPage="3" :totalPages="10" baseUrl="/users" />
--}}

@props([
'currentPage' => 1,
'totalPages' => 1,
'baseUrl' => '',
'showNumbers' => true,
'showEnds' => true,
])

@php
$hasPrevious = $currentPage > 1;
$hasNext = $currentPage < $totalPages; // Calculate which page numbers to show $range=2; // Show 2 pages on each side of
    current page $start=max(1, $currentPage - $range); $end=min($totalPages, $currentPage + $range);
    $pages=range($start, $end); @endphp <nav {{ $attributes->merge(['class' => 'flex items-center justify-between
    border-t border-slate-700 pt-4']) }}>
    <!-- Mobile: Previous/Next only -->
    <div class="flex flex-1 justify-between sm:hidden">
        @if($hasPrevious)
        <a href="{{ $baseUrl }}?page={{ $currentPage - 1 }}"
            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 transition-colors">
            Previous
        </a>
        @else
        <span
            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-500 bg-slate-800 border border-slate-700 rounded-lg cursor-not-allowed opacity-50">
            Previous
        </span>
        @endif

        @if($hasNext)
        <a href="{{ $baseUrl }}?page={{ $currentPage + 1 }}"
            class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 transition-colors">
            Next
        </a>
        @else
        <span
            class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-slate-500 bg-slate-800 border border-slate-700 rounded-lg cursor-not-allowed opacity-50">
            Next
        </span>
        @endif
    </div>

    <!-- Desktop: Full pagination -->
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-slate-400">
                Page <span class="font-medium text-white">{{ $currentPage }}</span> of <span
                    class="font-medium text-white">{{ $totalPages }}</span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- First Page -->
            @if($showEnds && $currentPage > 3)
            <a href="{{ $baseUrl }}?page=1"
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </a>
            @endif

            <!-- Previous -->
            @if($hasPrevious)
            <a href="{{ $baseUrl }}?page={{ $currentPage - 1 }}"
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            @else
            <span
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-500 bg-slate-800 border border-slate-700 rounded-lg cursor-not-allowed opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
            @endif

            <!-- Page Numbers -->
            @if($showNumbers)
            @if($start > 1)
            <span class="px-3 py-2 text-sm text-slate-500">...</span>
            @endif

            @foreach($pages as $page)
            @if($page == $currentPage)
            <span
                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-primary-500 border border-primary-500 rounded-lg">
                {{ $page }}
            </span>
            @else
            <a href="{{ $baseUrl }}?page={{ $page }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 transition-colors">
                {{ $page }}
            </a>
            @endif
            @endforeach

            @if($end < $totalPages) <span class="px-3 py-2 text-sm text-slate-500">...</span>
                @endif
                @endif

                <!-- Next -->
                @if($hasNext)
                <a href="{{ $baseUrl }}?page={{ $currentPage + 1 }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                @else
                <span
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-500 bg-slate-800 border border-slate-700 rounded-lg cursor-not-allowed opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
                @endif

                <!-- Last Page -->
                @if($showEnds && $currentPage < $totalPages - 2) <a href="{{ $baseUrl }}?page={{ $totalPages }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                    </a>
                    @endif
        </div>
    </div>
    </nav>