@props(['name', 'label', 'icon', 'color' => 'blue', 'active' => false])

@php
$isActive = $active || request()->routeIs("admin.{$name}.*");
@endphp

<div x-data="{ 
    open: {{ $isActive ? 'true' : 'false' }},
    init() {
        const saved = localStorage.getItem('nav_module_{{ $name }}');
        if (saved !== null) {
            this.open = saved === 'true';
        }
        this.$watch('open', value => {
            localStorage.setItem('nav_module_{{ $name }}', value);
            if (value) {
                window.dispatchEvent(new CustomEvent('module-opened', { detail: '{{ $name }}' }));
            }
        });
        
        window.addEventListener('module-opened', (e) => {
            if (e.detail !== '{{ $name }}' && this.open) {
                this.open = false;
            }
        });
    }
}" class="mb-2">
    <button @click="open = !open"
        class="flex items-center w-full px-6 py-3 text-sm font-medium transition-all duration-200 group text-slate-200 hover:bg-slate-800/30">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 transition-transform duration-200 text-slate-400"
                :class="open ? 'rotate-0' : '-rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
            <span>{{ $label }}</span>
        </div>
    </button>

    <div x-show="open" x-collapse x-cloak class="pb-2">
        {{ $slot }}
    </div>
</div>