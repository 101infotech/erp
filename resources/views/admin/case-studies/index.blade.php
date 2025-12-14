@extends('admin.layouts.app')

@section('title', 'Case Studies')
@section('page-title', 'Manage Case Studies')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('workspace.case-studies.create', ['workspace' => request()->segment(1)]) }}"
        class="bg-lime-500 hover:bg-lime-600 text-slate-950 font-medium px-6 py-2 rounded-lg">
        Add Case Study
    </a>
</div>

<div class="bg-slate-800 rounded-lg shadow border border-slate-700 overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-700">
        <thead class="bg-slate-900">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Title</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider hidden md:table-cell">
                    Site</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider hidden lg:table-cell">
                    Client</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider hidden sm:table-cell">
                    Published</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-slate-800 divide-y divide-slate-700">
            @forelse($caseStudies as $caseStudy)
            <tr>
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        @if($caseStudy->featured_image_path)
                        <img src="{{ Storage::url($caseStudy->featured_image_path) }}" alt="{{ $caseStudy->title }}"
                            class="w-16 h-16 rounded object-cover mr-3">
                        @else
                        <div class="w-16 h-16 rounded bg-slate-700 mr-3"></div>
                        @endif
                        <div class="text-sm font-medium text-white">{{ $caseStudy->title }}</div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-slate-300">{{ $caseStudy->client_name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-slate-300">{{ $caseStudy->category }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                    {{ $caseStudy->project_date ? \Carbon\Carbon::parse($caseStudy->project_date)->format('M d, Y') :
                    'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('workspace.case-studies.edit', ['workspace' => request()->segment(1), 'case_study' => $caseStudy]) }}"
                        class="text-lime-400 hover:text-lime-300 mr-3">Edit</a>
                    <form
                        action="{{ route('workspace.case-studies.destroy', ['workspace' => request()->segment(1), 'case_study' => $caseStudy]) }}"
                        method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-slate-400">No case studies found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($caseStudies->hasPages())
<div class="mt-4">
    {{ $caseStudies->links() }}
</div>
@endif
@endsection