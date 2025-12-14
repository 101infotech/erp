@extends('admin.layouts.app')

@section('title', 'Complaint Box')
@section('page-title', 'Complaint Box')

@section('content')
<!-- Header -->
<div class="mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
        <div>
            <h2 class="text-2xl font-bold text-white">Staff Feedback</h2>
            <p class="text-slate-400 mt-1">Review anonymous feedback from staff members</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search complaints..."
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">

            <select name="status"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                <option value="reviewing" {{ request('status')=='reviewing' ? 'selected' : '' }}>Reviewing</option>
                <option value="resolved" {{ request('status')=='resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="dismissed" {{ request('status')=='dismissed' ? 'selected' : '' }}>Dismissed</option>
            </select>

            <select name="priority"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                <option value="">All Priorities</option>
                <option value="high" {{ request('priority')=='high' ? 'selected' : '' }}>High</option>
                <option value="medium" {{ request('priority')=='medium' ? 'selected' : '' }}>Medium</option>
                <option value="low" {{ request('priority')=='low' ? 'selected' : '' }}>Low</option>
            </select>

            <select name="category"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                <option value="{{ $category }}" {{ request('category')==$category ? 'selected' : '' }}>
                    {{ ucfirst($category) }}
                </option>
                @endforeach
            </select>

            <button type="submit"
                class="px-4 py-2 text-sm bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-medium transition">
                Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-amber-500/20 to-amber-600/20 border border-amber-500/30 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white text-sm font-medium">Pending</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $complaints->where('status', 'pending')->count() }}</p>
            </div>
            <div class="bg-amber-500/20 p-3 rounded-lg">
                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-blue-500/20 to-blue-600/20 border border-blue-500/30 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-200 text-sm font-medium">Reviewing</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $complaints->where('status', 'reviewing')->count() }}
                </p>
            </div>
            <div class="bg-blue-500/20 p-3 rounded-lg">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-500/20 to-green-600/20 border border-green-500/30 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-200 text-sm font-medium">Resolved</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $complaints->where('status', 'resolved')->count() }}
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

    <div class="bg-gradient-to-br from-red-500/20 to-red-600/20 border border-red-500/30 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white text-sm font-medium">High Priority</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $complaints->where('priority', 'high')->count() }}</p>
            </div>
            <div class="bg-red-500/20 p-3 rounded-lg">
                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Complaints Table -->
<div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-700">
            <thead class="bg-slate-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Subject
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Category
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Priority
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                        Submitted</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-slate-800/50 divide-y divide-slate-700">
                @forelse($complaints as $complaint)
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-white">{{ Str::limit($complaint->subject, 50) }}</div>
                        <div class="text-xs text-slate-400 mt-1">{{ Str::limit($complaint->description, 80) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($complaint->category)
                        <span class="px-2 py-1 text-xs font-medium bg-slate-700 text-slate-300 rounded-full">
                            {{ ucfirst($complaint->category) }}
                        </span>
                        @else
                        <span class="text-slate-500 text-xs">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                        $statusColors = [
                        'pending' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                        'reviewing' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                        'resolved' => 'bg-green-500/20 text-green-300 border-green-500/30',
                        'dismissed' => 'bg-red-500/20 text-red-300 border-red-500/30',
                        ];
                        @endphp
                        <span
                            class="px-2 py-1 text-xs font-medium rounded-full border {{ $statusColors[$complaint->status] ?? '' }}">
                            {{ ucfirst($complaint->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                        $priorityColors = [
                        'high' => 'bg-red-500/20 text-red-300 border-red-500/30',
                        'medium' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                        'low' => 'bg-green-500/20 text-green-300 border-green-500/30',
                        ];
                        @endphp
                        <span
                            class="px-2 py-1 text-xs font-medium rounded-full border {{ $priorityColors[$complaint->priority] ?? '' }}">
                            {{ ucfirst($complaint->priority) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                        {{ $complaint->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.complaints.show', $complaint) }}"
                            class="text-lime-400 hover:text-lime-300 transition">
                            View Details
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-slate-600 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-slate-400 text-sm">No complaints found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($complaints->hasPages())
    <div class="px-6 py-4 border-t border-slate-700">
        {{ $complaints->links() }}
    </div>
    @endif
</div>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
    class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
    {{ session('success') }}
</div>
@endif
@endsection