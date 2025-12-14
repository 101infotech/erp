@extends('admin.layouts.app')

@section('title', 'Create Job Posting')
@section('page-title', 'Add Job Posting')

@section('content')
<div class="max-w-3xl">
    <div class="bg-slate-800 rounded-lg shadow border border-slate-700 p-6">
        <form action="{{ route('admin.careers.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="site_id" class="block text-sm font-medium text-gray-700 mb-2">Site</label>
                <select name="site_id" id="site_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Select Site</option>
                    @foreach($sites as $site)
                    <option value="{{ $site->id }}" {{ old('site_id')==$site->id ? 'selected' : '' }}>
                        {{ $site->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('description') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                <textarea name="requirements" id="requirements" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('requirements') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="responsibilities"
                    class="block text-sm font-medium text-gray-700 mb-2">Responsibilities</label>
                <textarea name="responsibilities" id="responsibilities" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('responsibilities') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="job_type" class="block text-sm font-medium text-gray-700 mb-2">Job Type</label>
                <select name="job_type" id="job_type" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Select Type</option>
                    <option value="full-time" {{ old('job_type')=='full-time' ? 'selected' : '' }}>Full-time</option>
                    <option value="part-time" {{ old('job_type')=='part-time' ? 'selected' : '' }}>Part-time</option>
                    <option value="contract" {{ old('job_type')=='contract' ? 'selected' : '' }}>Contract</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <input type="text" name="location" id="location" value="{{ old('location') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="application_deadline" class="block text-sm font-medium text-gray-700 mb-2">Application
                    Deadline</label>
                <input type="date" name="application_deadline" id="application_deadline"
                    value="{{ old('application_deadline') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
                    Create Job Posting
                </button>
                <a href="{{ route('admin.careers.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection