{{--
Input Component

Text input with label, error states, icons, and help text.

Props:
- type: string (default: 'text') - Input type
- label: string (optional) - Input label
- error: string (optional) - Error message
- help: string (optional) - Help text
- required: boolean (default: false) - Required indicator
- disabled: boolean (default: false) - Disabled state
- icon: string (optional) - Icon slot name

Usage:
<x-ui.input name="email" type="email" label="Email Address" :required="true" placeholder="you@example.com" />

<x-ui.input name="search" placeholder="Search..." error="Please enter at least 3 characters" />
--}}

@props([
'type' => 'text',
'label' => null,
'error' => null,
'help' => null,
'required' => false,
'disabled' => false,
])

@php
$inputClasses = 'w-full px-4 py-2.5 bg-slate-800 border rounded-lg text-white placeholder-slate-500 transition-colors
focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900';

if ($error) {
$inputClasses .= ' border-danger-500 focus:border-danger-500 focus:ring-danger-500/50';
} else {
$inputClasses .= ' border-slate-700 hover:border-slate-600 focus:border-primary-500 focus:ring-primary-500/50';
}

if ($disabled) {
$inputClasses .= ' opacity-50 cursor-not-allowed';
}
@endphp

<div class="w-full">
    @if($label)
    <x-ui.label :for="$attributes->get('id') ?? $attributes->get('name')" :required="$required">
        {{ $label }}
    </x-ui.label>
    @endif

    <div class="relative">
        @if(isset($icon))
        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
            {{ $icon }}
        </div>
        @endif

        <input {{ $attributes->merge([
        'type' => $type,
        'class' => $inputClasses . (isset($icon) ? ' pl-10' : ''),
        'disabled' => $disabled,
        'required' => $required,
        ]) }}
        />
    </div>

    @if($error)
    <p class="mt-1.5 text-sm text-danger-400 flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd" />
        </svg>
        {{ $error }}
    </p>
    @elseif($help)
    <p class="mt-1.5 text-sm text-slate-500">{{ $help }}</p>
    @endif
</div>