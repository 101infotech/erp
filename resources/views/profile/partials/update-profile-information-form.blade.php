<section>
    @php use Illuminate\Support\Facades\Storage; @endphp
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Profile Picture Section -->
        <div class="space-y-3">
            <h3 class="text-sm font-semibold text-white">{{ __('Profile Picture') }}</h3>
            <div class="flex items-center gap-6">
                <!-- Profile Picture Preview -->
                <div class="relative">
                    @if($user->profile_picture && Storage::exists($user->profile_picture))
                    <img src="{{ Storage::url($user->profile_picture) }}" alt="{{ $user->name }}"
                        class="w-24 h-24 rounded-lg object-cover border-2 border-lime-500/30">
                    @else
                    <div
                        class="w-24 h-24 rounded-lg bg-gradient-to-br from-lime-500 to-lime-600 flex items-center justify-center border-2 border-lime-500/30">
                        <svg class="w-12 h-12 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    @endif
                </div>

                <!-- Upload Input -->
                <div class="flex-1">
                    <label for="profile_picture" class="block">
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden"
                            x-on:change="document.getElementById('file-name').textContent = $el.files[0]?.name || 'No file chosen'" />
                        <button type="button"
                            class="inline-flex items-center px-4 py-2 bg-lime-600 hover:bg-lime-700 text-white text-sm font-medium rounded-lg transition cursor-pointer"
                            onclick="document.getElementById('profile_picture').click()">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ __('Upload Picture') }}
                        </button>
                        <p class="text-xs text-gray-400 mt-2">{{ __('Max. 2MB • JPG, PNG, GIF') }}</p>
                    </label>
                    <p id="file-name" class="text-sm text-gray-300 mt-2">{{ __('No file chosen') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
                </div>
            </div>
        </div>

        <hr class="border-slate-700">

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text"
                class="mt-1 block w-full bg-slate-700 border-slate-600 text-white placeholder-gray-400"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1 block w-full bg-slate-700 border-slate-600 text-white placeholder-gray-400"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-400">
                    {{ __('Your email address is unverified.') }}

                    <button form="send-verification"
                        class="underline text-sm text-lime-400 hover:text-lime-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                <p class="mt-2 font-medium text-sm text-lime-400">
                    {{ __('A new verification link has been sent to your email address.') }}
                </p>
                @endif
            </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-lime-600 hover:bg-lime-700 text-slate-950 text-sm font-semibold rounded-lg transition">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-lime-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>