<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Notifications</h1>
                        <p class="text-slate-400">Stay updated with all your notifications</p>
                    </div>
                    @if($unreadCount > 0)
                    <form action="{{ route('employee.notifications.mark-all-as-read') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-950 rounded-lg font-medium transition">
                            Mark All as Read ({{ $unreadCount }})
                        </button>
                    </form>
                    @endif
                </div>

                <!-- Notifications List -->
                <div class="space-y-3">
                    @forelse($notifications as $notification)
                    <div
                        class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden hover:border-slate-700 transition {{ !$notification->is_read ? 'ring-2 ring-lime-500/20' : '' }}">
                        <div class="p-5">
                            <div class="flex items-start gap-4">
                                <!-- Icon -->
                                <div class="flex-shrink-0">
                                    @if(str_contains($notification->type, 'leave'))
                                    <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    @elseif(str_contains($notification->type, 'complaint'))
                                    <div
                                        class="w-12 h-12 rounded-full bg-yellow-500/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    @else
                                    <div
                                        class="w-12 h-12 rounded-full bg-slate-600/30 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4 mb-2">
                                        <h3 class="text-white font-semibold text-lg">{{ $notification->title }}</h3>
                                        @if(!$notification->is_read)
                                        <div class="flex-shrink-0">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-lime-500/20 text-lime-400">
                                                New
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    <p class="text-slate-400 text-sm mb-3 leading-relaxed">{{ $notification->message }}
                                    </p>
                                    <div class="flex items-center gap-4">
                                        <span class="text-slate-500 text-xs">{{
                                            $notification->created_at->diffForHumans()
                                            }}</span>
                                        @if($notification->link)
                                        <a href="{{ $notification->link }}"
                                            class="text-lime-400 hover:text-lime-300 text-sm font-medium transition">
                                            View Details →
                                        </a>
                                        @endif
                                        @if(!$notification->is_read)
                                        <form
                                            action="{{ route('employee.notifications.mark-as-read', $notification->id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="text-slate-400 hover:text-white text-sm font-medium transition">
                                                Mark as Read
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-slate-400 text-lg font-medium">No notifications yet</p>
                        <p class="text-slate-500 text-sm mt-2">You're all caught up!</p>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
                @endif
            </div>
        </div>
</x-app-layout>