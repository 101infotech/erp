{{--
Table Component

A standardized table wrapper with consistent styling for data tables.

Props:
- responsive: boolean (default: true) - Horizontal scroll on mobile

Slots:
- header: Table header content
- default: Table body content

Usage:
<x-ui.table>
    <x-slot name="header">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Email</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Actions</th>
        </tr>
    </x-slot>

    <tr class="hover:bg-slate-700/30 transition-colors">
        <td class="px-6 py-4 text-sm text-white">John Doe</td>
        <td class="px-6 py-4 text-sm text-white">john@example.com</td>
        <td class="px-6 py-4">
            <x-ui.badge variant="success">Active</x-ui.badge>
        </td>
        <td class="px-6 py-4">
            <x-ui.button size="sm" variant="secondary">Edit</x-ui.button>
        </td>
    </tr>
</x-ui.table>
--}}

@props([
'responsive' => true,
])

<div {{ $attributes->merge(['class' => ($responsive ? 'overflow-x-auto' : '')]) }}>
    <table class="w-full">
        @if(isset($header))
        <thead class="bg-slate-800/50 border-b border-slate-700">
            {{ $header }}
        </thead>
        @endif

        <tbody class="divide-y divide-slate-700">
            {{ $slot }}
        </tbody>
    </table>
</div>