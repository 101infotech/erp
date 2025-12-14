<div class="relative" x-data="notificationBell">
    <!-- Notification Bell Button: navigate to full notifications page on click -->
    <button @click="goToAll()" @keydown.enter="goToAll()" type="button"
        class="relative p-2 rounded-lg hover:bg-slate-800 transition-colors">
        <svg class="w-5 h-5 text-slate-400 hover:text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>
        <!-- Unread Badge -->
        <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount"
            class="absolute top-0 right-0 bg-red-500 text-white text-xs font-bold rounded-full h-5 min-w-[1.25rem] px-1 flex items-center justify-center">
        </span>
    </button>

    <!-- Dropdown (removed - click redirects to full page) -->
    <div x-show="false" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-3 w-[40rem] bg-slate-800 rounded-xl shadow-2xl border border-slate-700 overflow-hidden z-50"
        style="display: none;">

        <!-- Header -->
        <div class="px-5 py-4 bg-slate-900/50 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-white font-semibold text-lg">Notifications</h3>
            <button @click="markAllAsRead" x-show="unreadCount > 0"
                class="text-xs text-lime-400 hover:text-lime-300 font-medium">
                Mark all read
            </button>
        </div>

        <!-- Notifications List -->
        <div class="max-h-[32rem] overflow-y-auto scrollbar-hide">
            <style>
                .scrollbar-hide::-webkit-scrollbar {
                    display: none;
                }

                .scrollbar-hide {
                    -ms-overflow-style: none;
                    scrollbar-width: none;
                }
            </style>
            <template x-if="notifications.length === 0">
                <div class="px-6 py-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                        </path>
                    </svg>
                    <p class="text-slate-400 font-medium">No notifications yet</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div :class="{'bg-slate-700/50': !notification.is_read, 'bg-transparent': notification.is_read}"
                    class="px-5 py-4 border-b border-slate-700/50 hover:bg-slate-700/30 transition-colors cursor-pointer">
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <template x-if="notification.type && notification.type.includes('leave')">
                                <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                            </template>
                            <template x-if="notification.type && notification.type.includes('complaint')">
                                <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                </div>
                            </template>
                            <template
                                x-if="!notification.type || (!notification.type.includes('leave') && !notification.type.includes('complaint'))">
                                <div class="w-10 h-10 rounded-full bg-slate-600/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                        </path>
                                    </svg>
                                </div>
                            </template>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <a :href="notification.link || '#'"
                                @click="if(notification.link) { markAsRead(notification.id); } else { $event.preventDefault(); }"
                                class="block group">
                                <p class="text-white font-semibold text-sm mb-1 group-hover:text-lime-400 transition"
                                    x-text="notification.title"></p>
                                <p class="text-slate-400 text-xs leading-relaxed" x-text="notification.message"></p>
                                <p class="text-slate-500 text-xs mt-2" x-text="formatDate(notification.created_at)"></p>
                            </a>
                        </div>

                        <!-- Unread indicator -->
                        <div class="flex-shrink-0 mt-1">
                            <template x-if="!notification.is_read">
                                <div class="w-2.5 h-2.5 bg-lime-500 rounded-full"></div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div x-show="notifications.length > 0" class="px-5 py-3 bg-slate-900/30 border-t border-slate-700 text-center">
            <a href="{{ route('notifications.all') }}"
                class="text-sm text-lime-400 hover:text-lime-300 font-medium">View all notifications</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
    Alpine.data('notificationBell', () => ({
        isOpen: false,
        notifications: [],
        unreadCount: 0,
        pollInterval: null,

        init() {
            // Only load unread count for the bell (we navigate to the full page on click)
            this.loadUnreadCount();
            this.startPolling();
            this.hoverTimeout = null;
        },

        // Navigate to the full notifications page (server route)
        goToAll() {
            window.location.href = '{{ route('notifications.all') }}';
        },

        // Hover handlers removed - click redirects to full notifications page

        async loadNotifications() {
            try {
                const response = await fetch('/notifications');
                const data = await response.json();
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            } catch (error) {
                console.error('Failed to load notifications:', error);
            }
        },

        async loadUnreadCount() {
            try {
                const response = await fetch('/notifications/unread-count');
                const data = await response.json();
                this.unreadCount = data.count;
            } catch (error) {
                console.error('Failed to load unread count:', error);
            }
        },

        async markAsRead(id) {
            // Mark as read in background, don't wait for response
            fetch(`/notifications/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                // Update local state
                const notification = this.notifications.find(n => n.id === id);
                if (notification && !notification.is_read) {
                    notification.is_read = true;
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            }).catch(error => {
                console.error('Failed to mark as read:', error);
            });
        },

        async markAllAsRead() {
            try {
                await fetch('/notifications/mark-all-as-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                await this.loadNotifications();
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        },

        startPolling() {
            // Poll for new notifications every 30 seconds
            this.pollInterval = setInterval(() => {
                if (!this.isOpen) {
                    this.loadUnreadCount();
                }
            }, 30000);
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInMinutes = Math.floor((now - date) / 60000);

            if (diffInMinutes < 1) return 'Just now';
            if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
            
            const diffInHours = Math.floor(diffInMinutes / 60);
            if (diffInHours < 24) return `${diffInHours}h ago`;
            
            const diffInDays = Math.floor(diffInHours / 24);
            if (diffInDays < 7) return `${diffInDays}d ago`;
            
            return date.toLocaleDateString();
        },

        destroy() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
            }
        }
    }));
});
</script>