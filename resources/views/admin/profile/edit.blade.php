@extends('admin.layouts.app')

@section('page-title', 'My Profile')

@section('content')
<div class="space-y-6">
    <!-- Profile Information Card -->
    <div
        class="bg-white dark:bg-slate-800 rounded-lg shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
                    {{ __('Profile Information') }}
                </h2>
                <p class="text-slate-600 dark:text-slate-400">
                    {{ __("Update your account's profile information and email address.") }}
                </p>
            </div>

            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <form method="post" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('patch')

                <!-- Profile Picture Section -->
                <div class="space-y-3 pb-6 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ __('Profile Picture') }}</h3>
                    <div class="flex items-center gap-6">
                        <!-- Profile Picture Preview -->
                        <div class="relative">
                            @php use Illuminate\Support\Facades\Storage; @endphp
                            @if($user->profile_picture && Storage::exists($user->profile_picture))
                            <img src="{{ Storage::url($user->profile_picture) }}" alt="{{ $user->name }}"
                                class="w-32 h-32 rounded-lg object-cover border-2 border-lime-500/30 shadow">
                            @else
                            <div
                                class="w-32 h-32 rounded-lg bg-gradient-to-br from-lime-500 to-lime-600 flex items-center justify-center border-2 border-lime-500/30 shadow">
                                <svg class="w-16 h-16 text-slate-950" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            @endif
                        </div>

                        <!-- Upload Input -->
                        <div class="flex-1">
                            <label for="profile_picture" class="block">
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*"
                                    class="hidden"
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
                                <p class="text-xs text-slate-600 dark:text-slate-400 mt-2">{{ __('Max. 2MB • JPG, PNG,
                                    GIF') }}</p>
                            </label>
                            <p id="file-name" class="text-sm text-slate-700 dark:text-slate-300 mt-2">{{ __('No file
                                chosen') }}</p>
                            @if ($errors->has('profile_picture'))
                            <div class="text-sm text-red-600 dark:text-red-400 mt-2">
                                @foreach ($errors->get('profile_picture') as $message)
                                <p>{{ $message }}</p>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-900 dark:text-white mb-2">
                        {{ __('Name') }}
                    </label>
                    <input id="name" name="name" type="text"
                        class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-lime-600 focus:border-transparent"
                        value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                    @if ($errors->has('name'))
                    <div class="text-sm text-red-600 dark:text-red-400 mt-2">
                        @foreach ($errors->get('name') as $message)
                        <p>{{ $message }}</p>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-900 dark:text-white mb-2">
                        {{ __('Email') }}
                    </label>
                    <input id="email" name="email" type="email"
                        class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-lime-600 focus:border-transparent"
                        value="{{ old('email', $user->email) }}" required autocomplete="username" />
                    @if ($errors->has('email'))
                    <div class="text-sm text-red-600 dark:text-red-400 mt-2">
                        @foreach ($errors->get('email') as $message)
                        <p>{{ $message }}</p>
                        @endforeach
                    </div>
                    @endif

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-4">
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification"
                                class="text-lime-600 dark:text-lime-400 hover:text-lime-700 dark:hover:text-lime-300 underline">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-lime-600 dark:text-lime-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Save Button -->
                <div class="flex items-center gap-4 pt-4">
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-lime-600 hover:bg-lime-700 text-white text-sm font-semibold rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        {{ __('Save Changes') }}
                    </button>

                    @if (session('status') === 'profile-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                        class="text-sm text-lime-600 dark:text-lime-400 font-medium">
                        <svg class="inline w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('Profile updated successfully!') }}
                    </p>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Update Password Card -->
    <div
        class="bg-white dark:bg-slate-800 rounded-lg shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
                    {{ __('Update Password') }}
                </h2>
                <p class="text-slate-600 dark:text-slate-400">
                    {{ __("Ensure your account is using a long, random password to stay secure.") }}
                </p>
            </div>

            @include('profile.partials.update-password-form')
        </div>
    </div>

    <!-- Delete Account Card -->
    <div
        class="bg-white dark:bg-slate-800 rounded-lg shadow border border-red-200 dark:border-red-900/30 overflow-hidden">
        <div class="px-6 py-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
                    {{ __('Delete Account') }}
                </h2>
                <p class="text-slate-600 dark:text-slate-400">
                    {{ __("Once your account is deleted, there is no going back. Please be certain.") }}
                </p>
            </div>

            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection