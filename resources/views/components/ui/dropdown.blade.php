{{--
Dropdown Component

Context menus and dropdown selectors with Alpine.js.

Props:
- align: string (default: 'left') - Alignment (left, right)
- width: string (default: '48') - Width in Tailwind units (48, 56, 64)

Slots:
- trigger: Dropdown trigger button
- default: Dropdown content

Usage:
<x-ui.dropdown>
    <x-slot name="trigger">
        <x-ui.button variant="secondary">Options</x-ui.button>
    </x-slot>

    <a href="/profile" class="block px-4 py-2 text-sm text-white hover:bg-slate-700">Profile</a>
    <a href="/settings" class="block px-4 py-2 text-sm text-white hover:bg-slate-700">Settings</a>
    <hr class="my-2 border-slate-700">
    <a href="/logout" class="block px-4 py-2 text-sm text-danger-400 hover:bg-slate-700">Logout</a>
</x-ui.dropdown>
--}}

@props([
'align' => 'left',
'width' => '48',
])

@php
$alignmentClasses = [
'left' => 'left-0 origin-top-left',
'right' => 'right-0 origin-top-right',
];

$widthClasses = [
'48' => 'w-48',
'56' => 'w-56',
'64' => 'w-64',
];

$alignClass = $alignmentClasses[$align] ?? $alignmentClasses['left'];
$widthClass = $widthClasses[$width] ?? $widthClasses['48'];
@endphp

<div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
    <!-- Trigger -->
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <!-- Dropdown Content -->
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-dropdown mt-2 {{ $widthClass }} {{ $alignClass }} bg-slate-800 border border-slate-700 rounded-lg shadow-lg py-1"
        style="display: none;" @click="open = false">
        {{ $slot }}
    </div>
</div>