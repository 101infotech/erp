{{--
Breadcrumb Component

Page hierarchy navigation with links and separators.

Props:
- items: array (required) - Breadcrumb items [['label' => 'Home', 'url' => '/'], ...]
- separator: string (default: 'chevron') - Separator type (chevron, slash, dot)

Usage:
<x-ui.breadcrumb :items="[
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Users', 'url' => '/users'],
        ['label' => 'John Doe']
    ]" />

<x-ui.breadcrumb :items="$breadcrumbs" separator="slash" />
--}}

@props([
'items' => [],
'separator' => 'chevron',
])

@php
$separators = [
'chevron' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>',
'slash' => '<span class="text-slate-500">/</span>',
'dot' => '<span class="text-slate-500">•</span>',
];

$separatorIcon = $separators[$separator] ?? $separators['chevron'];
@endphp

<nav {{ $attributes->merge(['class' => 'flex items-center space-x-2 text-sm']) }} aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        @foreach($items as $index => $item)
        <li class="flex items-center">
            @if($index > 0)
            <span class="mx-2 text-slate-500">
                {!! $separatorIcon !!}
            </span>
            @endif

            @if(isset($item['url']) && $index < count($items) - 1) <a href="{{ $item['url'] }}"
                class="text-slate-400 hover:text-primary-400 transition-colors">
                {{ $item['label'] }}
                </a>
                @else
                <span class="text-white font-medium">
                    {{ $item['label'] }}
                </span>
                @endif
        </li>
        @endforeach
    </ol>
</nav>