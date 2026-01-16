<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Confirm Password</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-slate-100 antialiased">
    <div class="min-h-screen flex items-center justify-center bg-slate-950 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <div class="text-center">
                <a href="/" class="inline-block">
                    <x-application-logo class="w-16 h-16 mx-auto fill-current text-primary-500" />
                </a>
                <h2 class="mt-6 text-3xl font-bold text-white">Confirm your password</h2>
                <p class="mt-2 text-sm text-slate-400">This is a secure area</p>
            </div>

            <x-ui.card class="mt-8">
                <x-ui.alert variant="warning" class="mb-6">
                    This is a secure area of the application. Please confirm your password before continuing.
                </x-ui.alert>

                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                    @csrf

                    <x-ui.input id="password" name="password" type="password" label="Password" :required="true"
                        :error="$errors->first('password')" placeholder="••••••••" autocomplete="current-password"
                        autofocus />

                    <x-ui.button type="submit" class="w-full" size="lg">
                        Confirm Password
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