<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <!-- 500 Illustration -->
        <div class="mb-8">
            <div class="relative">
                <!-- Large 500 Text -->
                <h1 class="text-[180px] md:text-[240px] font-black text-transparent bg-clip-text bg-gradient-to-br from-orange-400 to-orange-600 leading-none select-none opacity-20">
                    500
                </h1>
                
                <!-- Icon Overlay -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-32 h-32 md:w-40 md:h-40 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8 space-y-4">
            <h2 class="text-3xl md:text-4xl font-bold text-white">
                Server Error
            </h2>
            <p class="text-lg text-slate-400 max-w-md mx-auto">
                Oops! Something went wrong on our end. We're working to fix the issue. Please try again later.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <button onclick="location.reload()"
                class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition-all duration-200 inline-flex items-center gap-2 group">
                <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh Page
            </button>
            
            <a href="{{ url('/') }}"
                class="px-6 py-3 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition-all duration-200 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Go Home
            </a>
        </div>

        <!-- Error Details (only in debug mode) -->
        @if(config('app.debug') && isset($exception))
        <div class="mt-12 text-left">
            <details class="bg-slate-800/50 border border-slate-700 rounded-lg p-4">
                <summary class="cursor-pointer text-orange-400 font-semibold mb-2">Error Details (Debug Mode)</summary>
                <div class="mt-4 space-y-2">
                    <p class="text-sm text-slate-300"><span class="font-semibold text-white">Message:</span> {{ $exception->getMessage() }}</p>
                    <p class="text-sm text-slate-300"><span class="font-semibold text-white">File:</span> {{ $exception->getFile() }}</p>
                    <p class="text-sm text-slate-300"><span class="font-semibold text-white">Line:</span> {{ $exception->getLine() }}</p>
                </div>
            </details>
        </div>
        @endif
    </div>

    <!-- Decorative Elements -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
        <div class="absolute top-20 left-10 w-72 h-72 bg-orange-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-orange-600/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    </div>
</body>

</html>
