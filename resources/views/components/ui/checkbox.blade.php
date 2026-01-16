{{--
Checkbox Component

A styled checkbox input with label and description support.

Props:
- label: string (optional) - Checkbox label text
- description: string (optional) - Helper text below label
- checked: boolean (default: false) - Checked state
- disabled: boolean (default: false) - Disabled state
- error: string (optional) - Error message

Usage:
<x-ui.checkbox name="terms" label="I agree to the terms and conditions" :required="true" />

<x-ui.checkbox name="notifications" label="Email notifications"
    description="Receive email updates about your account activity" :checked="true" />
--}}

@props([
'label' => null,
'description' => null,
'checked' => false,
'disabled' => false,
'error' => null,
])

@php
$uniqueId = $attributes->get('id') ?? $attributes->get('name') ?? 'checkbox-' . uniqid();

$checkboxClasses = 'w-5 h-5 rounded border-2 bg-slate-800 transition-all cursor-pointer';
if ($error) {
$checkboxClasses .= ' border-danger-500 text-danger-500 focus:ring-danger-500/50';
} else {
$checkboxClasses .= ' border-slate-600 text-primary-500 focus:ring-primary-500/50';
}

if ($disabled) {
$checkboxClasses .= ' opacity-50 cursor-not-allowed';
}

$checkboxClasses .= ' focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900';
@endphp

<div class="flex items-start gap-3">
    <div class="flex items-center h-6">
        <input {{ $attributes->merge([
        'type' => 'checkbox',
        'id' => $uniqueId,
        'class' => $checkboxClasses,
        'checked' => $checked,
        'disabled' => $disabled,
        ]) }}
        />
    </div>

    @if($label || $description || $slot->isNotEmpty())
    <div class="flex-1">
        @if($label || $slot->isNotEmpty())
        <label for="{{ $uniqueId }}" class="block text-sm font-medium text-white cursor-pointer">
            {{ $label ?? $slot }}
        </label>
        @endif

        @if($description)
        <p class="mt-1 text-sm text-slate-400">{{ $description }}</p>
        @endif

        @if($error)
        <p class="mt-1 text-sm text-danger-400 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" />
            </svg>
            {{ $error }}
        </p>
        @endif
    </div>
    @endif
</div>