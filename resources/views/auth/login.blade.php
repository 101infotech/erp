<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-slate-700 bg-slate-800 text-lime-500 shadow-sm focus:ring-lime-500"
                    name="remember">
                <span class="ms-2 text-sm text-slate-300">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div class="flex items-center space-x-4">
                @if (Route::has('password.request'))
                <a class="underline text-sm text-slate-400 hover:text-lime-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
                @endif

                @if (Route::has('register'))
                <span class="text-slate-600">|</span>
                <a class="underline text-sm text-slate-400 hover:text-lime-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500"
                    href="{{ route('register') }}">
                    {{ __('Create account') }}
                </a>
                @endif
            </div>

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>