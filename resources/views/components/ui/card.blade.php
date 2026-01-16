{{--
Card Component

A standardized card container with optional title, subtitle, icon, and action link.

Props:
- title: string (optional) - Card title
- subtitle: string (optional) - Card subtitle
- icon: slot (optional) - Icon SVG
- iconColor: string (default: 'lime') - Icon background color (lime, blue, amber, red, green)
- action: string (optional) - Action link URL
- actionLabel: string (optional) - Action link text
- padding: boolean (default: true) - Apply padding to content area
- noBorder: boolean (default: false) - Remove border

Usage:
<x-ui.card>
    Simple card content
</x-ui.card>

<x-ui.card title="Recent Activity" subtitle="Last 7 days">
    Card with title and subtitle
</x-ui.card>

<x-ui.card title="User Stats" subtitle="Overview" action="{{ route('users.index') }}" actionLabel="View All"
    iconColor="lime">
    <x-slot name="icon">
        <svg class="w-5 h-5 text-lime-400">...</svg>
    </x-slot>
    Content here
</x-ui.card>
--}}

@props([
'title' => null,
'subtitle' => null,
'icon' => null,
'iconColor' => 'lime',
'action' => null,
'actionLabel' => null,
'padding' => true,
'noBorder' => false,
])

@php
$colorClasses = [
'lime' => 'bg-lime-500/20',
'blue' => 'bg-blue-500/20',
'amber' => 'bg-amber-500/20',
'red' => 'bg-red-500/20',
'green' => 'bg-green-500/20',
'purple' => 'bg-purple-500/20',
'cyan' => 'bg-cyan-500/20',
'pink' => 'bg-pink-500/20',
];

$iconBgClass = $colorClasses[$iconColor] ?? $colorClasses['lime'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-slate-800/50 backdrop-blur-sm rounded-xl' . ($noBorder ? '' : ' border
    border-slate-700')]) }}>
    @if($title)
    <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
        <div class="flex items-center gap-3">
            @if($icon)
            <div class="w-10 h-10 rounded-lg {{ $iconBgClass }} flex items-center justify-center">
                {!! $icon !!}
            </div>
            @endif
            <div>
                <h3 class="text-lg font-bold text-white">{{ $title }}</h3>
                @if($subtitle)
                <p class="text-xs text-slate-400">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        @if($action && $actionLabel)
        <a href="{{ $action }}"
            class="text-lime-400 hover:text-lime-300 text-sm font-medium flex items-center gap-1 transition-colors">
            {{ $actionLabel }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
        @endif
    </div>
    @endif

    <div class="{{ $padding ? 'px-6 py-4' : '' }}">
        {{ $slot }}
    </div>
</div>