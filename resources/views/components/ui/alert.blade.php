{{--
Alert Component

Notification and message boxes with different variants.

Props:
- variant: string (default: 'info') - Alert type (success, warning, danger, info)
- title: string (optional) - Alert title
- dismissible: boolean (default: false) - Show close button
- icon: boolean (default: true) - Show icon

Usage:
<x-ui.alert variant="success" title="Success!">
    Your changes have been saved successfully.
</x-ui.alert>

<x-ui.alert variant="danger" :dismissible="true">
    There was an error processing your request.
</x-ui.alert>
--}}

@props([
'variant' => 'info',
'title' => null,
'dismissible' => false,
'icon' => true,
])

@php
$variants = [
'success' => [
'bg' => 'bg-success-500/10',
'border' => 'border-success-500/20',
'text' => 'text-success-400',
'icon' => '
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
],
'warning' => [
'bg' => 'bg-warning-500/10',
'border' => 'border-warning-500/20',
'text' => 'text-warning-400',
'icon' => '
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
',
],
'danger' => [
'bg' => 'bg-danger-500/10',
'border' => 'border-danger-500/20',
'text' => 'text-danger-400',
'icon' => '
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
],
'info' => [
'bg' => 'bg-info-500/10',
'border' => 'border-info-500/20',
'text' => 'text-info-400',
'icon' => '
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
],
];

$config = $variants[$variant] ?? $variants['info'];
@endphp

<div {{ $attributes->merge(['class' => "relative rounded-lg border p-4 {$config['bg']} {$config['border']}"]) }}
    @if($dismissible)
    x-data="{ show: true }"
    x-show="show"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @endif
    >
    <div class="flex items-start gap-3">
        @if($icon)
        <div class="flex-shrink-0">
            <svg class="w-5 h-5 {{ $config['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $config['icon'] !!}
            </svg>
        </div>
        @endif

        <div class="flex-1 min-w-0">
            @if($title)
            <h4 class="text-sm font-semibold {{ $config['text'] }} mb-1">{{ $title }}</h4>
            @endif

            <div class="text-sm {{ $config['text'] }}">
                {{ $slot }}
            </div>
        </div>

        @if($dismissible)
        <button type="button" @click="show = false"
            class="flex-shrink-0 {{ $config['text'] }} hover:opacity-75 transition-opacity">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        @endif
    </div>
</div>