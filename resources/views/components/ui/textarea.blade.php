{{--
Textarea Component

Multi-line text input with label, error states, and character counter.

Props:
- label: string (optional) - Textarea label
- error: string (optional) - Error message
- help: string (optional) - Help text
- required: boolean (default: false) - Required indicator
- disabled: boolean (default: false) - Disabled state
- rows: number (default: 4) - Number of visible rows
- maxlength: number (optional) - Maximum character length
- showCount: boolean (default: false) - Show character counter

Usage:
<x-ui.textarea name="description" label="Description" :required="true" rows="6"
    placeholder="Enter a detailed description..." />

<x-ui.textarea name="notes" label="Notes" :maxlength="500" :showCount="true" />
--}}

@props([
'label' => null,
'error' => null,
'help' => null,
'required' => false,
'disabled' => false,
'rows' => 4,
'maxlength' => null,
'showCount' => false,
])

@php
$textareaClasses = 'w-full px-4 py-2.5 bg-slate-800 border rounded-lg text-white placeholder-slate-500 transition-colors
focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 resize-y';

if ($error) {
$textareaClasses .= ' border-danger-500 focus:border-danger-500 focus:ring-danger-500/50';
} else {
$textareaClasses .= ' border-slate-700 hover:border-slate-600 focus:border-primary-500 focus:ring-primary-500/50';
}

if ($disabled) {
$textareaClasses .= ' opacity-50 cursor-not-allowed';
}

$uniqueId = $attributes->get('id') ?? $attributes->get('name') ?? 'textarea-' . uniqid();
@endphp

<div class="w-full">
    @if($label)
    <x-ui.label :for="$uniqueId" :required="$required">
        {{ $label }}
    </x-ui.label>
    @endif

    <textarea {{ $attributes->merge([
            'id' => $uniqueId,
            'rows' => $rows,
            'class' => $textareaClasses,
            'disabled' => $disabled,
            'required' => $required,
            'maxlength' => $maxlength,
        ]) }}
        @if($showCount && $maxlength)
            x-data="{ count: $el.value.length }"
            x-on:input="count = $el.value.length"
        @endif
    ></textarea>

    @if($showCount && $maxlength)
    <div class="mt-1.5 flex items-center justify-between">
        <div>
            @if($error)
            <p class="text-sm text-danger-400 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                {{ $error }}
            </p>
            @elseif($help)
            <p class="text-sm text-slate-500">{{ $help }}</p>
            @endif
        </div>
        <p class="text-xs text-slate-500" x-text="`${count} / {{ $maxlength }}`"></p>
    </div>
    @else
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
    @endif
</div>