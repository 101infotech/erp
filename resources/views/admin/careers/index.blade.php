@extends('admin.layouts.app')

@section('title', 'Careers')
@section('page-title', 'Manage Careers')

@section('content')
<!-- Header -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white">Careers</h2>
            <p class="text-slate-400 mt-1">Manage job postings and opportunities</p>
        </div>
        <a href="{{ route('workspace.careers.create', ['workspace' => request()->segment(1)]) }}"
            class="px-4 py-2 bg-lime-500 text-slate-950 font-semibold rounded-lg hover:bg-lime-400 transition flex items-center space-x-2 w-fit">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Job Posting</span>
        </a>
    </div>
</div>

<!-- Desktop Table View -->
<div class="hidden lg:block bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-700">
            <thead class="bg-slate-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Job
                        Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Location
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Deadline
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-slate-800/50 divide-y divide-slate-700">
                @forelse($careers as $career)
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-white">{{ $career->title }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-slate-300">{{ ucfirst($career->job_type) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-slate-300">{{ $career->location }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                        {{ $career->application_deadline ?
                        \Carbon\Carbon::parse($career->application_deadline)->format('M d, Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $career->is_active ? 'bg-lime-500/20 text-lime-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $career->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <a href="{{ route('workspace.careers.edit', ['workspace' => request()->segment(1), 'career' => $career]) }}"
                            class="text-lime-400 hover:text-lime-300">Edit</a>
                        <form
                            action="{{ route('workspace.careers.destroy', ['workspace' => request()->segment(1), 'career' => $career]) }}"
                            method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <p>No job postings found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Card View -->
<div class="lg:hidden space-y-4">
    @forelse($careers as $career)
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-white">{{ $career->title }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ ucfirst($career->job_type) }}</p>
            </div>
            <span
                class="px-2 py-1 text-xs font-semibold rounded-full {{ $career->is_active ? 'bg-lime-500/20 text-lime-400' : 'bg-red-500/20 text-red-400' }}">
                {{ $career->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-2 mb-3 text-sm">
            <div>
                <span class="text-slate-400">Location:</span>
                <span class="text-white ml-1">{{ $career->location }}</span>
            </div>
            <div>
                <span class="text-slate-400">Deadline:</span>
                <span class="text-white ml-1">{{ $career->application_deadline ?
                    \Carbon\Carbon::parse($career->application_deadline)->format('M d, Y') : 'N/A' }}</span>
            </div>
        </div>

        <div class="flex gap-2 pt-3 border-t border-slate-700">
            <a href="{{ route('workspace.careers.edit', ['workspace' => request()->segment(1), 'career' => $career]) }}"
                class="flex-1 px-3 py-2 text-xs text-center bg-lime-500/20 text-lime-400 rounded-lg hover:bg-lime-500/30 transition">Edit</a>
            <form
                action="{{ route('workspace.careers.destroy', ['workspace' => request()->segment(1), 'career' => $career]) }}"
                method="POST" class="flex-1" onsubmit="return confirm('Are you sure?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full px-3 py-2 text-xs bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-8 text-center">
        <svg class="w-12 h-12 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        <p class="text-slate-400">No job postings found</p>
    </div>
    @endforelse
</div>

@if($careers->hasPages())
<div class="mt-4">
    {{ $careers->links() }}
</div>
@endif
@endsection