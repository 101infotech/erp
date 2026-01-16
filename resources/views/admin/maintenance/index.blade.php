@extends('admin.layouts.app')

@section('title', 'System Maintenance Mode')

@section('content')
<div x-data="maintenanceMode()" x-init="init()">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">System Maintenance Mode</h1>
            <p class="text-slate-600 mt-2">Control system access during maintenance periods</p>
        </div>

        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center"
                        :class="status.enabled ? 'bg-yellow-100' : 'bg-green-100'">
                        <svg class="w-6 h-6" :class="status.enabled ? 'text-yellow-600' : 'text-green-600'" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">Current Status</h2>
                        <p class="text-sm" :class="status.enabled ? 'text-yellow-600' : 'text-green-600'">
                            <span x-text="status.enabled ? 'Maintenance Mode Active' : 'System Online'"></span>
                        </p>
                    </div>
                </div>

                <!-- Toggle Button -->
                <button @click="toggleMaintenance()" :disabled="loading"
                    class="px-6 py-2.5 rounded-lg font-medium transition-colors" :class="status.enabled
                            ? 'bg-green-600 hover:bg-green-700 text-white'
                            : 'bg-yellow-600 hover:bg-yellow-700 text-white'">
                    <span x-show="!loading"
                        x-text="status.enabled ? 'Disable Maintenance' : 'Enable Maintenance'"></span>
                    <span x-show="loading">Processing...</span>
                </button>
            </div>

            <!-- Maintenance Message -->
            <div x-show="status.enabled" class="mt-6 pt-6 border-t border-slate-200">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Maintenance Message
                </label>
                <textarea x-model="message" rows="3"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Enter message to display to users..."></textarea>
                <button @click="updateMessage()" :disabled="loading || !message"
                    class="mt-3 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    Update Message
                </button>
            </div>
        </div>

        <!-- Information Card -->
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-6">
            <div class="flex gap-3">
                <svg class="w-6 h-6 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="font-semibold text-indigo-900 mb-2">How It Works</h3>
                    <ul class="space-y-2 text-sm text-indigo-800">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span><strong>Admins & Super Admins</strong> can always access the system during
                                maintenance</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span><strong>Whitelisted users</strong> (configured in config/maintenance.php) can bypass
                                maintenance</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span><strong>All other users</strong> will see a maintenance page with your custom
                                message</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span><strong>Testing:</strong> You can test staff features as an admin without
                                interruption</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Configuration Info -->
        <div class="mt-6 bg-slate-50 border border-slate-200 rounded-xl p-6">
            <h3 class="font-semibold text-slate-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Configuration
            </h3>
            <p class="text-sm text-slate-600 mb-2">
                To whitelist specific user IDs, edit <code
                    class="px-2 py-0.5 bg-slate-200 rounded text-xs font-mono">config/maintenance.php</code>:
            </p>
            <pre class="bg-slate-800 text-slate-100 p-4 rounded-lg text-xs overflow-x-auto"><code>'allowed_user_ids' => [
    1,  // User ID 1 can bypass maintenance
    5,  // User ID 5 can bypass maintenance
],</code></pre>
        </div>

        <!-- Alert Messages -->
        <div x-show="alert.show" x-transition class="fixed top-4 right-4 max-w-sm z-50"
            :class="alert.type === 'success' ? 'bg-green-100 border-green-400 text-green-800' : 'bg-red-100 border-red-400 text-red-800'"
            class="border-l-4 p-4 rounded shadow-lg">
            <div class="flex items-center justify-between">
                <p class="font-medium" x-text="alert.message"></p>
                <button @click="alert.show = false" class="ml-4 text-current hover:opacity-75">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function maintenanceMode() {
        return {
            status: {
                enabled: false,
                message: '',
            },
            message: '',
            loading: false,
            alert: {
                show: false,
                type: 'success',
                message: '',
            },

            async init() {
                await this.fetchStatus();
            },

            async fetchStatus() {
                try {
                    const response = await fetch('/api/maintenance/status');
                    const data = await response.json();
                    this.status = data;
                    this.message = data.message || '';
                } catch (error) {
                    console.error('Failed to fetch status:', error);
                }
            },

            async toggleMaintenance() {
                this.loading = true;
                const endpoint = this.status.enabled ? '/api/maintenance/disable' : '/api/maintenance/enable';

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            message: this.message,
                        }),
                    });

                    const data = await response.json();

                    if (response.ok) {
                        this.showAlert('success', data.message);
                        await this.fetchStatus();
                    } else {
                        this.showAlert('error', data.message || 'Failed to toggle maintenance mode');
                    }
                } catch (error) {
                    this.showAlert('error', 'An error occurred');
                    console.error('Error:', error);
                } finally {
                    this.loading = false;
                }
            },

            async updateMessage() {
                this.loading = true;

                try {
                    const response = await fetch('/api/maintenance/message', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            message: this.message,
                        }),
                    });

                    const data = await response.json();

                    if (response.ok) {
                        this.showAlert('success', 'Message updated successfully');
                        await this.fetchStatus();
                    } else {
                        this.showAlert('error', data.message || 'Failed to update message');
                    }
                } catch (error) {
                    this.showAlert('error', 'An error occurred');
                    console.error('Error:', error);
                } finally {
                    this.loading = false;
                }
            },

            showAlert(type, message) {
                this.alert = {
                    show: true,
                    type,
                    message,
                };

                setTimeout(() => {
                    this.alert.show = false;
                }, 5000);
            },
        };
    }
</script>
@endpush