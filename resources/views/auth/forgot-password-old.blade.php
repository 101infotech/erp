<x-guest-layout>
    <div class="mb-4 text-sm text-slate-300">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password
        reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <div class="flex items-center space-x-4">
                <a class="underline text-sm text-slate-400 hover:text-lime-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500"
                    href="{{ route('login') }}">
                    {{ __('Back to login') }}
                </a>

                @if (Route::has('register'))
                <span class="text-slate-600">|</span>
                <a class="underline text-sm text-slate-400 hover:text-lime-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500"
                    href="{{ route('register') }}">
                    {{ __('Create account') }}
                </a>
                @endif
            </div>

            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>