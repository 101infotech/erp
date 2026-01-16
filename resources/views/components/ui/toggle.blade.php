{{--
Toggle Component

A switch/toggle button with label and description support.

Props:
- label: string (optional) - Toggle label text
- description: string (optional) - Helper text below label
- checked: boolean (default: false) - Checked state
- disabled: boolean (default: false) - Disabled state
- size: string (default: 'md') - Size (sm, md, lg)

Usage:
<x-ui.toggle name="dark_mode" label="Dark Mode" description="Enable dark theme across the application"
    :checked="true" />

<x-ui.toggle name="notifications" label="Push Notifications" size="sm" />
--}}

@props([
'label' => null,
'description' => null,
'checked' => false,
'disabled' => false,
'size' => 'md',
])

@php
$uniqueId = $attributes->get('id') ?? $attributes->get('name') ?? 'toggle-' . uniqid();

$sizes = [
'sm' => [
'track' => 'w-9 h-5',
'circle' => 'w-4 h-4',
'translate' => 'translate-x-4',
],
'md' => [
'track' => 'w-11 h-6',
'circle' => 'w-5 h-5',
'translate' => 'translate-x-5',
],
'lg' => [
'track' => 'w-14 h-7',
'circle' => 'w-6 h-6',
'translate' => 'translate-x-7',
],
];

$sizeConfig = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="flex items-start justify-between gap-4">
    <div class="flex-1">
        @if($label || $slot->isNotEmpty())
        <label for="{{ $uniqueId }}" class="block text-sm font-medium text-white cursor-pointer">
            {{ $label ?? $slot }}
        </label>
        @endif

        @if($description)
        <p class="mt-1 text-sm text-slate-400">{{ $description }}</p>
        @endif
    </div>

    <button type="button" role="switch" aria-checked="{{ $checked ? 'true' : 'false' }}" @if(!$disabled)
        x-data="{ enabled: {{ $checked ? 'true' : 'false' }} }"
        @click="enabled = !enabled; $refs.input.checked = enabled; $refs.input.dispatchEvent(new Event('change'))"
        :aria-checked="enabled.toString()" @endif
        class="relative inline-flex items-center {{ $sizeConfig['track'] }} rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}"
        :class="{ 'bg-primary-500': enabled, 'bg-slate-700': !enabled }" {{ $disabled ? 'disabled' : '' }}>
        <span
            class="{{ $sizeConfig['circle'] }} bg-white rounded-full shadow-lg transform transition-transform duration-200"
            :class="{ '{{ $sizeConfig['translate'] }}': enabled, 'translate-x-0.5': !enabled }"></span>
    </button>

    <input {{ $attributes->merge([
    'type' => 'checkbox',
    'id' => $uniqueId,
    'class' => 'sr-only',
    'checked' => $checked,
    'disabled' => $disabled,
    ]) }}
    x-ref="input"
    />
</div>