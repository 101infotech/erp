@extends('admin.layouts.app')

@section('title', 'Weekly Employee Feedback')
@section('page-title', 'Weekly Employee Feedback')

@section('content')
<!-- Header -->
<div class="mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
        <div>
            <h2 class="text-2xl font-bold text-white">Weekly Employee Feedback</h2>
            <p class="text-slate-400 mt-1">Review weekly feelings, progress, and self-improvement insights from your
                team</p>
        </div>
        <a href="{{ route('admin.feedback.analytics') }}"
            class="px-4 py-2 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-medium transition flex items-center gap-2 justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            View Analytics
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employee name..."
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">

            <select name="status"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                <option value="">All Status</option>
                <option value="submitted" {{ request('status')=='submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
            </select>

            <input type="date" name="week" value="{{ request('week') }}"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">

            <button type="submit"
                class="px-4 py-2 text-sm bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-medium transition">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-500/20 to-blue-600/20 border border-blue-500/30 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-200 text-sm font-medium">Total Employees</p>
                <p class="text-3xl font-bold text-white mt-1">{{ $totalEmployees }}</p>
            </div>
            <div class="bg-blue-500/20 p-3 rounded-lg">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-500/20 to-green-600/20 border border-green-500/30 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-200 text-sm font-medium">Submitted This Week</p>
                <p class="text-3xl font-bold text-white mt-1">{{ $currentWeekSubmitted }}</p>
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
                <p class="text-white text-sm font-medium">Pending</p>
                <p class="text-3xl font-bold text-white mt-1">{{ $currentWeekPending }}</p>
            </div>
            <div class="bg-amber-500/20 p-3 rounded-lg">
                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Feedback List -->
<div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-700">
            <thead class="bg-slate-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Employee
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                        Submitted</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-slate-800/50 divide-y divide-slate-700">
                @forelse($feedbacks as $feedback)
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0">
                                <div
                                    class="h-10 w-10 rounded-full bg-gradient-to-br from-lime-500 to-lime-600 flex items-center justify-center">
                                    <span class="text-slate-900 font-bold text-sm">{{
                                        strtoupper(substr($feedback->user->name, 0, 1)) }}</span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-white">{{ $feedback->user->name }}</p>
                                <p class="text-xs text-slate-400">{{ $feedback->user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($feedback->is_submitted)
                        <span
                            class="px-3 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-300 border border-green-500/30">
                            Submitted
                        </span>
                        @else
                        <span
                            class="px-3 py-1 text-xs font-medium rounded-full bg-amber-500/20 text-white border border-amber-500/30">
                            Pending
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                        @if($feedback->submitted_at)
                        {{ $feedback->submitted_at->format('M d, Y h:i A') }}
                        @else
                        <span class="text-slate-500">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if($feedback->is_submitted)
                        <a href="{{ route('admin.feedback.show', $feedback) }}"
                            class="text-lime-400 hover:text-lime-300 transition">
                            View
                        </a>
                        @else
                        <span class="text-slate-500">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-slate-600 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-slate-400 text-sm">No feedback found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($feedbacks->hasPages())
    <div class="px-6 py-4 border-t border-slate-700">
        {{ $feedbacks->links() }}
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