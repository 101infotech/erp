<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Reset Password</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-slate-100 antialiased">
    <div class="min-h-screen flex items-center justify-center bg-slate-950 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <!-- Logo -->
            <div class="text-center">
                <a href="/" class="inline-block">
                    <x-application-logo class="w-16 h-16 mx-auto fill-current text-primary-500" />
                </a>
                <h2 class="mt-6 text-3xl font-bold text-white">Reset your password</h2>
                <p class="mt-2 text-sm text-slate-400">We'll send you a password reset link</p>
            </div>

            <!-- Forgot Password Card -->
            <x-ui.card class="mt-8">
                <!-- Session Status -->
                @if (session('status'))
                <x-ui.alert variant="success" class="mb-6">
                    {{ session('status') }}
                </x-ui.alert>
                @endif

                <div class="mb-6 text-sm text-slate-400">
                    Forgot your password? No problem. Just let us know your email address and we'll send you a password reset link.
                </div>

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <x-ui.input
                        id="email"
                        name="email"
                        type="email"
                        label="Email Address"
                        :value="old('email')"
                        :required="true"
                        :error="$errors->first('email')"
                        placeholder="you@example.com"
                        autofocus
                    />

                    <!-- Submit Button -->
                    <x-ui.button type="submit" class="w-full" size="lg">
                        Email Password Reset Link
                    </x-ui.button>

                    <!-- Links -->
                    <div class="flex items-center justify-center gap-4 text-sm">
                        <a href="{{ route('login') }}" class="text-primary-400 hover:text-primary-300 transition-colors">
                            Back to login
                        </a>

                        @if (Route::has('register'))
                        <span class="text-slate-600">•</span>
                        <a href="{{ route('register') }}" class="text-primary-400 hover:text-primary-300 transition-colors">
                            Create account
                        </a>
                        @endif
                    </div>
                </form>
            </x-ui.card>

            <!-- Footer -->
            <p class="text-center text-xs text-slate-500">
                {{ config('app.name') }} © {{ date('Y') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>
