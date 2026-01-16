{{--
Badge Component

A status indicator badge with color variants and size options.

Props:
- variant: string (default: 'default') - Color variant (default, primary, success, warning, danger, info)
- size: string (default: 'md') - Size (sm, md, lg)
- dot: boolean (default: false) - Show a dot indicator

Usage:
<x-ui.badge>Draft</x-ui.badge>
<x-ui.badge variant="success">Active</x-ui.badge>
<x-ui.badge variant="warning" size="sm">Pending</x-ui.badge>
<x-ui.badge variant="danger" :dot="true">Failed</x-ui.badge>
--}}

@props([
'variant' => 'default',
'size' => 'md',
'dot' => false,
])

@php
$variants = [
'default' => 'bg-slate-700 text-slate-300',
'primary' => 'bg-primary-500/10 text-primary-400 border border-primary-500/20',
'success' => 'bg-success-500/10 text-success-400 border border-success-500/20',
'warning' => 'bg-warning-500/10 text-warning-400 border border-warning-500/20',
'danger' => 'bg-danger-500/10 text-danger-400 border border-danger-500/20',
'info' => 'bg-info-500/10 text-info-400 border border-info-500/20',
];

$sizes = [
'sm' => 'px-2 py-0.5 text-xs',
'md' => 'px-2.5 py-1 text-sm',
'lg' => 'px-3 py-1.5 text-base',
];

$dotColors = [
'default' => 'bg-slate-400',
'primary' => 'bg-primary-400',
'success' => 'bg-success-400',
'warning' => 'bg-warning-400',
'danger' => 'bg-danger-400',
'info' => 'bg-info-400',
];

$variantClass = $variants[$variant] ?? $variants['default'];
$sizeClass = $sizes[$size] ?? $sizes['md'];
$dotColorClass = $dotColors[$variant] ?? $dotColors['default'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full font-medium {$variantClass}
    {$sizeClass}"]) }}>
    @if($dot)
    <span class="w-1.5 h-1.5 rounded-full {{ $dotColorClass }}"></span>
    @endif
    {{ $slot }}
</span>