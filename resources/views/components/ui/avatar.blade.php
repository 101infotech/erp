{{--
Avatar Component

User profile images with fallback initials and status indicators.

Props:
- src: string (optional) - Image URL
- alt: string (optional) - Alt text
- name: string (optional) - Name for initials fallback
- size: string (default: 'md') - Size (xs, sm, md, lg, xl, 2xl)
- status: string (optional) - Status indicator (online, offline, away, busy)
- rounded: string (default: 'full') - Border radius (full, lg, md, none)

Usage:
<x-ui.avatar src="/images/user.jpg" alt="John Doe" />
<x-ui.avatar name="John Doe" size="lg" status="online" />
<x-ui.avatar name="JD" size="sm" />
--}}

@props([
'src' => null,
'alt' => '',
'name' => '',
'size' => 'md',
'status' => null,
'rounded' => 'full',
])

@php
$sizes = [
'xs' => 'w-6 h-6 text-xs',
'sm' => 'w-8 h-8 text-sm',
'md' => 'w-10 h-10 text-base',
'lg' => 'w-12 h-12 text-lg',
'xl' => 'w-16 h-16 text-xl',
'2xl' => 'w-20 h-20 text-2xl',
];

$roundedClasses = [
'full' => 'rounded-full',
'lg' => 'rounded-lg',
'md' => 'rounded-md',
'none' => 'rounded-none',
];

$statusColors = [
'online' => 'bg-success-500',
'offline' => 'bg-slate-500',
'away' => 'bg-warning-500',
'busy' => 'bg-danger-500',
];

$sizeClass = $sizes[$size] ?? $sizes['md'];
$roundedClass = $roundedClasses[$rounded] ?? $roundedClasses['full'];
$statusColor = isset($statusColors[$status]) ? $statusColors[$status] : null;

// Generate initials from name
$initials = '';
if ($name) {
$words = explode(' ', trim($name));
if (count($words) >= 2) {
$initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
} else {
$initials = strtoupper(substr($name, 0, 2));
}
}

// Generate background color from name
$colors = ['bg-red-500', 'bg-orange-500', 'bg-amber-500', 'bg-lime-500', 'bg-green-500', 'bg-teal-500', 'bg-cyan-500',
'bg-blue-500', 'bg-indigo-500', 'bg-purple-500', 'bg-pink-500'];
$colorIndex = $name ? (ord($name[0]) % count($colors)) : 0;
$bgColor = $colors[$colorIndex];
@endphp

<div {{ $attributes->merge(['class' => "relative inline-block {$sizeClass}"]) }}>
    @if($src)
    <img src="{{ $src }}" alt="{{ $alt }}"
        class="{{ $sizeClass }} {{ $roundedClass }} object-cover border-2 border-slate-700" />
    @else
    <div
        class="{{ $sizeClass }} {{ $roundedClass }} {{ $bgColor }} flex items-center justify-center text-white font-semibold border-2 border-slate-700">
        {{ $initials ?: '?' }}
    </div>
    @endif

    @if($statusColor)
    <span
        class="absolute bottom-0 right-0 block {{ $size === 'xs' || $size === 'sm' ? 'w-2 h-2' : 'w-3 h-3' }} {{ $statusColor }} rounded-full ring-2 ring-slate-900"></span>
    @endif
</div>