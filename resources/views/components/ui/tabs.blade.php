{{--
Tabs Component

Tabbed content interface with Alpine.js state management.

Props:
- tabs: array (required) - Tab items [['id' => 'tab1', 'label' => 'Tab 1', 'content' => '...'], ...]
- defaultTab: string (optional) - Default active tab ID
- variant: string (default: 'underline') - Tab style (underline, pills, buttons)

Usage:
<x-ui.tabs :tabs="[
            ['id' => 'profile', 'label' => 'Profile', 'content' => 'Profile content...'],
            ['id' => 'settings', 'label' => 'Settings', 'content' => 'Settings content...'],
        ]" defaultTab="profile" />

Or use slots for custom content:
<x-ui.tabs variant="pills">
    <x-slot name="tabs">
        [['id' => 'tab1', 'label' => 'Tab 1'], ['id' => 'tab2', 'label' => 'Tab 2']]
    </x-slot>

    <x-slot name="tab1">Custom content for tab 1</x-slot>
    <x-slot name="tab2">Custom content for tab 2</x-slot>
</x-ui.tabs>
--}}

@props([
'tabs' => [],
'defaultTab' => null,
'variant' => 'underline',
])

@php
$defaultTabId = $defaultTab ?? ($tabs[0]['id'] ?? null);

$variantClasses = [
'underline' => [
'container' => 'border-b border-slate-700',
'tab' => 'px-4 py-3 border-b-2 border-transparent text-slate-400 hover:text-white hover:border-slate-600
transition-colors',
'active' => 'border-primary-500 text-white font-medium',
],
'pills' => [
'container' => 'bg-slate-800 rounded-lg p-1',
'tab' => 'px-4 py-2 rounded-md text-slate-400 hover:text-white transition-colors',
'active' => 'bg-primary-500 text-white font-medium shadow-sm',
],
'buttons' => [
'container' => 'space-x-2',
'tab' => 'px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-400 hover:text-white
hover:border-slate-600 transition-colors',
'active' => 'bg-primary-500 border-primary-500 text-white font-medium',
],
];

$classes = $variantClasses[$variant] ?? $variantClasses['underline'];
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }} x-data="{ activeTab: '{{ $defaultTabId }}' }">
    <!-- Tab Headers -->
    <div class="{{ $classes['container'] }} flex items-center">
        @foreach($tabs as $tab)
        <button type="button" @click="activeTab = '{{ $tab['id'] }}'"
            :class="{ '{{ $classes['active'] }}': activeTab === '{{ $tab['id'] }}' }" class="{{ $classes['tab'] }}">
            @if(isset($tab['icon']))
            <span class="mr-2">{!! $tab['icon'] !!}</span>
            @endif
            {{ $tab['label'] }}
            @if(isset($tab['badge']))
            <x-ui.badge variant="primary" size="sm" class="ml-2">{{ $tab['badge'] }}</x-ui.badge>
            @endif
        </button>
        @endforeach
    </div>

    <!-- Tab Content -->
    <div class="mt-4">
        @foreach($tabs as $tab)
        <div x-show="activeTab === '{{ $tab['id'] }}'" x-cloak>
            @if(isset($tab['content']))
            {!! $tab['content'] !!}
            @elseif(isset(${$tab['id']}))
            {{ ${$tab['id']} }}
            @endif
        </div>
        @endforeach

        @if($slot->isNotEmpty())
        {{ $slot }}
        @endif
    </div>
</div>