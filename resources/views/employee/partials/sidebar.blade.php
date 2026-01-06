@use('App\Constants\Design')
<div class="fixed left-0 top-0 h-screen w-20 md:w-64 bg-slate-900 border-r border-slate-800 overflow-y-auto z-40">
    <!-- Logo/Brand -->
    <div class="{{ Design::PAD_SM }} border-b border-slate-800">
        <div class="{{ Design::GAP_LG }} flex items-center justify-center md:justify-start">
            <div
                class="{{ Design::SIDEBAR_ICON_SIZE }} rounded-lg bg-gradient-to-br from-lime-500 to-lime-600 flex items-center justify-center">
                <span class="text-slate-950 {{ Design::TEXT_LG }} {{ Design::FONT_BOLD }}">S</span>
            </div>
            <div class="hidden md:block">
                <div class="{{ Design::TEXT_SM }} {{ Design::FONT_SEMIBOLD }} text-white">Saubhagya ERP</div>
                <div class="{{ Design::TEXT_XS }} text-slate-400">Employee Portal</div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-4 px-2 md:px-3 space-y-2">
        <!-- Dashboard -->
        <a href="{{ route('employee.dashboard') }}"
            class="flex items-center justify-center md:justify-start md:px-4 md:gap-3 md:py-2.5 px-0 gap-0 py-3 rounded-lg transition {{ request()->is('employee/dashboard') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
            <svg class="w-5 h-5 md:mr-3 mr-0 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="hidden md:inline">Dashboard</span>
        </a>

        <!-- Attendance -->
        <a href="{{ route('employee.attendance.index') }}"
            class="flex items-center justify-center md:justify-start md:px-4 md:gap-3 md:py-2.5 px-0 gap-0 py-3 rounded-lg transition {{ request()->routeIs('employee.attendance.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
            <svg class="w-5 h-5 md:mr-3 mr-0 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <span class="hidden md:inline">Attendance</span>
        </a>

        <!-- Payroll -->
        <a href="{{ route('employee.payroll.index') }}"
            class="flex items-center justify-center md:justify-start md:px-4 md:gap-3 md:py-2.5 px-0 gap-0 py-3 rounded-lg transition {{ request()->routeIs('employee.payroll.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
            <svg class="w-5 h-5 md:mr-3 mr-0 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="hidden md:inline">Payroll</span>
        </a>

        <!-- Leave Requests -->
        <a href="{{ route('employee.leave.index') }}"
            class="flex items-center justify-center md:justify-start md:px-4 md:gap-3 md:py-2.5 px-0 gap-0 py-3 rounded-lg transition {{ request()->routeIs('employee.leave.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
            <svg class="w-5 h-5 md:mr-3 mr-0 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="hidden md:inline">Leave Requests</span>
        </a>

        <!-- Holidays -->
        <a href="{{ route('employee.holidays.index') }}"
            class="flex items-center justify-center md:justify-start md:px-4 md:gap-3 md:py-2.5 px-0 gap-0 py-3 rounded-lg transition {{ request()->routeIs('employee.holidays.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
            <svg class="w-5 h-5 md:mr-3 mr-0 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="hidden md:inline">Holidays</span>
        </a>

        <!-- Resource Requests -->
        <a href="{{ route('employee.resource-requests.index') }}"
            class="flex items-center justify-center md:justify-start md:px-4 md:gap-3 md:py-2.5 px-0 gap-0 py-3 rounded-lg transition {{ request()->routeIs('employee.resource-requests.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
            <svg class="w-5 h-5 md:mr-3 mr-0 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span class="hidden md:inline">Resource Requests</span>
        </a>

        <!-- Expense Claims -->
        <a href="{{ route('employee.expense-claims.index') }}"
            class="flex items-center justify-center md:justify-start md:px-4 md:gap-3 md:py-2.5 px-0 gap-0 py-3 rounded-lg transition {{ request()->routeIs('employee.expense-claims.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
            <svg class="w-5 h-5 md:mr-3 mr-0 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
            </svg>
            <span class="hidden md:inline">Expense Claims</span>
        </a>

        <div class="{{ Design::PAD_Y_LG }} {{ Design::MARGIN_Y_SM }} border-t border-slate-800">
            <p
                class="hidden md:block {{ Design::PAD_X_MD }} {{ Design::MARGIN_Y_SM }} {{ Design::TEXT_XS }} {{ Design::FONT_SEMIBOLD }} text-slate-500 uppercase tracking-wider">
                Self-Service</p>

            <!-- My Profile -->
            <a href="{{ route('employee.profile.show') }}"
                class="flex items-center justify-center md:justify-start md:px-4 md:gap-3 md:py-2.5 px-0 gap-0 py-3 rounded-lg transition {{ request()->routeIs('employee.profile.show') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                <svg class="w-5 h-5 md:mr-3 mr-0 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="hidden md:inline">View Profile</span>
            </a>

            <!-- Announcements -->
            <a href="{{ route('employee.announcements.index') }}"
                class="flex items-center justify-center md:justify-start md:px-4 md:gap-3 md:py-2.5 px-0 gap-0 py-3 rounded-lg transition {{ request()->routeIs('employee.announcements.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                <svg class="w-5 h-5 md:mr-3 mr-0 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
                <span class="hidden md:inline">Announcements</span>
            </a>

            <!-- Weekly Feedback -->
            <a href="{{ route('employee.feedback.dashboard') }}"
                class="flex items-center justify-center md:justify-start md:px-4 md:gap-3 md:py-2.5 px-0 gap-0 py-3 rounded-lg transition {{ request()->routeIs('employee.feedback.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                <svg class="w-5 h-5 md:mr-3 mr-0 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                <span class="hidden md:inline">Weekly Feedback</span>
            </a>

            <!-- Complaint Box -->
            <a href="{{ route('employee.complaints.index') }}"
                class="flex items-center justify-center md:justify-start md:px-4 md:gap-3 md:py-2.5 px-0 gap-0 py-3 rounded-lg transition {{ request()->routeIs('employee.complaints.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                <svg class="w-5 h-5 md:mr-3 mr-0 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <span class="hidden md:inline">Complaint Box</span>
            </a>
        </div>
    </nav>

    <!-- Footer - Logout -->
    <div class="{{ Design::PAD_MD }} border-t border-slate-800">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center md:justify-start md:px-4 md:gap-3 md:py-2.5 px-0 gap-0 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
                <svg class="w-5 h-5 md:mr-3 mr-0 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="hidden md:inline">Logout</span>
            </button>
        </form>
    </div>
</div>