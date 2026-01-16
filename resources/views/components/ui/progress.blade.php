{{--
Progress Bar Component

Visual progress indicators with percentage display.

Props:
- value: number (required) - Progress value (0-100)
- max: number (default: 100) - Maximum value
- variant: string (default: 'primary') - Color variant (primary, success, warning, danger, info)
- size: string (default: 'md') - Size (sm, md, lg)
- showLabel: boolean (default: false) - Show percentage label
- label: string (optional) - Custom label text
- animated: boolean (default: false) - Animated stripes

Usage:
<x-ui.progress :value="75" />
<x-ui.progress :value="60" variant="success" :showLabel="true" />
<x-ui.progress :value="45" size="lg" :animated="true" label="Uploading..." />
--}}

@props([
'value' => 0,
'max' => 100,
'variant' => 'primary',
'size' => 'md',
'showLabel' => false,
'label' => null,
'animated' => false,
])

@php
$percentage = $max > 0 ? min(100, ($value / $max) * 100) : 0;

$variants = [
'primary' => 'bg-primary-500',
'success' => 'bg-success-500',
'warning' => 'bg-warning-500',
'danger' => 'bg-danger-500',
'info' => 'bg-info-500',
];

$sizes = [
'sm' => 'h-1',
'md' => 'h-2',
'lg' => 'h-3',
];

$variantClass = $variants[$variant] ?? $variants['primary'];
$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if($label || $showLabel)
    <div class="flex items-center justify-between mb-2">
        @if($label)
        <span class="text-sm font-medium text-white">{{ $label }}</span>
        @endif
        @if($showLabel)
        <span class="text-sm font-medium text-slate-400">{{ round($percentage) }}%</span>
        @endif
    </div>
    @endif

    <div class="w-full bg-slate-700 rounded-full overflow-hidden {{ $sizeClass }}">
        <div class="{{ $variantClass }} {{ $sizeClass }} rounded-full transition-all duration-300 {{ $animated ? 'progress-bar-animated' : '' }}"
            style="width: {{ $percentage }}%" role="progressbar" aria-valuenow="{{ $value }}" aria-valuemin="0"
            aria-valuemax="{{ $max }}"></div>
    </div>
</div>

@if($animated)
@once
@push('styles')
<style>
    .progress-bar-animated {
        background-image: linear-gradient(45deg,
                rgba(255, 255, 255, 0.15) 25%,
                transparent 25%,
                transparent 50%,
                rgba(255, 255, 255, 0.15) 50%,
                rgba(255, 255, 255, 0.15) 75%,
                transparent 75%,
                transparent);
        background-size: 1rem 1rem;
        animation: progress-bar-stripes 1s linear infinite;
    }

    @keyframes progress-bar-stripes {
        0% {
            background-position: 1rem 0;
        }

        100% {
            background-position: 0 0;
        }
    }
</style>
@endpush
@endonce
@endif