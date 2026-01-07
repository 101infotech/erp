{{-- Dashboard Card Container Component --}}
<div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700 {{ $attributes->get('class') }}">
    @if(isset($title))
    <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
        <div class="flex items-center">
            @if(isset($icon))
            <div class="w-8 h-8 rounded-lg bg-{{ $color ?? 'slate' }}-500/20 flex items-center justify-center mr-3">
                {!! $icon !!}
            </div>
            @endif
            <div>
                <h3 class="text-lg font-bold text-white">{{ $title }}</h3>
                @if(isset($subtitle))
                <p class="text-xs text-slate-400">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        @if(isset($action) && isset($actionLabel))
        <a href="{{ $action }}" class="text-lime-400 hover:text-lime-300 text-sm font-medium flex items-center gap-1">
            {{ $actionLabel }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
        @endif
    </div>
    @endif

    <div class="px-6 py-4">
        {{ $slot }}
    </div>
</div>

@props(['title' => null, 'subtitle' => null, 'icon' => null, 'color' => 'slate', 'action' => null, 'actionLabel' =>
null])