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

        <div class="flex-1 @if(request()->is('employee/*') || request()->is('student/*')) ml-64 @endif">
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
    @stack('scripts')
</body>

</html>