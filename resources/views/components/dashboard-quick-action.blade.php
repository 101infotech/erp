@props(['title' => '', 'subtitle' => null, 'color' => 'cyan', 'href' => '#'])

{{-- Dashboard Quick Action Card Component --}}
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex items-center gap-3 p-3 bg-slate-700/50 rounded-lg
    hover:bg-slate-700 transition-colors']) }}>
    @if($slot->isNotEmpty())
    <div class="w-10 h-10 bg-{{ $color }}-500/20 rounded-xl flex items-center justify-center">
        {{ $slot }}
    </div>
    @endif
    <div>
        <p class="text-white font-medium text-sm">{{ $title }}</p>
        @if(isset($subtitle))
        <p class="text-slate-400 text-xs">{{ $subtitle }}</p>
        @endif
    </div>
</a>