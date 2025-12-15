@props([
'name',
'title' => 'Confirm Action',
'message' => 'Are you sure?',
'confirmText' => 'Confirm',
'cancelText' => 'Cancel',
'type' => 'default', // default, danger, warning, success
'onConfirm' => '', // JavaScript callback or form submit
'form' => null, // Optional form ID to submit
])

@php
$buttonColors = [
'default' => 'bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800',
'danger' => 'bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800',
'warning' => 'bg-yellow-600 hover:bg-yellow-700 dark:bg-yellow-700 dark:hover:bg-yellow-800',
'success' => 'bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800',
];

$currentColor = $buttonColors[$type] ?? $buttonColors['default'];
@endphp

<div x-data="{
        show: false,
        confirmAction() {
            @if($form)
                document.getElementById('{{ $form }}').submit();
            @else
                {{ $onConfirm }};
            @endif
            this.show = false;
        }
    }" x-on:open-confirm.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-confirm.window="$event.detail == '{{ $name }}' ? show = false : null">
    <!-- Backdrop -->
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 dark:bg-black/70 transition-opacity z-50" @click="show = false"></div>

    <!-- Dialog -->
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center px-4" x-cloak @click.stop="show = false">
        <div @click.stop
            class="w-full max-w-sm rounded-lg shadow-xl dark:shadow-2xl bg-white dark:bg-slate-800 overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
            </div>

            <!-- Message -->
            <div class="px-6 py-4">
                <p class="text-gray-700 dark:text-gray-300">{{ $message }}</p>
            </div>

            <!-- Footer -->
            <div
                class="px-6 py-4 border-t dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50 flex gap-3 justify-end">
                <button @click="show = false" type="button"
                    class="px-4 py-2 rounded-lg font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-slate-600 hover:bg-gray-300 dark:hover:bg-slate-500 transition-colors">
                    {{ $cancelText }}
                </button>
                <button @click="confirmAction()" type="button"
                    class="px-4 py-2 rounded-lg font-medium text-white {{ $currentColor }} transition-colors">
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>