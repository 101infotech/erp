<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('employee.dashboard') }}"
                    class="inline-flex items-center text-sm text-slate-400 hover:text-lime-400 transition mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Dashboard
                </a>
                <h1 class="text-4xl font-bold text-white mb-2">Feedback Box</h1>
                <p class="text-slate-400">Share your thoughts, concerns, or suggestions with management</p>
            </div>

            <!-- Anonymous Notice -->
            <div
                class="bg-gradient-to-r from-lime-500/10 to-green-500/10 border border-lime-500/30 rounded-lg p-6 mb-8">
                <div class="flex items-start gap-4">
                    <div class="bg-lime-500/20 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-1">Your Privacy Matters</h3>
                        <p class="text-slate-300">Your feedback is submitted confidentially. Management will review your
                            message without knowing who sent it.</p>
                    </div>
                </div>
            </div>

            <!-- Create Complaint Form -->
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-white mb-6">Submit Your Feedback</h2>

                    <form method="POST" action="{{ route('employee.complaints.store') }}" class="space-y-6">
                        @csrf

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Category <span class="text-slate-500">(Optional)</span>
                            </label>
                            <select name="category"
                                class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                                <option value="">Select a category...</option>
                                <option value="workplace">Workplace Environment</option>
                                <option value="management">Management</option>
                                <option value="harassment">Harassment/Discrimination</option>
                                <option value="safety">Safety Concerns</option>
                                <option value="compensation">Compensation/Benefits</option>
                                <option value="workload">Workload</option>
                                <option value="other">Other</option>
                            </select>
                            @error('category')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Subject <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="subject" value="{{ old('subject') }}" required
                                class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500"
                                placeholder="Brief summary of your feedback">
                            @error('subject')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Details <span class="text-red-400">*</span>
                            </label>
                            <textarea name="description" rows="6" required
                                class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500"
                                placeholder="Please provide detailed information about your feedback, concern, or suggestion...">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-3 justify-end pt-4">
                            <a href="{{ route('employee.complaints.index') }}"
                                class="px-6 py-3 bg-slate-700 text-white rounded-lg hover:bg-slate-600 font-medium transition">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-6 py-3 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-semibold transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Guidelines -->
            <div class="mt-8 bg-slate-800/30 border border-slate-700/50 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-slate-300 mb-3">Guidelines for Feedback</h3>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-lime-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Be specific and provide details to help management understand your concern</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-lime-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Remain professional and constructive in your feedback</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-lime-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Include any relevant dates, times, or situations</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-lime-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Your feedback will be reviewed and addressed appropriately</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
        class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        {{ session('success') }}
    </div>
    @endif
</x-app-layout>