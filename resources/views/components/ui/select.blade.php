{{--
Select Component

Dropdown selection with label, error states, and placeholder support.

Props:
- label: string (optional) - Select label
- error: string (optional) - Error message
- help: string (optional) - Help text
- required: boolean (default: false) - Required indicator
- disabled: boolean (default: false) - Disabled state
- placeholder: string (optional) - Placeholder option text

Usage:
<x-ui.select name="status" label="Status" :required="true" placeholder="Select status">
    <option value="active">Active</option>
    <option value="inactive">Inactive</option>
    <option value="pending">Pending</option>
</x-ui.select>

<x-ui.select name="country" label="Country" error="Please select a country">
    <option value="us">United States</option>
    <option value="uk">United Kingdom</option>
</x-ui.select>
--}}

@props([
'label' => null,
'error' => null,
'help' => null,
'required' => false,
'disabled' => false,
'placeholder' => null,
])

@php
$selectClasses = 'w-full px-4 py-2.5 bg-slate-800 border rounded-lg text-white transition-colors focus:outline-none
focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 cursor-pointer';

if ($error) {
$selectClasses .= ' border-danger-500 focus:border-danger-500 focus:ring-danger-500/50';
} else {
$selectClasses .= ' border-slate-700 hover:border-slate-600 focus:border-primary-500 focus:ring-primary-500/50';
}

if ($disabled) {
$selectClasses .= ' opacity-50 cursor-not-allowed';
}

$uniqueId = $attributes->get('id') ?? $attributes->get('name') ?? 'select-' . uniqid();
@endphp

<div class="w-full">
    @if($label)
    <x-ui.label :for="$uniqueId" :required="$required">
        {{ $label }}
    </x-ui.label>
    @endif

    <div class="relative">
        <select {{ $attributes->merge([
            'id' => $uniqueId,
            'class' => $selectClasses,
            'disabled' => $disabled,
            'required' => $required,
            ]) }}
            >
            @if($placeholder)
            <option value="" disabled selected class="text-slate-500">{{ $placeholder }}</option>
            @endif

            {{ $slot }}
        </select>

        <!-- Dropdown arrow icon -->
        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
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