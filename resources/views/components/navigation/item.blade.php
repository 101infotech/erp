@props(['href', 'label', 'icon' => null, 'active' => false, 'badge' => null, 'indent' => false])

@php
$isActive = $active || request()->fullUrlIs($href) || request()->url() === $href;
$baseClasses = 'group flex items-center justify-between px-6 py-2.5 text-sm font-normal transition-all duration-150';
$activeClasses = 'bg-slate-800 text-white';
$inactiveClasses = 'text-slate-300 hover:bg-slate-800/60 hover:text-white';
@endphp

<a href="{{ $href }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
    <div class="flex items-center gap-3 min-w-0">
        @if($icon)
        <span class="flex-shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">
            {!! $icon !!}
        </span>
        @endif
        <span class="truncate">{{ $label }}</span>
    </div>

    @if($badge)
    <span
        class="ml-auto flex-shrink-0 inline-flex items-center justify-center min-w-[1.75rem] px-2 py-0.5 text-xs font-medium rounded-md bg-slate-700 text-slate-300">
        {{ $badge }}
    </span>
    @endif
</a>