<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Register</title>

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
                <h2 class="mt-6 text-3xl font-bold text-white">Create your account</h2>
                <p class="mt-2 text-sm text-slate-400">Join our team to get started</p>
            </div>

            <!-- Registration Card -->
            <x-ui.card class="mt-8">
                <!-- Info Alert -->
                <x-ui.alert variant="info" class="mb-6">
                    <strong class="block mb-1">Employee Registration</strong>
                    <span class="text-xs">Only employees registered in our HRM system can create accounts. Your email must match the one in our records.</span>
                </x-ui.alert>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <x-ui.input
                        id="name"
                        name="name"
                        type="text"
                        label="Full Name"
                        :value="old('name')"
                        :required="true"
                        :error="$errors->first('name')"
                        placeholder="John Doe"
                        autofocus
                        autocomplete="name"
                    />

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
                        autocomplete="username"
                        help="Must match your company email"
                    />

                    <!-- Password -->
                    <x-ui.input
                        id="password"
                        name="password"
                        type="password"
                        label="Password"
                        :required="true"
                        :error="$errors->first('password')"
                        placeholder="••••••••"
                        autocomplete="new-password"
                        help="Minimum 8 characters"
                    />

                    <!-- Confirm Password -->
                    <x-ui.input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        label="Confirm Password"
                        :required="true"
                        :error="$errors->first('password_confirmation')"
                        placeholder="••••••••"
                        autocomplete="new-password"
                    />

                    <!-- Submit Button -->
                    <x-ui.button type="submit" class="w-full" size="lg">
                        Create Account
                    </x-ui.button>

                    <!-- Link to Login -->
                    <div class="text-center text-sm">
                        <span class="text-slate-400">Already have an account?</span>
                        <a href="{{ route('login') }}" class="ml-1 text-primary-400 hover:text-primary-300 transition-colors font-medium">
                            Sign in
                        </a>
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
