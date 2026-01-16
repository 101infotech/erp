{{--
Loading Component

Loading spinners and skeleton screens for async operations.

Props:
- type: string (default: 'spinner') - Loading type (spinner, dots, pulse, skeleton)
- size: string (default: 'md') - Size (sm, md, lg)
- text: string (optional) - Loading text
- center: boolean (default: false) - Center the loader

Usage:
<x-ui.loading />
<x-ui.loading type="dots" size="lg" />
<x-ui.loading type="pulse" text="Loading data..." :center="true" />
<x-ui.loading type="skeleton" />
--}}

@props([
'type' => 'spinner',
'size' => 'md',
'text' => null,
'center' => false,
])

@php
$sizes = [
'sm' => 'w-4 h-4',
'md' => 'w-8 h-8',
'lg' => 'w-12 h-12',
];

$sizeClass = $sizes[$size] ?? $sizes['md'];

$containerClass = $center ? 'flex flex-col items-center justify-center min-h-[200px]' : 'inline-flex items-center
gap-3';
@endphp

<div {{ $attributes->merge(['class' => $containerClass]) }}>
    @if($type === 'spinner')
    <svg class="{{ $sizeClass }} animate-spin text-primary-500" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
        </path>
    </svg>

    @elseif($type === 'dots')
    <div class="flex items-center gap-2">
        <div class="w-3 h-3 bg-primary-500 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
        <div class="w-3 h-3 bg-primary-500 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
        <div class="w-3 h-3 bg-primary-500 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
    </div>

    @elseif($type === 'pulse')
    <div class="relative {{ $sizeClass }}">
        <div class="absolute inset-0 bg-primary-500 rounded-full animate-ping opacity-75"></div>
        <div class="relative bg-primary-500 rounded-full {{ $sizeClass }}"></div>
    </div>

    @elseif($type === 'skeleton')
    <div class="animate-pulse space-y-4 w-full">
        <div class="h-4 bg-slate-700 rounded w-3/4"></div>
        <div class="space-y-2">
            <div class="h-4 bg-slate-700 rounded"></div>
            <div class="h-4 bg-slate-700 rounded w-5/6"></div>
        </div>
        <div class="h-32 bg-slate-700 rounded"></div>
        <div class="grid grid-cols-3 gap-4">
            <div class="h-20 bg-slate-700 rounded"></div>
            <div class="h-20 bg-slate-700 rounded"></div>
            <div class="h-20 bg-slate-700 rounded"></div>
        </div>
    </div>
    @endif

    @if($text)
    <span class="text-sm text-slate-400 {{ $center ? 'mt-3' : '' }}">{{ $text }}</span>
    @endif
</div>