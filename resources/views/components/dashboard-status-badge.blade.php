{{-- Dashboard Status Badge Component --}}
@php
$styles = [
    'pending' => 'bg-yellow-500/20 text-yellow-400',
    'approved' => 'bg-green-500/20 text-green-400',
    'rejected' => 'bg-red-500/20 text-red-400',
    'draft' => 'bg-slate-500/20 text-slate-400',
    'paid' => 'bg-green-500/20 text-green-400',
    'active' => 'bg-lime-500/20 text-lime-400',
    'inactive' => 'bg-slate-500/20 text-slate-400',
];

$selectedStyle = $styles[$status] ?? 'bg-slate-500/20 text-slate-400';
@endphp

<span class="px-3 py-1 rounded-full text-xs font-semibold {{ $selectedStyle }}">
    {{ $label ?? ucfirst($status) }}
</span>

@props(['status' => 'pending', 'label' => null])
