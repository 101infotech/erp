@extends('admin.layouts.app')

@section('title', 'Announcements')
@section('page-title', 'Announcements')

@section('content')
<!-- Header -->
<div class="mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
        <div>
            <h2 class="text-2xl font-bold text-white">Company Announcements</h2>
            <p class="text-slate-400 mt-1">Manage and send announcements to staff members</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}"
            class="px-4 py-2 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-semibold inline-flex items-center gap-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Announcement
        </a>
    </div>
</div>

@if(session('success'))
<div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4 mb-6">
    <p class="text-green-400">{{ session('success') }}</p>
</div>
@endif

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-500/20 to-blue-600/20 border border-blue-500/30 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-200 text-sm font-medium">Total Announcements</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $announcements->total() }}</p>
            </div>
            <div class="bg-blue-500/20 p-3 rounded-lg">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-500/20 to-green-600/20 border border-green-500/30 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-200 text-sm font-medium">Published</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $announcements->where('is_published', true)->count() }}
                </p>
            </div>
            <div class="bg-green-500/20 p-3 rounded-lg">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-amber-500/20 to-amber-600/20 border border-amber-500/30 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-amber-200 text-sm font-medium">High Priority</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $announcements->where('priority', 'high')->count() }}
                </p>
            </div>
            <div class="bg-amber-500/20 p-3 rounded-lg">
                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Announcements List -->
<div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-700/50 border-b border-slate-600">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Title
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Priority
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                        Recipients</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Created
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @forelse($announcements as $announcement)
                <tr class="hover:bg-slate-700/30 transition">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-white font-medium">{{ $announcement->title }}</span>
                            <span class="text-slate-400 text-sm mt-1">{{ Str::limit($announcement->content, 50)
                                }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                        $priorityColors = [
                        'low' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                        'normal' => 'bg-green-500/20 text-green-400 border-green-500/30',
                        'high' => 'bg-red-500/20 text-red-400 border-red-500/30',
                        ];
                        @endphp
                        <span
                            class="px-2 py-1 text-xs font-semibold rounded-full border {{ $priorityColors[$announcement->priority] ?? '' }}">
                            {{ ucfirst($announcement->priority) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-slate-300 text-sm">{{ $announcement->getRecipientsList() }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($announcement->is_published)
                        <span
                            class="px-2 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-400 border border-green-500/30">
                            Published
                        </span>
                        @else
                        <span
                            class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-500/20 text-slate-400 border border-slate-500/30">
                            Draft
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-white text-sm">{{ $announcement->created_at->format('M d, Y') }}</span>
                            <span class="text-slate-400 text-xs">{{ $announcement->creator->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.announcements.show', $announcement) }}"
                                class="px-3 py-1.5 bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/30 text-sm font-medium transition">
                                View
                            </a>
                            <a href="{{ route('admin.announcements.edit', $announcement) }}"
                                class="px-3 py-1.5 bg-amber-500/20 text-amber-400 rounded-lg hover:bg-amber-500/30 text-sm font-medium transition">
                                Edit
                            </a>
                            <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Are you sure you want to delete this announcement?')"
                                    class="px-3 py-1.5 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 text-sm font-medium transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                        <p class="text-slate-400 text-lg font-medium">No announcements yet</p>
                        <p class="text-slate-500 text-sm mt-1">Create your first announcement to get started</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($announcements->hasPages())
    <div class="px-6 py-4 border-t border-slate-700">
        {{ $announcements->links() }}
    </div>
    @endif
</div>

@endsection