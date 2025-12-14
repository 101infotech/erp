@extends('admin.layouts.app')

@section('title', 'Edit Site')
@section('page-title', 'Edit Site')

@section('content')
<!-- Header -->
<div class="mb-8">
    <div class="flex items-center gap-4 mb-4">
        <a href="{{ route('admin.sites.index') }}"
            class="flex items-center text-slate-400 hover:text-lime-400 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Sites
        </a>
    </div>
    <h2 class="text-3xl font-bold text-white">Edit Site</h2>
    <p class="text-slate-400 mt-2">Update site information and settings</p>
</div>

<div class="max-w-2xl">
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-xl p-8">
        <form action="{{ route('admin.sites.update', $site) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Site Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $site->name) }}" required
                    class="w-full px-4 py-3 bg-slate-900/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:border-transparent transition">
                @error('name')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="slug" class="block text-sm font-medium text-slate-300 mb-2">Slug</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $site->slug) }}" required
                    class="w-full px-4 py-3 bg-slate-900/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:border-transparent transition">
                <p class="text-xs text-slate-500 mt-2">Used in URLs and API calls. Should be lowercase with hyphens.</p>
                @error('slug')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="domain" class="block text-sm font-medium text-slate-300 mb-2">Domain</label>
                <input type="text" name="domain" id="domain" value="{{ old('domain', $site->domain) }}"
                    placeholder="example.com"
                    class="w-full px-4 py-3 bg-slate-900/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:border-transparent transition">
                @error('domain')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-slate-300 mb-2">Description</label>
                <textarea name="description" id="description" rows="4"
                    placeholder="Brief description of this website..."
                    class="w-full px-4 py-3 bg-slate-900/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:border-transparent transition resize-none">{{ old('description', $site->description) }}</textarea>
                @error('description')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $site->is_active) ? 'checked'
                    : '' }}
                    class="rounded border-slate-600 bg-slate-900/50 text-lime-500 focus:ring-lime-500
                    focus:ring-offset-slate-800 transition">
                    <span class="ml-3 text-sm font-medium text-slate-300">Active</span>
                </label>
            </div>

            <div class="flex gap-3 pt-4 border-t border-slate-700">
                <button type="submit"
                    class="px-6 py-3 bg-lime-500 hover:bg-lime-400 text-slate-950 font-semibold rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Site
                </button>
                <a href="{{ route('admin.sites.index') }}"
                    class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection