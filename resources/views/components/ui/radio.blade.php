{{--
Radio Component

A styled radio button with label and description support.

Props:
- label: string (optional) - Radio label text
- description: string (optional) - Helper text below label
- value: string (required) - Radio value
- checked: boolean (default: false) - Checked state
- disabled: boolean (default: false) - Disabled state
- error: string (optional) - Error message

Usage:
<div class="space-y-3">
    <x-ui.radio name="plan" value="free" label="Free Plan" description="Basic features" :checked="true" />
    <x-ui.radio name="plan" value="pro" label="Pro Plan" description="Advanced features" />
    <x-ui.radio name="plan" value="enterprise" label="Enterprise" description="All features + support" />
</div>
--}}

@props([
'label' => null,
'description' => null,
'value' => null,
'checked' => false,
'disabled' => false,
'error' => null,
])

@php
$uniqueId = $attributes->get('id') ?? ($attributes->get('name') . '-' . $value) ?? 'radio-' . uniqid();

$radioClasses = 'w-5 h-5 rounded-full border-2 bg-slate-800 transition-all cursor-pointer';
if ($error) {
$radioClasses .= ' border-danger-500 text-danger-500 focus:ring-danger-500/50';
} else {
$radioClasses .= ' border-slate-600 text-primary-500 focus:ring-primary-500/50';
}

if ($disabled) {
$radioClasses .= ' opacity-50 cursor-not-allowed';
}

$radioClasses .= ' focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900';
@endphp

<div class="flex items-start gap-3">
    <div class="flex items-center h-6">
        <input {{ $attributes->merge([
        'type' => 'radio',
        'id' => $uniqueId,
        'value' => $value,
        'class' => $radioClasses,
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