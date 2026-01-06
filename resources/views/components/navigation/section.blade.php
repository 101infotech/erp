@props(['label', 'defaultOpen' => true])

<div x-data="{ open: {{ $defaultOpen ? 'true' : 'false' }} }" class="mb-4">
    <button @click="open = !open"
        class="w-full flex items-center justify-between px-6 py-2.5 text-left group hover:bg-slate-800/30 transition-colors">
        <h3 class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
            {{ $label }}
        </h3>
        <span class="text-slate-500 transition-colors group-hover:text-slate-300">
            <svg x-show="open" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"></path>
            </svg>
            <svg x-show="!open" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
        </span>
    </button>

    <div x-show="open" x-collapse class="space-y-0.5">
        {{ $slot }}
    </div>
</div>