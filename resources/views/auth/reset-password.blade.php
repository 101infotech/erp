<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Reset Password</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-slate-100 antialiased">
    <div class="min-h-screen flex items-center justify-center bg-slate-950 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <div class="text-center">
                <a href="/" class="inline-block">
                    <x-application-logo class="w-16 h-16 mx-auto fill-current text-primary-500" />
                </a>
                <h2 class="mt-6 text-3xl font-bold text-white">Set new password</h2>
                <p class="mt-2 text-sm text-slate-400">Enter your new password below</p>
            </div>

            <x-ui.card class="mt-8">
                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <x-ui.input
                        id="email"
                        name="email"
                        type="email"
                        label="Email Address"
                        :value="old('email', $request->email)"
                        :required="true"
                        :error="$errors->first('email')"
                        readonly
                        autocomplete="username"
                    />

                    <x-ui.input
                        id="password"
                        name="password"
                        type="password"
                        label="New Password"
                        :required="true"
                        :error="$errors->first('password')"
                        placeholder="••••••••"
                        autocomplete="new-password"
                        help="Minimum 8 characters"
                        autofocus
                    />

                    <x-ui.input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        label="Confirm New Password"
                        :required="true"
                        :error="$errors->first('password_confirmation')"
                        placeholder="••••••••"
                        autocomplete="new-password"
                    />

                    <x-ui.button type="submit" class="w-full" size="lg">
                        Reset Password
                    </x-ui.button>
                </form>
            </x-ui.card>

            <p class="text-center text-xs text-slate-500">
                {{ config('app.name') }} © {{ date('Y') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>
