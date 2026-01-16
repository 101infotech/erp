<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Resource Request Details</h1>
                    <p class="text-slate-400">Request #{{ $resourceRequest->id }}</p>
                </div>
                <a href="{{ route('employee.resource-requests.index') }}"
                    class="px-4 py-2 bg-slate-700 text-slate-100 rounded-lg font-semibold hover:bg-slate-600">← Back</a>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Details -->
                <div class="lg:col-span-2 space-y-4">
                    <!-- Item Information -->
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
                        <h2 class="text-lg font-semibold text-white mb-4">Item Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-slate-400">Item Name</label>
                                <p class="text-white font-semibold text-lg">{{ $resourceRequest->item_name }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-slate-400">Category</label>
                                    <p class="text-white font-semibold">{{ ucwords(str_replace('_', ' ',
                                        $resourceRequest->category)) }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-slate-400">Quantity</label>
                                    <p class="text-white font-semibold">{{ $resourceRequest->quantity }}</p>
                                </div>
                            </div>
                            @if($resourceRequest->description)
                            <div>
                                <label class="text-sm text-slate-400">Description</label>
                                <p class="text-slate-200">{{ $resourceRequest->description }}</p>
                            </div>
                            @endif
                            @if($resourceRequest->reason)
                            <div>
                                <label class="text-sm text-slate-400">Reason</label>
                                <p class="text-slate-200">{{ $resourceRequest->reason }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Submission Information -->
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
                        <h2 class="text-lg font-semibold text-white mb-4">Submission Details</h2>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-slate-400">Submitted On</label>
                                    <p class="text-white">{{ $resourceRequest->created_at->format('M d, Y H:i A') }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-slate-400">Last Updated</label>
                                    <p class="text-white">{{ $resourceRequest->updated_at->format('M d, Y H:i A') }}</p>
                                </div>
                            </div>
                            @if($resourceRequest->estimated_cost)
                            <div>
                                <label class="text-sm text-slate-400">Estimated Cost</label>
                                <p class="text-white font-semibold">₹ {{ number_format($resourceRequest->estimated_cost,
                                    2) }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Status -->
                <div class="space-y-4">
                    <!-- Status Card -->
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
                        <h2 class="text-lg font-semibold text-white mb-4">Status</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-slate-400">Current Status</label>
                                <div class="mt-2">
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                                        @if($resourceRequest->status==='approved') bg-blue-500/20 text-blue-300
                                        @elseif($resourceRequest->status==='fulfilled') bg-lime-500/20 text-lime-300
                                        @elseif($resourceRequest->status==='rejected') bg-red-500/20 text-red-300
                                        @else bg-amber-500/20 text-amber-200 @endif">
                                        {{ ucfirst($resourceRequest->status) }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-slate-400">Priority</label>
                                <div class="mt-2">
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                                        @if($resourceRequest->priority==='urgent') bg-red-500/20 text-red-300
                                        @elseif($resourceRequest->priority==='high') bg-orange-500/20 text-orange-300
                                        @elseif($resourceRequest->priority==='medium') bg-blue-500/20 text-blue-300
                                        @else bg-slate-700 text-slate-200 @endif">
                                        {{ ucfirst($resourceRequest->priority) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Timeline -->
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
                        <h2 class="text-lg font-semibold text-white mb-4">Timeline</h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-lime-400 mt-1.5"></div>
                                <div>
                                    <p class="text-slate-300">Request submitted</p>
                                    <p class="text-slate-500 text-xs">{{ $resourceRequest->created_at->format('M d, Y')
                                        }}</p>
                                </div>
                            </div>
                            @if($resourceRequest->status !== 'pending')
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full 
                                    @if($resourceRequest->status==='approved') bg-blue-400
                                    @elseif($resourceRequest->status==='fulfilled') bg-lime-400
                                    @elseif($resourceRequest->status==='rejected') bg-red-400
                                    @else bg-slate-400 @endif mt-1.5"></div>
                                <div>
                                    <p class="text-slate-300">{{ ucfirst($resourceRequest->status) }}</p>
                                    <p class="text-slate-500 text-xs">{{ $resourceRequest->updated_at->format('M d, Y')
                                        }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Comments/Notes (if any) -->
            @if($resourceRequest->admin_notes)
            <div class="bg-blue-500/10 border border-blue-500/40 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-blue-300 mb-3">Admin Notes</h2>
                <p class="text-slate-200">{{ $resourceRequest->admin_notes }}</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>