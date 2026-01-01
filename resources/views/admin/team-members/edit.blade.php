@extends('admin.layouts.app')

@section('title', 'Edit Team Member')
@section('page-title', 'Edit Team Member')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.team-members.index') }}" class="group">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-white">Edit Team Member</h2>
    </div>

    <div class="bg-slate-800 rounded-lg shadow border border-slate-700 p-6">
        <form action="{{ route('admin.team-members.update', $teamMember) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="site_id" class="block text-sm font-medium text-gray-700 mb-2">Site</label>
                <select name="site_id" id="site_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Select Site</option>
                    @foreach($sites as $site)
                    <option value="{{ $site->id }}" {{ old('site_id', $teamMember->site_id) == $site->id ? 'selected' :
                        '' }}>
                        {{ $site->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $teamMember->name) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                <input type="text" name="position" id="position" value="{{ old('position', $teamMember->position) }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                <textarea name="bio" id="bio" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('bio', $teamMember->bio) }}</textarea>
            </div>

            @if($teamMember->image_path)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                <img src="{{ Storage::url($teamMember->image_path) }}" alt="{{ $teamMember->name }}"
                    class="w-32 h-32 rounded-lg object-cover">
            </div>
            @endif

            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">{{ $teamMember->image_path ?
                    'Replace Image' : 'Profile Image' }}</label>
                <input type="file" name="image" id="image" accept="image/*"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">Facebook URL</label>
                <input type="url" name="facebook_url" id="facebook_url"
                    value="{{ old('facebook_url', $teamMember->facebook_url) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-2">Twitter URL</label>
                <input type="url" name="twitter_url" id="twitter_url"
                    value="{{ old('twitter_url', $teamMember->twitter_url) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-2">LinkedIn URL</label>
                <input type="url" name="linkedin_url" id="linkedin_url"
                    value="{{ old('linkedin_url', $teamMember->linkedin_url) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-6">
                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                <input type="number" name="order" id="order" value="{{ old('order', $teamMember->order) }}" min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
                    Update Member
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