<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('styles')
</head>

<body class="font-sans antialiased bg-slate-950">
    <div class="min-h-screen bg-slate-950 flex">
        <!-- Sidebar for Employee Panel -->
        @if(request()->is('employee/*'))
        @include('employee.partials.sidebar')
        @elseif(request()->is('student/*'))
        @include('student.partials.sidebar')
        @endif

        <div class="flex-1 @if(request()->is('employee/*') || request()->is('student/*')) ml-20 md:ml-64 @endif">
            <!-- Employee/Student Navigation Bar -->
            @if(request()->is('employee/*'))
            @include('employee.partials.nav')
            @elseif(request()->is('student/*'))
            @include('student.partials.nav')
            @endif

            <!-- Page Heading -->
            @isset($header)
            <header class="bg-slate-900 shadow-lg border-b border-slate-800">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="text-white">
                        {{ $header }}
                    </div>
                </div>
            </header>
            @endisset

            <!-- Page Content -->
            <main class="bg-slate-950">
                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>
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