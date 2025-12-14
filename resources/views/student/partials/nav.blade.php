<nav class="bg-slate-900/50 backdrop-blur-sm border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="#" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-lime-400 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-slate-900" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Right Side - Notification & Profile Only -->
            <div class="flex items-center space-x-2">
                <!-- Notification Bell -->
                <x-notification-bell />
                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false"
                        class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-slate-800/50 transition">
                        <span class="text-white font-semibold">Student</span>
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" class="absolute right-0 mt-2 w-48 bg-slate-800 rounded-lg shadow-lg z-50"
                        style="display: none;">
                        <a href="#" class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700">Profile</a>
                        <a href="#" class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>