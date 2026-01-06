@use('App\Constants\Design')
<!-- Collapsed Icon Sidebar with Detail Panel (Dark Mode) -->
<div x-data="{ activeNav: null }" @click.away="activeNav = null" class="fixed left-0 top-0 h-screen z-50 flex"
    style="pointer-events: auto;">

    <!-- Icon Sidebar (Always Visible) -->
    <aside class="w-20 bg-slate-900 border-r border-slate-800 flex flex-col" style="min-width: 80px;">

        <!-- Logo -->
        <div class="flex items-center justify-center h-20 border-b border-slate-800 flex-shrink-0">
            <div
                class="{{ Design::SIDEBAR_ICON_SIZE }} rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center">
                <span class="text-white {{ Design::TEXT_LG }} {{ Design::FONT_BOLD }}">S</span>
            </div>
        </div>

        <!-- Navigation Icons -->
        <nav class="flex-1 {{ Design::PAD_Y_XL }} flex flex-col items-center {{ Design::SIDEBAR_ITEM_SPACING }}">

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                @click.prevent="activeNav = null; window.location.href = '{{ route('dashboard') }}'"
                class="relative group w-12 h-12 flex items-center justify-center rounded-lg transition-colors {{ request()->is('dashboard') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200' }}">
                <div style="left: calc(100% + 8px); top: 50%; transform: translateY(-50%);"
                    class="absolute bg-slate-800 text-white text-xs font-medium py-1.5 px-3 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                    Dashboard</div>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
            </a>

            <!-- HRM -->
            <button @click="activeNav = activeNav === 'hrm' ? null : 'hrm'"
                class="relative group w-12 h-12 flex items-center justify-center rounded-lg transition-colors"
                :class="activeNav === 'hrm' || {{ request()->routeIs('admin.hrm.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.feedback.*') || request()->routeIs('admin.complaints.*') || request()->routeIs('admin.announcements.*') ? 'true' : 'false' }} ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'">
                <div style="left: calc(100% + 8px); top: 50%; transform: translateY(-50%);"
                    class="absolute bg-slate-800 text-white text-xs font-medium py-1.5 px-3 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                    Human Resources</div>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
            </button>

            <!-- Service -->
            <button @click="activeNav = activeNav === 'service' ? null : 'service'"
                class="relative group w-12 h-12 flex items-center justify-center rounded-lg transition-colors"
                :class="activeNav === 'service' || {{ request()->routeIs('admin.leads.*') || request()->routeIs('admin.services.*') || request()->routeIs('admin.sites.*') ? 'true' : 'false' }} ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'">
                <div style="left: calc(100% + 8px); top: 50%; transform: translateY(-50%);"
                    class="absolute bg-slate-800 text-white text-xs font-medium py-1.5 px-3 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                    Service</div>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
            </button>

            <!-- Finance -->
            <button @click="activeNav = activeNav === 'finance' ? null : 'finance'"
                class="relative group w-12 h-12 flex items-center justify-center rounded-lg transition-colors"
                :class="activeNav === 'finance' || {{ request()->routeIs('admin.finance.*') ? 'true' : 'false' }} ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'">
                <div style="left: calc(100% + 8px); top: 50%; transform: translateY(-50%);"
                    class="absolute bg-slate-800 text-white text-xs font-medium py-1.5 px-3 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                    Finance</div>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
            </button>

        </nav>

    </aside>

    <!-- Detail Panel (Shows when nav item is clicked) -->
    <div x-show="activeNav" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 -translate-x-full" x-cloak
        class="w-64 bg-slate-900 border-r border-slate-800 h-screen overflow-y-auto flex-shrink-0">

        <!-- HRM Detail Panel -->
        <div x-show="activeNav === 'hrm'" class="{{ Design::SIDEBAR_SECTION_PADDING }}">
            <div class="flex items-center justify-between mb-6">
                <h2 class="{{ Design::TEXT_LG }} {{ Design::FONT_SEMIBOLD }} text-white">Human Resources</h2>
                <button @click="activeNav = null" class="text-slate-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- People Management -->
            <div x-data="{ open: true }" class="mb-6">
                <button @click="open = !open" class="w-full flex items-center justify-between py-2 text-left">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">People Management</h3>
                    <svg x-show="open" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    <svg x-show="!open" x-cloak class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-2 space-y-1">
                    <a href="{{ route('admin.hrm.attendance.active-users') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.hrm.attendance.active-users') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                            <circle cx="12" cy="12" r="4" fill="currentColor" class="text-lime-400" />
                        </svg>
                        <span>Who is Clocked In</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.hrm.employees.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <span>Employees</span>
                    </a>
                    <a href="{{ route('admin.hrm.organization.index') }}"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm rounded transition-colors {{ request()->routeIs('admin.hrm.organization.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <span>Organization</span>
                    </a>
                </div>
            </div>

            <!-- Payroll & Claims -->
            <div x-data="{ open: true }" class="mb-6">
                <button @click="open = !open" class="w-full flex items-center justify-between py-2 text-left">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Payroll & Claims</h3>
                    <svg x-show="open" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    <svg x-show="!open" x-cloak class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-2 space-y-1">
                    <a href="{{ route('admin.hrm.payroll.index') }}"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm rounded transition-colors {{ request()->routeIs('admin.hrm.payroll.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span>Payroll</span>
                    </a>
                    <a href="{{ route('admin.hrm.holidays.index') }}"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm rounded transition-colors {{ request()->routeIs('admin.hrm.holidays.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span>Holidays</span>
                    </a>
                    <a href="{{ route('admin.hrm.resource-requests.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.hrm.resource-requests.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m0 10v10l8 4">
                            </path>
                        </svg>
                        <span>Resource Requests</span>
                    </a>
                    <a href="{{ route('admin.hrm.expense-claims.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.hrm.expense-claims.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z">
                            </path>
                        </svg>
                        <span>Expense Claims</span>
                    </a>
                </div>
            </div>

            <!-- Leave Management -->
            <div x-data="{ open: true }" class="mb-6">
                <button @click="open = !open" class="w-full flex items-center justify-between py-2 text-left">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Leave Management</h3>
                    <svg x-show="open" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    <svg x-show="!open" x-cloak class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-2 space-y-1">
                    <a href="{{ route('admin.hrm.leaves.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.hrm.leaves.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span>Leave Requests</span>
                    </a>
                    <a href="{{ route('admin.hrm.leave-policies.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.hrm.leave-policies.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span>Leave Policies</span>
                    </a>
                </div>
            </div>

            <!-- Communication -->
            <div x-data="{ open: true }" class="mb-6">
                <button @click="open = !open" class="w-full flex items-center justify-between py-2 text-left">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Communication</h3>
                    <svg x-show="open" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    <svg x-show="!open" x-cloak class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-2 space-y-1">
                    <a href="{{ route('admin.feedback.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.feedback.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span>Weekly Feedback</span>
                    </a>
                    <a href="{{ route('admin.complaints.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.complaints.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                        <span>Staff Feedback</span>
                    </a>
                    <a href="{{ route('admin.announcements.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.announcements.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                            </path>
                        </svg>
                        <span>Announcements</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Service Detail Panel -->
        <div x-show="activeNav === 'service'" class="{{ Design::SIDEBAR_SECTION_PADDING }}">
            <div class="flex items-center justify-between mb-6">
                <h2 class="{{ Design::TEXT_LG }} {{ Design::FONT_SEMIBOLD }} text-white">Service</h2>
                <button @click="activeNav = null" class="text-slate-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Leads Management -->
            <div x-data="{ open: true }" class="mb-6">
                <button @click="open = !open" class="w-full flex items-center justify-between py-2 text-left">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Leads Management</h3>
                    <svg x-show="open" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    <svg x-show="!open" x-cloak class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-2 space-y-1">
                    <a href="{{ route('admin.leads.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.leads.*') && !request()->routeIs('admin.leads.analytics') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        <span>Service Leads</span>
                    </a>
                    <a href="{{ route('admin.leads.analytics') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.leads.analytics') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <span>Analytics</span>
                    </a>
                </div>
            </div>

            <!-- Service Configuration -->
            <div x-data="{ open: true }" class="mb-6">
                <button @click="open = !open" class="w-full flex items-center justify-between py-2 text-left">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Configuration</h3>
                    <svg x-show="open" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    <svg x-show="!open" x-cloak class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-2 space-y-1">
                    <a href="{{ route('admin.sites.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.sites.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                            </path>
                        </svg>
                        <span>Sites</span>
                    </a>
                    <a href="{{ route('admin.services.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.services.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span>Services</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Finance Detail Panel -->
        <div x-show="activeNav === 'finance'" class="{{ Design::SIDEBAR_SECTION_PADDING }}">
            <div class="flex items-center justify-between mb-6">
                <h2 class="{{ Design::TEXT_LG }} {{ Design::FONT_SEMIBOLD }} text-white">Finance</h2>
                <button @click="activeNav = null" class="text-slate-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Finance Dashboard -->
            <div class="mb-6">
                <a href="{{ route('admin.finance.dashboard') }}"
                    class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.finance.dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    <span>Finance Dashboard</span>
                </a>
            </div>

            <!-- Configuration -->
            <div x-data="{ open: true }" class="mb-6">
                <button @click="open = !open" class="w-full flex items-center justify-between py-2 text-left">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Configuration</h3>
                    <svg x-show="open" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    <svg x-show="!open" x-cloak class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-2 space-y-1">
                    <a href="{{ route('admin.finance.companies.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.finance.companies.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <span>Companies</span>
                    </a>
                </div>
            </div>

            <!-- Accounting -->
            <div x-data="{ open: true }" class="mb-6">
                <button @click="open = !open" class="w-full flex items-center justify-between py-2 text-left">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Accounting</h3>
                    <svg x-show="open" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    <svg x-show="!open" x-cloak class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-2 space-y-1">
                    <a href="{{ route('admin.finance.accounts.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.finance.accounts.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                        <span>Accounts</span>
                    </a>
                    <a href="{{ route('admin.finance.transactions.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.finance.transactions.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        <span>Transactions</span>
                    </a>
                </div>
            </div>

            <!-- Sales & Purchases -->
            <div x-data="{ open: true }" class="mb-6">
                <button @click="open = !open" class="w-full flex items-center justify-between py-2 text-left">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Sales & Purchases</h3>
                    <svg x-show="open" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    <svg x-show="!open" x-cloak class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-2 space-y-1">
                    <a href="{{ route('admin.finance.sales.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.finance.sales.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span>Sales</span>
                    </a>
                    <a href="{{ route('admin.finance.purchases.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.finance.purchases.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span>Purchases</span>
                    </a>
                    <a href="{{ route('admin.finance.customers.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.finance.customers.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span>Customers</span>
                    </a>
                    <a href="{{ route('admin.finance.vendors.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.finance.vendors.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <span>Vendors</span>
                    </a>
                </div>
            </div>

            <!-- Reports & Analysis -->
            <div x-data="{ open: true }" class="mb-6">
                <button @click="open = !open" class="w-full flex items-center justify-between py-2 text-left">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Reports & Analysis</h3>
                    <svg x-show="open" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    <svg x-show="!open" x-cloak class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-2 space-y-1">
                    <a href="{{ route('admin.finance.budgets.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.finance.budgets.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span>Budgets</span>
                    </a>
                    <a href="{{ route('admin.finance.recurring-expenses.index') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.finance.recurring-expenses.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span>Recurring Expenses</span>
                    </a>
                    <a href="{{ route('admin.finance.reports') }}"
                        class="{{ Design::MENU_ITEM }} {{ request()->routeIs('admin.finance.reports') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span>Reports</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>