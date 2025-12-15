@props([
'name',
'title' => '',
'description' => '',
'type' => 'default', // default, success, danger, warning, info
'show' => false,
'maxWidth' => '2xl',
'closeButton' => true,
'backdrop' => true,
'persistent' => false,
])

@php
$maxWidth = [
'sm' => 'sm:max-w-sm',
'md' => 'sm:max-w-md',
'lg' => 'sm:max-w-lg',
'xl' => 'sm:max-w-xl',
'2xl' => 'sm:max-w-2xl',
][$maxWidth];

$typeColors = [
'default' => 'border-blue-500 bg-blue-50 dark:bg-blue-900/20',
'success' => 'border-green-500 bg-green-50 dark:bg-green-900/20',
'danger' => 'border-red-500 bg-red-50 dark:bg-red-900/20',
'warning' => 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20',
'info' => 'border-cyan-500 bg-cyan-50 dark:bg-cyan-900/20',
];

$typeIcons = [
'default' => 'ℹ️',
'success' => '✓',
'danger' => '⚠️',
'warning' => '⚠️',
'info' => 'ℹ️',
];

$currentType = $typeColors[$type] ?? $typeColors['default'];
$icon = $typeIcons[$type] ?? $typeIcons['default'];
@endphp

<div x-data="{
        show: @js($show),
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                .filter(el => !el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
    }" x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            setTimeout(() => this.firstFocusable()?.focus(), 100);
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })" x-on:open-dialog.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-dialog.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="{{ $persistent ? '' : 'show = false' }}"
    x-on:keydown.escape.window="{{ $persistent ? '' : 'show = false' }}"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()" x-show="show" x-cloak
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" style="display: {{ $show ? 'block' : 'none' }};">
    <!-- Backdrop -->
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 dark:bg-black/70 transition-opacity"
        @click="{{ $persistent ? '' : 'show = false' }}"></div>

    <!-- Dialog -->
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="relative mx-auto my-12 w-full {{ $maxWidth }} rounded-lg shadow-xl dark:shadow-2xl bg-white dark:bg-slate-800 divide-y dark:divide-slate-700"
        @click.stop>
        <!-- Header -->
        <div class="px-6 py-4 flex items-start justify-between">
            <div class="flex-1">
                @if($title)
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="text-xl">{{ $icon }}</span>
                    {{ $title }}
                </h2>
                @endif
                @if($description)
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $description }}</p>
                @endif
            </div>
            @if($closeButton && !$persistent)
            <button @click="show = false"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                aria-label="Close dialog">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            @endif
        </div>

        <!-- Content -->
        <div class="px-6 py-4 text-gray-700 dark:text-gray-300">
            {{ $slot }}
        </div>

        <!-- Footer -->
        @if($footer ?? false)
        <div class="px-6 py-4 flex gap-3 justify-end">
            {{ $footer }}
        </div>
        @endif
    </div>
</div>