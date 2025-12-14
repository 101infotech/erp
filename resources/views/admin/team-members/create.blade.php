@extends('admin.layouts.app')

@section('title', 'Create Team Member')
@section('page-title', 'Add New Team Member')

@section('content')
<div class="max-w-2xl">
    <div class="bg-slate-800 rounded-lg shadow border border-slate-700 p-6">
        <form action="{{ route('admin.team-members.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="site_id" class="block text-sm font-medium text-gray-700 mb-2">Site</label>
                <select name="site_id" id="site_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 {{ $selectedSiteId ? 'bg-gray-100' : '' }}"
                    {{ $selectedSiteId ? 'readonly onclick="return false;"' : '' }}>
                    <option value="">Select Site</option>
                    @foreach($sites as $site)
                    <option value="{{ $site->id }}" {{ (old('site_id', $selectedSiteId)==$site->id) ? 'selected' : ''
                        }}>
                        {{ $site->name }}
                    </option>
                    @endforeach
                </select>
                @if($selectedSiteId)
                <p class="mt-1 text-xs text-gray-500">Site is pre-selected based on your current workspace</p>
                @endif
            </div>

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                <input type="text" name="position" id="position" value="{{ old('position') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                <textarea name="bio" id="bio" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('bio') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Profile Image</label>
                <input type="file" name="image" id="image" accept="image/*"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">Facebook URL</label>
                <input type="url" name="facebook_url" id="facebook_url" value="{{ old('facebook_url') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-2">Twitter URL</label>
                <input type="url" name="twitter_url" id="twitter_url" value="{{ old('twitter_url') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-2">LinkedIn URL</label>
                <input type="url" name="linkedin_url" id="linkedin_url" value="{{ old('linkedin_url') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-6">
                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
                    Create Member
                </button>
                <a href="{{ route('admin.team-members.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection