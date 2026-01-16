{{--
Stat Card Component

A card specifically designed for displaying statistics and metrics.

Props:
- title: string (required) - Stat title/label
- value: string|number (required) - Stat value
- subtitle: string (optional) - Additional description
- icon: slot (optional) - Icon SVG
- iconColor: string (default: 'lime') - Icon background color
- trend: string (optional) - 'up', 'down', 'neutral' - Trend direction
- trendValue: string (optional) - Trend value (e.g., '+12%', '-5%')
- href: string (optional) - Make card clickable with link

Usage:
<x-ui.stat-card title="Total Users" value="1,234" subtitle="Active users" iconColor="lime">
    <x-slot name="icon">
        <svg class="w-5 h-5 text-lime-400">...</svg>
    </x-slot>
</x-ui.stat-card>

With trend:
<x-ui.stat-card title="Revenue" value="$12,450" subtitle="This month" trend="up" trendValue="+12.5%" iconColor="green">
    <x-slot name="icon">
        <svg class="w-5 h-5 text-green-400">...</svg>
    </x-slot>
</x-ui.stat-card>

Clickable:
<x-ui.stat-card title="Pending Tasks" value="45" href="{{ route('tasks.index') }}" iconColor="amber">
    <x-slot name="icon">
        <svg class="w-5 h-5 text-amber-400">...</svg>
    </x-slot>
</x-ui.stat-card>
--}}

@props([
'title' => '',
'value' => '',
'subtitle' => null,
'icon' => null,
'iconColor' => 'lime',
'trend' => null,
'trendValue' => null,
'href' => null,
])

@php
$colorClasses = [
'lime' => 'bg-lime-500/10 hover:border-lime-500/50',
'blue' => 'bg-blue-500/10 hover:border-blue-500/50',
'amber' => 'bg-amber-500/10 hover:border-amber-500/50',
'red' => 'bg-red-500/10 hover:border-red-500/50',
'green' => 'bg-green-500/10 hover:border-green-500/50',
'purple' => 'bg-purple-500/10 hover:border-purple-500/50',
'cyan' => 'bg-cyan-500/10 hover:border-cyan-500/50',
'orange' => 'bg-orange-500/10 hover:border-orange-500/50',
];

$iconBgClass = $colorClasses[$iconColor] ?? $colorClasses['lime'];
$hoverBorderClass = str_contains($iconBgClass, 'hover:border-') ? explode(' ', $iconBgClass)[1] :
'hover:border-lime-500/50';

$cardBaseClass = 'bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 transition-all duration-200';
$cardClass = $href
? $cardBaseClass . ' ' . $hoverBorderClass . ' cursor-pointer transform hover:-translate-y-1 hover:shadow-lg'
: $cardBaseClass;
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $cardClass]) }}>
    @else
    <div {{ $attributes->merge(['class' => $cardClass]) }}>
        @endif
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-slate-400 text-xs mb-1.5">{{ $title }}</p>
                <h2 class="text-2xl font-bold text-white mb-1">{{ $value }}</h2>
                @if($subtitle)
                <p class="text-xs text-slate-500">{{ $subtitle }}</p>
                @endif

                @if($trend && $trendValue)
                <div class="mt-2 flex items-center gap-1">
                    @if($trend === 'up')
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <span class="text-xs font-semibold text-green-400">{{ $trendValue }}</span>
                    @elseif($trend === 'down')
                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                    <span class="text-xs font-semibold text-red-400">{{ $trendValue }}</span>
                    @else
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                    </svg>
                    <span class="text-xs font-semibold text-slate-400">{{ $trendValue }}</span>
                    @endif
                </div>
                @endif
            </div>

            @if($icon)
            <div
                class="w-10 h-10 {{ explode(' ', $iconBgClass)[0] }} rounded-xl flex items-center justify-center flex-shrink-0">
                {!! $icon !!}
            </div>
            @endif
        </div>
        @if($href)
</a>
@else
</div>
@endif