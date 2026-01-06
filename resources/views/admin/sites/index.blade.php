@extends('admin.layouts.app')
@use('App\Constants\Design')

@section('title', 'Sites')
@section('page-title', 'Manage Sites')

@section('content')
<!-- Header -->
<div class="{{ Design::MARGIN_LG }}">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between {{ Design::GAP_LG }}">
        <div>
            <h2 class="{{ Design::TEXT_3XL }} {{ Design::FONT_BOLD }} text-white">Sites</h2>
            <p class="text-slate-400 {{ Design::MARGIN_Y_SM }}">Manage content for each company website</p>
        </div>
        <a href="{{ route('admin.sites.create') }}"
            class="{{ Design::BTN_PADDING }} bg-lime-500 text-slate-950 {{ Design::FONT_SEMIBOLD }} rounded-lg hover:bg-lime-400 transition flex items-center {{ Design::GAP_SM }} w-fit">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add New Site</span>
        </a>
    </div>
</div>

<!-- Sites Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 {{ Design::GAP_LG }}">
    @forelse($sites as $site)
    <div
        class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-xl overflow-hidden hover:border-lime-500/50 transition-all group">
        <!-- Card Content -->
        <div class="{{ Design::CARD_PADDING }}">
            <div class="flex items-start justify-between {{ Design::MARGIN_Y_MD }}">
                <div class="flex-1">
                    <h3 class="{{ Design::TEXT_XL }} {{ Design::FONT_BOLD }} text-white group-hover:text-lime-400 transition">{{ $site->name }}</h3>
                    <p class="{{ Design::TEXT_SM }} text-slate-400 {{ Design::MARGIN_Y_SM }}">{{ $site->slug }}</p>
                    @if($site->domain)
                    <a href="https://{{ $site->domain }}" target="_blank"
                        class="text-xs text-lime-400 hover:text-lime-300 {{ Design::MARGIN_Y_SM }} inline-block">
                        {{ $site->domain }} ↗
                    </a>
                    @endif
                </div>
                <span
                    class="px-3 py-1 {{ Design::TEXT_XS }} {{ Design::FONT_SEMIBOLD }} rounded-full {{ $site->is_active ? 'bg-lime-500/20 text-lime-400' : 'bg-red-500/20 text-red-400' }}">
                    {{ $site->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            @if($site->description)
            <p class="{{ Design::TEXT_SM }} text-slate-300 {{ Design::MARGIN_Y_MD }} {{ Design::MARGIN_Y_LG }}">{{ Str::limit($site->description, 100) }}</p>
            @endif

            <a href="{{ route('admin.sites.dashboard', $site) }}"
                class="block w-full {{ Design::BTN_PADDING }} bg-lime-500 text-slate-950 {{ Design::FONT_SEMIBOLD }} rounded-lg hover:bg-lime-400 transition text-center">
                <span class="flex items-center justify-center {{ Design::GAP_SM }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Manage Content</span>
                </span>
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-xl {{ Design::CARD_PADDING }} text-center">
        <svg class="w-16 h-16 mx-auto {{ Design::MARGIN_Y_MD }} text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
        </svg>
        <p class="text-slate-400 {{ Design::TEXT_LG }}">No sites found</p>
        <p class="text-slate-500 {{ Design::TEXT_SM }} {{ Design::MARGIN_Y_SM }}">Create your first site to get started</p>
    </div>
    @endforelse
</div>
@endsection