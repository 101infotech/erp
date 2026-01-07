{{-- Dashboard Section Header Component --}}
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
    <div>
        <h2 class="text-lg font-semibold text-white">{{ $title }}</h2>
        @if(isset($subtitle))
        <p class="text-xs text-slate-400 mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($action) && isset($actionLabel))
    <a href="{{ $action }}" class="text-lime-400 hover:text-lime-300 inline-flex items-center gap-2 text-sm font-medium">
        {{ $actionLabel }}
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </a>
    @endif
</div>

@props(['title' => '', 'subtitle' => null, 'action' => null, 'actionLabel' => null])
