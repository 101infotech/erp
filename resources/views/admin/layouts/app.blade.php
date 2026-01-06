<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Saubhagya Group</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script>
        (function() {
            try {
                const saved = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const useDark = saved ? saved === 'dark' : prefersDark;
                document.documentElement.classList.toggle('dark', useDark);
                document.documentElement.dataset.theme = useDark ? 'dark' : 'light';
            } catch (e) { /* no-op */ }
        })();
        window.toggleTheme = function() {
            const isDark = document.documentElement.classList.contains('dark');
            const next = isDark ? 'light' : 'dark';
            document.documentElement.classList.toggle('dark', next === 'dark');
            document.documentElement.dataset.theme = next;
            try { localStorage.setItem('theme', next); } catch (e) { /* no-op */ }
            document.dispatchEvent(new CustomEvent('themechange', { detail: { theme: next } }));
        };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('styles')
</head>

<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100">
    <!-- Navigation Section (Sidebar with Icon Bar) - Fixed, Always on Top -->
    @include('admin.layouts.partials.sidebar')

    <!-- Main Content Wrapper with proper margin -->
    <div style="margin-left: 80px; min-height: 100vh;">
        <!-- Header -->
        <header
            class="bg-white/90 dark:bg-slate-900 border-b border-l border-slate-800 sticky top-0 z-30">
            <div class="flex justify-between items-center px-8 py-4">
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">@yield('page-title')</h2>
                <div class="flex items-center space-x-4">
                    <!-- Theme Toggle -->
                    <button
                        x-data="{t: document.documentElement.dataset.theme || (document.documentElement.classList.contains('dark') ? 'dark' : 'light')}"
                        @themechange.window="t = $event.detail.theme" @click="toggleTheme()"
                        class="inline-flex items-center justify-center px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        aria-label="Toggle theme">
                        <svg x-show="t === 'dark'" class="w-5 h-5 text-amber-400" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path
                                d="M10 15a5 5 0 110-10 5 5 0 010 10zm0 5a1 1 0 001-1v-1a1 1 0 10-2 0v1a1 1 0 001 1zm0-18a1 1 0 00-1 1v1a1 1 0 102 0V2a1 1 0 00-1-1zm9 9a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM3 10a1 1 0 00-1-1H1a1 1 0 100 2h1a1 1 0 001-1zm13.657 6.657a1 1 0 01-1.414 0l-.707-.707a1 1 0 111.414-1.414l.707.707a1 1 0 010 1.414zM5.464 5.464A1 1 0 104.05 6.878l.707.707A1 1 0 106.17 6.17l-.707-.707zM15.657 3.05a1 1 0 10-1.414 1.414l.707.707A1 1 0 1016.364 3.757l-.707-.707zM3.05 15.657a1 1 0 001.414 0l.707-.707a1 1 0 10-1.414-1.414l-.707.707a1 1 0 000 1.414z" />
                        </svg>
                        <svg x-show="t === 'light'" class="w-5 h-5 text-slate-700" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                    </button>
                    <!-- Nepali Date Display -->
                    <div
                        class="flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800/50 rounded-lg border border-lime-600/30 dark:border-primary-500/30">
                        <svg class="w-5 h-5 text-lime-600 dark:text-lime-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Nepali Date</span>
                            <span class="text-sm font-semibold text-lime-700 dark:text-lime-300">{{ nepali_date()
                                }}</span>
                        </div>
                    </div>

                    <button
                        class="p-2 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    <x-notification-bell />
                    <!-- Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="inline-flex items-center justify-center w-10 h-10 rounded-full border-2 border-slate-200 dark:border-slate-600 hover:border-primary-500 dark:hover:border-primary-500 transition focus:outline-none focus:ring-2 focus:ring-lime-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                            @php use Illuminate\Support\Facades\Storage; @endphp
                            @if(Auth::user()->profile_picture && Storage::exists(Auth::user()->profile_picture))
                            <img src="{{ Storage::url(Auth::user()->profile_picture) }}" alt="{{ Auth::user()->name }}"
                                class="w-full h-full rounded-full object-cover">
                            @else
                            <div
                                class="w-full h-full rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                <span class="text-sm font-semibold text-white">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </span>
                            </div>
                            @endif
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 z-50 mt-2 w-56 rounded-lg shadow-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 py-1">
                            <div
                                class="px-4 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full border-2 border-primary-500/30 flex-shrink-0">
                                    @if(Auth::user()->profile_picture &&
                                    Storage::exists(Auth::user()->profile_picture))
                                    <img src="{{ Storage::url(Auth::user()->profile_picture) }}"
                                        alt="{{ Auth::user()->name }}" class="w-full h-full rounded-full object-cover">
                                    @else
                                    <div
                                        class="w-full h-full rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                        <span class="text-lg font-semibold text-white">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{
                                        Auth::user()->name }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.profile.edit') }}"
                                class="flex items-center px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                    </path>
                                </svg>
                                {{ __('Profile') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border-t border-slate-200 dark:border-slate-700">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-8 flex-1 bg-slate-50 dark:bg-slate-950">
            @if(session('success'))
            <div
                class="mb-6 bg-lime-100 dark:bg-lime-900/20 border border-lime-300 dark:border-lime-700 text-lime-800 dark:text-lime-300 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div
                class="mb-6 bg-red-100 dark:bg-red-900/20 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // Global Modal Functions
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent background scroll
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto'; // Re-enable scroll
            }
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('[id*="Modal"]:not(.hidden), [id*="modal"]:not(.hidden)');
                modals.forEach(modal => {
                    modal.classList.add('hidden');
                });
                document.body.style.overflow = 'auto';
            }
        });

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('fixed') && (event.target.id.includes('Modal') || event.target.id.includes('modal'))) {
                event.target.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });
    </script>

    @stack('scripts')
</body>

</html>