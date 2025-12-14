@extends('admin.layouts.app')

@section('title', 'Edit Case Study')
@section('page-title', 'Edit Case Study')

@section('content')
<div class="max-w-3xl">
    <div class="bg-slate-800 rounded-lg shadow border border-slate-700 p-6">
        <form action="{{ route('admin.case-studies.update', $caseStudy) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="site_id" class="block text-sm font-medium text-gray-700 mb-2">Site</label>
                <select name="site_id" id="site_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Select Site</option>
                    @foreach($sites as $site)
                    <option value="{{ $site->id }}" {{ old('site_id', $caseStudy->site_id) == $site->id ? 'selected' :
                        '' }}>
                        {{ $site->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Project Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $caseStudy->title) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $caseStudy->slug) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="6" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('description', $caseStudy->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="client_name" class="block text-sm font-medium text-gray-700 mb-2">Client Name</label>
                <input type="text" name="client_name" id="client_name"
                    value="{{ old('client_name', $caseStudy->client_name) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-4">
                <label for="project_date" class="block text-sm font-medium text-gray-700 mb-2">Project Date</label>
                <input type="date" name="project_date" id="project_date"
                    value="{{ old('project_date', $caseStudy->project_date) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            @if($caseStudy->featured_image_path)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Featured Image</label>
                <img src="{{ Storage::url($caseStudy->featured_image_path) }}" alt="{{ $caseStudy->title }}"
                    class="w-48 h-32 rounded-lg object-cover">
            </div>
            @endif

            <div class="mb-4">
                <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">{{
                    $caseStudy->featured_image_path ? 'Replace Featured Image' : 'Featured Image' }}</label>
                <input type="file" name="featured_image" id="featured_image" accept="image/*"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            @if($caseStudy->gallery && count($caseStudy->gallery) > 0)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Gallery Images</label>
                <div class="grid grid-cols-4 gap-2">
                    @foreach($caseStudy->gallery as $image)
                    <img src="{{ Storage::url($image) }}" alt="Gallery" class="w-full h-24 rounded object-cover">
                    @endforeach
                </div>
            </div>
            @endif

            <div class="mb-4">
                <label for="gallery" class="block text-sm font-medium text-gray-700 mb-2">Add More Gallery
                    Images</label>
                <input type="file" name="gallery[]" id="gallery" accept="image/*" multiple
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                <p class="text-xs text-gray-500 mt-1">New images will be added to existing gallery</p>
            </div>

            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <input type="text" name="category" id="category" value="{{ old('category', $caseStudy->category) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-6">
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                <input type="text" name="tags" id="tags"
                    value="{{ old('tags', is_array($caseStudy->tags) ? implode(', ', $caseStudy->tags) : '') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                <p class="text-xs text-gray-500 mt-1">Comma-separated tags</p>
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
                    Update Case Study
                </button>
                <a href="{{ route('admin.case-studies.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection