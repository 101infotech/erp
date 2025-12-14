@extends('admin.layouts.app')

@section('title', 'Blogs')
@section('page-title', 'Manage Blogs')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('workspace.blogs.create', ['workspace' => request()->segment(1)]) }}"
        class="bg-lime-500 hover:bg-lime-600 text-slate-950 font-medium px-6 py-2 rounded-lg">
        Add Blog Post
    </a>
</div>

<div class="bg-slate-800 rounded-lg shadow border border-slate-700 overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-700">
        <thead class="bg-slate-900">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Post</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider hidden lg:table-cell">
                    Author</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider hidden sm:table-cell">
                    Category</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider hidden md:table-cell">
                    Published
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-slate-800 divide-y divide-slate-700">
            @forelse($blogs as $blog)
            <tr>
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        @if($blog->featured_image_path)
                        <img src="{{ Storage::url($blog->featured_image_path) }}" alt="{{ $blog->title }}"
                            class="w-16 h-16 rounded object-cover mr-3">
                        @else
                        <div class="w-16 h-16 rounded bg-slate-700 mr-3"></div>
                        @endif
                        <div>
                            <div class="text-sm font-medium text-white">{{ $blog->title }}</div>
                            <div class="text-xs text-slate-400">{{ Str::limit($blog->excerpt, 50) }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                    <div class="text-sm text-slate-300">{{ $blog->author }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                    <div class="text-sm text-slate-300">{{ $blog->category }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300 hidden md:table-cell">
                    {{ $blog->published_at ? $blog->published_at->format('M d, Y') : 'Not published' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $blog->is_published ? 'bg-lime-500/20 text-lime-400' : 'bg-yellow-500/20 text-yellow-400' }}">
                        {{ $blog->is_published ? 'Published' : 'Draft' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('workspace.blogs.edit', ['workspace' => request()->segment(1), 'blog' => $blog]) }}"
                        class="text-lime-400 hover:text-lime-300 mr-3">Edit</a>
                    <form
                        action="{{ route('workspace.blogs.destroy', ['workspace' => request()->segment(1), 'blog' => $blog]) }}"
                        method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-slate-400">No blog posts found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($blogs->hasPages())
<div class="mt-4">
    {{ $blogs->links() }}
</div>
@endif
@endsection