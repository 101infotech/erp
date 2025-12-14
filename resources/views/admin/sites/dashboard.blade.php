@extends('admin.layouts.app')

@section('title', $site->name . ' - Content Management')
@section('page-title', $site->name)

@section('content')
<!-- Header with Back Button -->
<div class="mb-8">
    <div class="flex items-center gap-4 mb-4">
        <a href="{{ route('admin.sites.index') }}"
            class="flex items-center text-slate-400 hover:text-lime-400 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Sites
        </a>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-white">{{ $site->name }}</h2>
            <p class="text-slate-400 mt-2">Manage all content for this site</p>
            @if($site->domain)
            <a href="https://{{ $site->domain }}" target="_blank"
                class="text-sm text-lime-400 hover:text-lime-300 mt-2 inline-block">
                {{ $site->domain }} ↗
            </a>
            @endif
        </div>
        <div class="flex gap-3 items-start">
            <span
                class="px-4 py-2 text-sm font-semibold rounded-lg {{ $site->is_active ? 'bg-lime-500/20 text-lime-400' : 'bg-red-500/20 text-red-400' }}">
                {{ $site->is_active ? 'Active' : 'Inactive' }}
            </span>
            <a href="{{ route('admin.sites.edit', $site) }}"
                class="px-4 py-2 text-sm bg-slate-700 text-slate-300 rounded-lg hover:bg-slate-600 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Site
            </a>
            <form action="{{ route('admin.sites.destroy', $site) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete {{ $site->name }}? This will remove all associated content.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-4 py-2 text-sm bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Content Management Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- Services (Saubhagya Ghar, Brand Bird) -->
    @if($site->slug === 'saubhagya-ghar' || $site->slug === 'brand-bird-agency')
    <div
        class="bg-gradient-to-br from-blue-500/10 to-blue-600/5 border border-blue-500/20 rounded-xl p-6 hover:border-blue-400/40 hover:shadow-lg hover:shadow-blue-500/10 transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-blue-500/20 rounded-lg group-hover:bg-blue-500/30 transition">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-3xl font-bold text-blue-400">{{ $site->services_count ?? 0 }}</span>
        </div>
        <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-blue-400 transition">Services</h3>
        <p class="text-sm text-slate-400 mb-4">Manage service offerings</p>
        <a href="{{ route('admin.services.index') }}?site={{ $site->id }}"
            class="block w-full px-4 py-2.5 bg-blue-500/20 text-blue-400 font-medium rounded-lg hover:bg-blue-500/30 transition text-center">
            Manage Services
        </a>
    </div>
    @endif

    <!-- Blogs (Saubhagya Ghar) -->
    @if($site->slug === 'saubhagya-ghar')
    <div
        class="bg-gradient-to-br from-purple-500/10 to-purple-600/5 border border-purple-500/20 rounded-xl p-6 hover:border-purple-400/40 hover:shadow-lg hover:shadow-purple-500/10 transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-purple-500/20 rounded-lg group-hover:bg-purple-500/30 transition">
                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
            </div>
            <span class="text-3xl font-bold text-purple-400">{{ $site->blogs_count ?? 0 }}</span>
        </div>
        <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-purple-400 transition">Blogs</h3>
        <p class="text-sm text-slate-400 mb-4">Manage blog articles</p>
        <a href="{{ route('admin.blogs.index') }}?site={{ $site->id }}"
            class="block w-full px-4 py-2.5 bg-purple-500/20 text-purple-400 font-medium rounded-lg hover:bg-purple-500/30 transition text-center">
            Manage Blogs
        </a>
    </div>
    @endif

    <!-- Case Studies (Brand Bird) -->
    @if($site->slug === 'brand-bird-agency')
    <div
        class="bg-gradient-to-br from-orange-500/10 to-orange-600/5 border border-orange-500/20 rounded-xl p-6 hover:border-orange-400/40 hover:shadow-lg hover:shadow-orange-500/10 transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-orange-500/20 rounded-lg group-hover:bg-orange-500/30 transition">
                <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <span class="text-3xl font-bold text-orange-400">{{ $site->case_studies_count ?? 0 }}</span>
        </div>
        <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-orange-400 transition">Case Studies</h3>
        <p class="text-sm text-slate-400 mb-4">Showcase portfolio work</p>
        <a href="{{ route('admin.case-studies.index') }}?site={{ $site->id }}"
            class="block w-full px-4 py-2.5 bg-orange-500/20 text-orange-400 font-medium rounded-lg hover:bg-orange-500/30 transition text-center">
            Manage Case Studies
        </a>
    </div>
    @endif

    <!-- Companies List (Saubhagya Group) -->
    @if($site->slug === 'saubhagya-group')
    <div
        class="bg-gradient-to-br from-indigo-500/10 to-indigo-600/5 border border-indigo-500/20 rounded-xl p-6 hover:border-indigo-400/40 hover:shadow-lg hover:shadow-indigo-500/10 transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-indigo-500/20 rounded-lg group-hover:bg-indigo-500/30 transition">
                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <span class="text-3xl font-bold text-indigo-400">{{ $site->companies_list_count ?? 0 }}</span>
        </div>
        <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-indigo-400 transition">Companies</h3>
        <p class="text-sm text-slate-400 mb-4">Manage subsidiary companies</p>
        <a href="{{ route('admin.companies-list.index') }}?site={{ $site->id }}"
            class="block w-full px-4 py-2.5 bg-indigo-500/20 text-indigo-400 font-medium rounded-lg hover:bg-indigo-500/30 transition text-center">
            Manage Companies
        </a>
    </div>
    @endif

    <!-- Media (Saubhagya Group) -->
    @if($site->slug === 'saubhagya-group')
    <div
        class="bg-gradient-to-br from-pink-500/10 to-pink-600/5 border border-pink-500/20 rounded-xl p-6 hover:border-pink-400/40 hover:shadow-lg hover:shadow-pink-500/10 transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-pink-500/20 rounded-lg group-hover:bg-pink-500/30 transition">
                <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-3xl font-bold text-pink-400">{{ $site->news_media_count ?? 0 }}</span>
        </div>
        <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-pink-400 transition">News & Media</h3>
        <p class="text-sm text-slate-400 mb-4">Manage media coverage</p>
        <a href="{{ route('admin.news-media.index') }}?site={{ $site->id }}"
            class="block w-full px-4 py-2.5 bg-pink-500/20 text-pink-400 font-medium rounded-lg hover:bg-pink-500/30 transition text-center">
            Manage Media
        </a>
    </div>
    @endif

    <!-- Hirings (Saubhagya Group) -->
    @if($site->slug === 'saubhagya-group')
    <div
        class="bg-gradient-to-br from-teal-500/10 to-teal-600/5 border border-teal-500/20 rounded-xl p-6 hover:border-teal-400/40 hover:shadow-lg hover:shadow-teal-500/10 transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-teal-500/20 rounded-lg group-hover:bg-teal-500/30 transition">
                <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <span class="text-3xl font-bold text-teal-400">{{ $site->hirings_count ?? 0 }}</span>
        </div>
        <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-teal-400 transition">Job Openings</h3>
        <p class="text-sm text-slate-400 mb-4">Manage hiring positions</p>
        <a href="{{ route('admin.hirings.index') }}?site={{ $site->id }}"
            class="block w-full px-4 py-2.5 bg-teal-500/20 text-teal-400 font-medium rounded-lg hover:bg-teal-500/30 transition text-center">
            Manage Hirings
        </a>
    </div>
    @endif

    <!-- Team Members (All Sites) -->
    <div
        class="bg-gradient-to-br from-cyan-500/10 to-cyan-600/5 border border-cyan-500/20 rounded-xl p-6 hover:border-cyan-400/40 hover:shadow-lg hover:shadow-cyan-500/10 transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-cyan-500/20 rounded-lg group-hover:bg-cyan-500/30 transition">
                <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <span class="text-3xl font-bold text-cyan-400">{{ $site->team_members_count ?? 0 }}</span>
        </div>
        <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-cyan-400 transition">Team Members</h3>
        <p class="text-sm text-slate-400 mb-4">Manage team profiles</p>
        <a href="{{ route('admin.team-members.index') }}?site={{ $site->id }}"
            class="block w-full px-4 py-2.5 bg-cyan-500/20 text-cyan-400 font-medium rounded-lg hover:bg-cyan-500/30 transition text-center">
            Manage Team
        </a>
    </div>

    <!-- Contact Forms (All Sites) -->
    <div
        class="bg-gradient-to-br from-green-500/10 to-green-600/5 border border-green-500/20 rounded-xl p-6 hover:border-green-400/40 hover:shadow-lg hover:shadow-green-500/10 transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-green-500/20 rounded-lg group-hover:bg-green-500/30 transition">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-3xl font-bold text-green-400">{{ $site->contact_forms_count ?? 0 }}</span>
        </div>
        <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-green-400 transition">Contact Submissions</h3>
        <p class="text-sm text-slate-400 mb-4">View contact form entries</p>
        <a href="{{ route('admin.contact-forms.index') }}?site={{ $site->id }}"
            class="block w-full px-4 py-2.5 bg-green-500/20 text-green-400 font-medium rounded-lg hover:bg-green-500/30 transition text-center">
            View Contacts
        </a>
    </div>

    <!-- Booking Forms (Not for Saubhagya Group) -->
    @if($site->slug !== 'saubhagya-group')
    <div
        class="bg-gradient-to-br from-yellow-500/10 to-yellow-600/5 border border-yellow-500/20 rounded-xl p-6 hover:border-yellow-400/40 hover:shadow-lg hover:shadow-yellow-500/10 transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-yellow-500/20 rounded-lg group-hover:bg-yellow-500/30 transition">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-3xl font-bold text-yellow-400">{{ $site->booking_forms_count ?? 0 }}</span>
        </div>
        <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-yellow-400 transition">Booking Submissions
        </h3>
        <p class="text-sm text-slate-400 mb-4">View booking requests</p>
        <a href="{{ route('admin.booking-forms.index') }}?site={{ $site->id }}"
            class="block w-full px-4 py-2.5 bg-yellow-500/20 text-yellow-400 font-medium rounded-lg hover:bg-yellow-500/30 transition text-center">
            View Bookings
        </a>
    </div>
    @endif

    <!-- Schedule Meetings (Saubhagya Group) -->
    @if($site->slug === 'saubhagya-group')
    <div
        class="bg-gradient-to-br from-red-500/10 to-red-600/5 border border-red-500/20 rounded-xl p-6 hover:border-red-400/40 hover:shadow-lg hover:shadow-red-500/10 transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-red-500/20 rounded-lg group-hover:bg-red-500/30 transition">
                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-3xl font-bold text-red-400">{{ $site->schedule_meetings_count ?? 0 }}</span>
        </div>
        <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-red-400 transition">Meeting Requests</h3>
        <p class="text-sm text-slate-400 mb-4">Manage scheduled meetings</p>
        <a href="{{ route('admin.schedule-meetings.index') }}?site={{ $site->id }}"
            class="block w-full px-4 py-2.5 bg-red-500/20 text-red-400 font-medium rounded-lg hover:bg-red-500/30 transition text-center">
            View Meetings
        </a>
    </div>
    @endif
</div>
@endsection