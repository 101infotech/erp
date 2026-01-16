{{--
Toast Component

Temporary notification messages that appear and auto-dismiss.

Props:
- variant: string (default: 'info') - Toast type (success, warning, danger, info)
- title: string (optional) - Toast title
- duration: number (default: 5000) - Auto-dismiss duration in ms (0 = no auto-dismiss)
- position: string (default: 'top-right') - Position (top-right, top-left, bottom-right, bottom-left, top-center,
bottom-center)

Usage in Blade:
This component is meant to be triggered via Alpine.js events.

Add to layout:
<div x-data="toastManager()" @toast.window="addToast($event.detail)">
    <template x-for="toast in toasts" :key="toast.id">
        <x-ui.toast />
    </template>
</div>

Trigger from JavaScript:
window.dispatchEvent(new CustomEvent('toast', {
detail: { variant: 'success', title: 'Saved!', message: 'Changes saved successfully' }
}))
--}}

@props([
'variant' => 'info',
'title' => null,
'duration' => 5000,
'position' => 'top-right',
])

@php
$variants = [
'success' => [
'bg' => 'bg-success-500/10',
'border' => 'border-success-500/20',
'text' => 'text-success-400',
'icon' => '
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
],
'warning' => [
'bg' => 'bg-warning-500/10',
'border' => 'border-warning-500/20',
'text' => 'text-warning-400',
'icon' => '
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
',
],
'danger' => [
'bg' => 'bg-danger-500/10',
'border' => 'border-danger-500/20',
'text' => 'text-danger-400',
'icon' => '
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
],
'info' => [
'bg' => 'bg-info-500/10',
'border' => 'border-info-500/20',
'text' => 'text-info-400',
'icon' => '
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
],
];

$positions = [
'top-right' => 'top-4 right-4',
'top-left' => 'top-4 left-4',
'bottom-right' => 'bottom-4 right-4',
'bottom-left' => 'bottom-4 left-4',
'top-center' => 'top-4 left-1/2 -translate-x-1/2',
'bottom-center' => 'bottom-4 left-1/2 -translate-x-1/2',
];

$config = $variants[$variant] ?? $variants['info'];
$positionClass = $positions[$position] ?? $positions['top-right'];
@endphp

{{-- Toast Container Template --}}
<div x-data="{
        show: true,
        duration: {{ $duration }},
        init() {
            if (this.duration > 0) {
                setTimeout(() => this.show = false, this.duration);
            }
        }
    }" x-show="show" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    class="fixed {{ $positionClass }} z-toast w-full max-w-sm pointer-events-auto">
    <div class="bg-slate-800 border {{ $config['border'] }} rounded-lg shadow-2xl overflow-hidden">
        <div class="p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 {{ $config['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $config['icon'] !!}
                    </svg>
                </div>

                <div class="flex-1 min-w-0">
                    @if($title)
                    <p class="text-sm font-semibold text-white mb-1" x-text="toast.title || '{{ $title }}'">{{ $title }}
                    </p>
                    @endif
                    <p class="text-sm {{ $config['text'] }}" x-text="toast.message || ''">{{ $slot }}</p>
                </div>

                <button type="button" @click="show = false"
                    class="flex-shrink-0 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        @if($duration > 0)
        <div class="h-1 {{ $config['bg'] }}">
            <div class="h-full {{ $config['text'] }} bg-current" x-data="{ width: 100 }" x-init="
                    let interval = setInterval(() => {
                        width -= 100 / ({{ $duration }} / 100);
                        if (width <= 0) clearInterval(interval);
                    }, 100);
                " :style="`width: ${width}%`"></div>
        </div>
        @endif
    </div>
</div>

@once
@push('scripts')
<script>
    function toastManager() {
    return {
        toasts: [],
        addToast(toast) {
            const id = Date.now();
            this.toasts.push({ id, ...toast });
            
            if (toast.duration !== 0) {
                setTimeout(() => {
                    this.removeToast(id);
                }, toast.duration || 5000);
            }
        },
        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }
}
</script>
@endpush
@endonce