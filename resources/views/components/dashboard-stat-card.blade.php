{{-- Dashboard Stat Card Component --}}
<div
    class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 hover:border-{{ $color }}-500/50 transition-colors">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-slate-400 text-xs mb-1.5">{{ $title }}</p>
            <h2 class="text-2xl font-bold text-white">{{ $value }}</h2>
            @if(isset($subtitle))
            <p class="text-xs text-slate-500 mt-1">{{ $subtitle }}</p>
            @endif
            @if(isset($metric))
            <p class="text-xs {{ str_contains($metric, '+') ? 'text-green-400' : 'text-red-400' }} mt-1">{{ $metric }}
            </p>
            @endif
        </div>
        <div class="w-10 h-10 bg-{{ $color }}-500/10 rounded-xl flex items-center justify-center">
            {!! $icon !!}
        </div>
    </div>
</div>

@props(['title' => '', 'value' => 0, 'subtitle' => null, 'metric' => null, 'color' => 'blue', 'icon' => ''])