<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Verify Email</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-slate-100 antialiased">
    <div class="min-h-screen flex items-center justify-center bg-slate-950 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <div class="text-center">
                <a href="/" class="inline-block">
                    <x-application-logo class="w-16 h-16 mx-auto fill-current text-primary-500" />
                </a>
                <h2 class="mt-6 text-3xl font-bold text-white">Verify your email</h2>
                <p class="mt-2 text-sm text-slate-400">We sent you a verification link</p>
            </div>

            <x-ui.card class="mt-8">
                @if (session('status') == 'verification-link-sent')
                <x-ui.alert variant="success" class="mb-6">
                    A new verification link has been sent to your email address.
                </x-ui.alert>
                @endif

                <div class="mb-6 text-sm text-slate-400">
                    Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we'll gladly send you another.
                </div>

                <div class="flex items-center justify-between gap-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <x-ui.button type="submit" variant="primary">
                            Resend Verification Email
                        </x-ui.button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-ui.button type="submit" variant="secondary">
                            Log Out
                        </x-ui.button>
                    </form>
                </div>
            </x-ui.card>

            <p class="text-center text-xs text-slate-500">
                {{ config('app.name') }} © {{ date('Y') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>
