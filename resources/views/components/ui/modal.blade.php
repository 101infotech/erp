{{--
Modal Component

A modal dialog with backdrop overlay, close button, and customizable content.

Props:
- name: string (required) - Unique identifier for the modal
- title: string (optional) - Modal title
- maxWidth: string (default: '2xl') - Max width class (sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl, full)
- show: boolean (default: false) - Initial visibility state

Slots:
- title: Modal title content
- default: Modal body content
- footer: Modal footer content

Usage:
<x-ui.modal name="confirm-delete" title="Confirm Deletion" maxWidth="md">
    <p class="text-slate-400">Are you sure you want to delete this item?</p>

    <x-slot name="footer">
        <x-ui.button variant="secondary" @click="show = false">Cancel</x-ui.button>
        <x-ui.button variant="danger">Delete</x-ui.button>
    </x-slot>
</x-ui.modal>

To trigger the modal, use Alpine.js:
<x-ui.button @click="$dispatch('open-modal', 'confirm-delete')">Delete</x-ui.button>
--}}

@props([
'name',
'title' => null,
'maxWidth' => '2xl',
'show' => false,
])

@php
$maxWidthClasses = [
'sm' => 'sm:max-w-sm',
'md' => 'sm:max-w-md',
'lg' => 'sm:max-w-lg',
'xl' => 'sm:max-w-xl',
'2xl' => 'sm:max-w-2xl',
'3xl' => 'sm:max-w-3xl',
'4xl' => 'sm:max-w-4xl',
'5xl' => 'sm:max-w-5xl',
'6xl' => 'sm:max-w-6xl',
'7xl' => 'sm:max-w-7xl',
'full' => 'sm:max-w-full',
];

$maxWidthClass = $maxWidthClasses[$maxWidth] ?? $maxWidthClasses['2xl'];
@endphp

<div x-data="{ 
        show: @js($show),
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
    }" x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-hidden');
            {{ $show ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-hidden');
        }
    })" x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null" x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false" x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()" x-show="show"
    class="fixed inset-0 z-modal overflow-y-auto px-4 py-6 sm:px-0" style="display: {{ $show ? 'block' : 'none' }};">
    <!-- Backdrop -->
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/75 backdrop-blur-sm transform transition-all" @click="show = false"></div>

    <!-- Modal Content -->
    <div x-show="show" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="mb-6 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl overflow-hidden transform transition-all sm:w-full sm:mx-auto {{ $maxWidthClass }}">
        <!-- Header -->
        @if($title || isset($title))
        <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-xl font-bold text-white">
                @if(isset($title))
                {{ $title }}
                @else
                {{ $title }}
                @endif
            </h3>
            <button type="button" @click="show = false" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        @endif

        <!-- Body -->
        <div class="px-6 py-4">
            {{ $slot }}
        </div>

        <!-- Footer -->
        @if(isset($footer))
        <div class="px-6 py-4 border-t border-slate-700 flex items-center justify-end gap-3">
            {{ $footer }}
        </div>
        @endif
    </div>
</div>