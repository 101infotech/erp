<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <!-- 403 Illustration -->
        <div class="mb-8">
            <div class="relative">
                <!-- Large 403 Text -->
                <h1 class="text-[180px] md:text-[240px] font-black text-transparent bg-clip-text bg-gradient-to-br from-red-400 to-red-600 leading-none select-none opacity-20">
                    403
                </h1>
                
                <!-- Icon Overlay -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-32 h-32 md:w-40 md:h-40 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8 space-y-4">
            <h2 class="text-3xl md:text-4xl font-bold text-white">
                Access Forbidden
            </h2>
            <p class="text-lg text-slate-400 max-w-md mx-auto">
                You don't have permission to access this resource. Please contact an administrator if you believe this is an error.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="javascript:history.back()"
                class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition-all duration-200 inline-flex items-center gap-2 group">
                <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Go Back
            </a>
            
            <a href="{{ url('/') }}"
                class="px-6 py-3 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition-all duration-200 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Go Home
            </a>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
        <div class="absolute top-20 left-10 w-72 h-72 bg-red-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-red-600/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    </div>
</body>

</html>
