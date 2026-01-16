{{--
Empty State Component

Placeholder for empty data, no results, or blank states.

Props:
- icon: string (optional) - Icon type (inbox, search, folder, users, file)
- title: string (required) - Empty state title
- description: string (optional) - Empty state description
- action: string (optional) - Action button text
- actionHref: string (optional) - Action button link

Slots:
- icon: Custom icon
- action: Custom action buttons

Usage:
<x-ui.empty-state icon="inbox" title="No messages" description="You don't have any messages yet." action="Send Message"
    actionHref="/messages/new" />
--}}

@props([
'icon' => 'inbox',
'title' => '',
'description' => null,
'action' => null,
'actionHref' => null,
])

@php
$icons = [
'inbox' => '
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
',
'search' => '
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
',
'folder' => '
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />',
'users' => '
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
',
'file' => '
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />',
];

$iconPath = $icons[$icon] ?? $icons['inbox'];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-12 px-4']) }}>
    <div class="w-20 h-20 rounded-full bg-slate-800 flex items-center justify-center mb-4">
        @if(isset($icon) && $slot->isNotEmpty())
        {{ $icon }}
        @else
        <svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $iconPath !!}
        </svg>
        @endif
    </div>

    <h3 class="text-lg font-semibold text-white mb-2">{{ $title }}</h3>

    @if($description)
    <p class="text-sm text-slate-400 max-w-md mb-6">{{ $description }}</p>
    @endif

    @if(isset($action) && $slot->isNotEmpty())
    <div class="mt-4">
        {{ $action }}
    </div>
    @elseif($action)
    <div class="mt-4">
        @if($actionHref)
        <x-ui.button :href="$actionHref">{{ $action }}</x-ui.button>
        @else
        <x-ui.button>{{ $action }}</x-ui.button>
        @endif
    </div>
    @endif
</div>