<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">My Feedback</h1>
                        <p class="text-slate-400">Track your submitted feedback and responses</p>
                    </div>
                    <a href="{{ route('employee.complaints.create') }}"
                        class="px-6 py-3 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-semibold transition flex items-center gap-2 justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Submit New Feedback
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4 mb-6">
                <div class="flex gap-3">
                    <select name="status"
                        class="flex-1 px-4 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                        <option value="reviewing" {{ request('status')=='reviewing' ? 'selected' : '' }}>Reviewing
                        </option>
                        <option value="resolved" {{ request('status')=='resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="dismissed" {{ request('status')=='dismissed' ? 'selected' : '' }}>Dismissed
                        </option>
                    </select>
                    <button type="submit"
                        class="px-6 py-2 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-medium transition">
                        Filter
                    </button>
                </div>
            </form>

            <!-- Complaints List -->
            <div class="space-y-4">
                @forelse($complaints as $complaint)
                <div
                    class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden hover:border-lime-500/30 transition">
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-white mb-2">{{ $complaint->subject }}</h3>
                                <p class="text-slate-400 text-sm">{{ Str::limit($complaint->description, 150) }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                @php
                                $statusColors = [
                                'pending' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                'reviewing' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                'resolved' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                'dismissed' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                ];
                                @endphp
                                <span
                                    class="px-3 py-1 text-xs font-medium rounded-full border {{ $statusColors[$complaint->status] ?? '' }}">
                                    {{ ucfirst($complaint->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-slate-700">
                            <div class="flex items-center gap-4 text-sm text-slate-400">
                                @if($complaint->category)
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    {{ ucfirst($complaint->category) }}
                                </span>
                                @endif
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $complaint->created_at->format('M d, Y') }}
                                </span>
                            </div>
                            <a href="{{ route('employee.complaints.show', $complaint) }}"
                                class="text-lime-400 hover:text-lime-300 text-sm font-medium transition">
                                View Details →
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-12">
                    <div class="flex flex-col items-center justify-center text-center">
                        <svg class="w-16 h-16 text-slate-600 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-slate-400 text-lg mb-2">No feedback submitted yet</p>
                        <p class="text-slate-500 text-sm mb-6">Share your thoughts and help us improve</p>
                        <a href="{{ route('employee.complaints.create') }}"
                            class="px-6 py-3 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-semibold transition inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Submit Your First Feedback
                        </a>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($complaints->hasPages())
            <div class="mt-6">
                {{ $complaints->links() }}
            </div>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
        class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        {{ session('success') }}
    </div>
    @endif
</x-app-layout>