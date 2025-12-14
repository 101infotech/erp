@extends('admin.layouts.app')

@section('title', 'Complaint Details')
@section('page-title', 'Complaint Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.complaints.index') }}"
        class="inline-flex items-center text-sm text-slate-400 hover:text-lime-400 transition mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Complaints
    </a>

    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
        <!-- Header Section -->
        <div class="p-6 border-b border-slate-700">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-white">{{ $complaint->subject }}</h2>
                    <div class="flex items-center gap-3 mt-3">
                        @php
                        $statusColors = [
                        'pending' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                        'reviewing' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                        'resolved' => 'bg-green-500/20 text-green-300 border-green-500/30',
                        'dismissed' => 'bg-red-500/20 text-red-300 border-red-500/30',
                        ];
                        $priorityColors = [
                        'high' => 'bg-red-500/20 text-red-300 border-red-500/30',
                        'medium' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                        'low' => 'bg-green-500/20 text-green-300 border-green-500/30',
                        ];
                        @endphp
                        <span
                            class="px-3 py-1 text-xs font-medium rounded-full border {{ $statusColors[$complaint->status] ?? '' }}">
                            {{ ucfirst($complaint->status) }}
                        </span>
                        <span
                            class="px-3 py-1 text-xs font-medium rounded-full border {{ $priorityColors[$complaint->priority] ?? '' }}">
                            {{ ucfirst($complaint->priority) }} Priority
                        </span>
                        @if($complaint->category)
                        <span class="px-3 py-1 text-xs font-medium bg-slate-700 text-slate-300 rounded-full">
                            {{ ucfirst($complaint->category) }}
                        </span>
                        @endif
                    </div>
                </div>
                <button onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                    class="px-3 py-2 text-sm bg-red-500/20 text-red-400 border border-red-500/30 rounded-lg hover:bg-red-500/30 transition">
                    Delete
                </button>
            </div>
        </div>

        <!-- Content Section -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Description -->
                    <div>
                        <h3 class="text-sm font-semibold text-slate-300 mb-3">Description</h3>
                        <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                            <p class="text-slate-300 whitespace-pre-wrap">{{ $complaint->description }}</p>
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    <div>
                        <h3 class="text-sm font-semibold text-slate-300 mb-3">Admin Notes</h3>
                        <form method="POST" action="{{ route('admin.complaints.add-notes', $complaint) }}"
                            class="space-y-3">
                            @csrf
                            <textarea name="admin_notes" rows="4"
                                class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500"
                                placeholder="Add internal notes here...">{{ $complaint->admin_notes }}</textarea>
                            <button type="submit"
                                class="px-4 py-2 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-medium transition">
                                Save Notes
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Metadata (subtle submitter info) -->
                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                        <h3 class="text-sm font-semibold text-slate-300 mb-3">Information</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-slate-400">Submitted:</span>
                                <span class="text-white ml-2">{{ $complaint->created_at->format('M d, Y h:i A')
                                    }}</span>
                            </div>
                            @if($complaint->resolved_at)
                            <div>
                                <span class="text-slate-400">Resolved:</span>
                                <span class="text-white ml-2">{{ $complaint->resolved_at->format('M d, Y h:i A')
                                    }}</span>
                            </div>
                            @endif
                            <div>
                                <span class="text-slate-400">ID:</span>
                                <span class="text-white ml-2">#{{ $complaint->id }}</span>
                            </div>
                            <!-- Subtle submitter info - displayed in small text -->
                            <div class="pt-3 border-t border-slate-700/50" x-data="{ show: false }">
                                <button @click="show = !show" type="button"
                                    class="text-xs text-slate-500 hover:text-slate-400 transition flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-text="show ? 'Hide metadata' : 'Show metadata'"></span>
                                </button>
                                <div x-show="show" x-transition class="mt-2 text-xs text-slate-500">
                                    <span>From:</span>
                                    <span class="ml-1">{{ $complaint->user->name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Status -->
                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                        <h3 class="text-sm font-semibold text-slate-300 mb-3">Update Status</h3>
                        <form method="POST" action="{{ route('admin.complaints.update-status', $complaint) }}"
                            class="space-y-3">
                            @csrf
                            @method('PATCH')
                            <select name="status"
                                class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500">
                                <option value="pending" {{ $complaint->status == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="reviewing" {{ $complaint->status == 'reviewing' ? 'selected' : ''
                                    }}>Reviewing</option>
                                <option value="resolved" {{ $complaint->status == 'resolved' ? 'selected' : ''
                                    }}>Resolved</option>
                                <option value="dismissed" {{ $complaint->status == 'dismissed' ? 'selected' : ''
                                    }}>Dismissed</option>
                            </select>
                            <button type="submit"
                                class="w-full px-4 py-2 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-medium transition">
                                Update Status
                            </button>
                        </form>
                    </div>

                    <!-- Update Priority -->
                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                        <h3 class="text-sm font-semibold text-slate-300 mb-3">Update Priority</h3>
                        <form method="POST" action="{{ route('admin.complaints.update-priority', $complaint) }}"
                            class="space-y-3">
                            @csrf
                            @method('PATCH')
                            <select name="priority"
                                class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500">
                                <option value="low" {{ $complaint->priority == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $complaint->priority == 'medium' ? 'selected' : '' }}>Medium
                                </option>
                                <option value="high" {{ $complaint->priority == 'high' ? 'selected' : '' }}>High
                                </option>
                            </select>
                            <button type="submit"
                                class="w-full px-4 py-2 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-medium transition">
                                Update Priority
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal"
    class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-slate-800 rounded-lg p-6 max-w-md w-full border border-slate-700">
        <h3 class="text-lg font-bold text-white mb-2">Delete Complaint</h3>
        <p class="text-slate-400 mb-6">Are you sure you want to delete this complaint? This action cannot be undone.</p>
        <div class="flex gap-3 justify-end">
            <button onclick="document.getElementById('deleteModal').classList.add('hidden')"
                class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition">
                Cancel
            </button>
            <form method="POST" action="{{ route('admin.complaints.destroy', $complaint) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
    class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
    {{ session('success') }}
</div>
@endif
@endsection