<nav class="bg-slate-900/50 backdrop-blur-sm border-b border-slate-800 relative z-30">
    @use('App\Constants\Design')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('employee.dashboard') }}" class="{{ Design::GAP_LG }} flex items-center">
                    <div class="w-10 h-10 bg-lime-400 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-slate-900" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Right Side - Notification & Profile Only -->
            <div class="{{ Design::GAP_SM }} flex items-center">
                <!-- Admin Panel Link -->
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}"
                    class="hidden md:flex items-center {{ Design::GAP_SM }} {{ Design::PAD_X_MD }} {{ Design::PAD_Y_MD }} bg-lime-500/10 hover:bg-lime-500/20 text-lime-400 hover:text-lime-300 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="{{ Design::TEXT_SM }} {{ Design::FONT_MEDIUM }}">Admin Panel</span>
                </a>
                @endif

                <!-- Notification Bell -->
                <x-notification-bell />

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false"
                        class="{{ Design::GAP_LG }} flex items-center {{ Design::PAD_X_MD }} {{ Design::PAD_Y_MD }} rounded-lg hover:bg-slate-800/50 transition">
                        @php
                        $employee = Auth::user()->hrmEmployee;
                        $avatarUrl = $employee && $employee->avatar ? asset('storage/' . $employee->avatar) : null;
                        @endphp
                        @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ Auth::user()->name }}"
                            class="w-8 h-8 rounded-full object-cover border-2 border-lime-400 user-avatar-img">
                        @else
                        <div class="w-8 h-8 bg-lime-400 rounded-full flex items-center justify-center">
                            <span class="text-slate-900 font-bold text-sm">{{ substr(Auth::user()->name, 0, 2) }}</span>
                        </div>
                        @endif
                        <div class="hidden md:block text-left">
                            <p class="text-white text-sm font-medium">{{ Auth::user()->name }}</p>
                            <p class="text-slate-400 text-xs">{{ $employee ? $employee->position : 'Employee' }}</p>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" :class="{ 'rotate-180': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Dropdown Menu -->
                    <div x-show="open" x-transition
                        class="absolute right-0 mt-2 w-56 bg-slate-800 rounded-lg shadow-2xl border border-slate-700 py-2 z-50">
                        <a href="{{ route('employee.profile.show') }}"
                            class="flex items-center space-x-3 px-4 py-2 text-slate-300 hover:bg-slate-700 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-sm font-medium">My Profile</span>
                        </a>
                        <div class="border-t border-slate-700 my-2"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            @method('POST')
                            <button type="submit"
                                class="w-full flex items-center space-x-3 px-4 py-2 text-red-400 hover:bg-slate-700 hover:text-red-300 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="text-sm font-medium">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>