{{--
Button Component

A unified button component with multiple variants, sizes, and states.

Props:
- variant: 'primary', 'secondary', 'danger', 'success', 'outline' (default: 'primary')
- size: 'sm', 'md', 'lg' (default: 'md')
- type: 'button', 'submit', 'reset' (default: 'button')
- disabled: boolean (default: false)
- loading: boolean (default: false)
- href: string (optional, renders as link)
- icon: slot (optional)
- iconPosition: 'left', 'right' (default: 'left')

Usage:
<x-ui.button variant="primary">Save</x-ui.button>
<x-ui.button variant="secondary" size="lg">Cancel</x-ui.button>
<x-ui.button variant="danger" :loading="true">Deleting...</x-ui.button>

With icon:
<x-ui.button variant="primary">
    <x-slot name="icon">
        <svg>...</svg>
    </x-slot>
    Save Changes
</x-ui.button>
--}}

@props([
'variant' => 'primary',
'size' => 'md',
'type' => 'button',
'disabled' => false,
'loading' => false,
'href' => null,
'icon' => null,
'iconPosition' => 'left',
])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 font-semibold transition-all duration-200
disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2
focus:ring-offset-slate-900';

$variantClasses = [
'primary' => 'bg-primary hover:bg-primary-dark text-slate-950 focus:ring-primary',
'secondary' => 'bg-slate-700 hover:bg-slate-600 text-white focus:ring-slate-500',
'danger' => 'bg-danger hover:bg-danger-dark text-white focus:ring-danger',
'success' => 'bg-success hover:bg-success-dark text-white focus:ring-success',
'outline' => 'border-2 border-slate-700 hover:border-primary hover:text-primary text-white bg-transparent
focus:ring-primary',
'ghost' => 'text-slate-400 hover:text-white hover:bg-slate-800 focus:ring-slate-500',
];

$sizeClasses = [
'sm' => 'px-3 py-1.5 text-sm rounded-lg',
'md' => 'px-4 py-2 text-base rounded-lg',
'lg' => 'px-6 py-3 text-lg rounded-xl',
];

$classes = trim("{$baseClasses} {$variantClasses[$variant]} {$sizeClasses[$size]}");
$isDisabled = $disabled || $loading;
@endphp

@if($href && !$isDisabled)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}
    >
    @if($loading)
    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
        </path>
    </svg>
    @elseif($icon && $iconPosition === 'left')
    {!! $icon !!}
    @endif

    {{ $slot }}

    @if($icon && $iconPosition === 'right' && !$loading)
    {!! $icon !!}
    @endif
</a>
@else
<button type="{{ $type }}" @disabled($isDisabled) {{ $attributes->merge(['class' => $classes]) }}
    >
    @if($loading)
    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
        </path>
    </svg>
    @elseif($icon && $iconPosition === 'left')
    {!! $icon !!}
    @endif

    {{ $slot }}

    @if($icon && $iconPosition === 'right' && !$loading)
    {!! $icon !!}
    @endif
</button>
@endif