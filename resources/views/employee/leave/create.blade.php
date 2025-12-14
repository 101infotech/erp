<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-4xl font-bold text-white">Request Leave</h1>
                <a href="{{ route('employee.leave.index') }}"
                    class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600">
                    ← Back
                </a>
            </div>

            <!-- Leave Balance Summary -->
            <div class="bg-gradient-to-r from-lime-500/10 to-lime-600/10 border border-lime-500/30 rounded-xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-4">Your Leave Balance</h3>
                <div class="grid grid-cols-3 gap-4">
                    @foreach(['sick' => 'Sick', 'casual' => 'Casual', 'annual' => 'Annual'] as $type => $label)
                    <div class="text-center">
                        <p class="text-slate-400 text-sm mb-1">{{ $label }}</p>
                        <p class="text-2xl font-bold text-lime-400">{{ $stats[$type]['available'] ?? 0 }}</p>
                        <p class="text-xs text-slate-500">days available</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Leave Request Form -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700 p-8">
                <form method="POST" action="{{ route('employee.leave.store') }}">
                    @csrf

                    <!-- Leave Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Leave Type <span
                                class="text-red-400">*</span></label>
                        <select name="leave_type" required
                            class="w-full bg-slate-700 border-slate-600 text-white rounded-lg focus:ring-lime-500 focus:border-lime-500 p-3">
                            <option value="">Select leave type</option>
                            <option value="sick" {{ old('leave_type')==='sick' ? 'selected' : '' }}>Sick Leave ({{
                                $stats['sick']['available'] ?? 0 }} available)</option>
                            <option value="casual" {{ old('leave_type')==='casual' ? 'selected' : '' }}>Casual Leave ({{
                                $stats['casual']['available'] ?? 0 }} available)</option>
                            <option value="annual" {{ old('leave_type')==='annual' ? 'selected' : '' }}>Annual Leave ({{
                                $stats['annual']['available'] ?? 0 }} available)</option>
                            @if(isset($stats['period']))
                            <option value="period" {{ old('leave_type')==='period' ? 'selected' : '' }}>Period Leave ({{
                                $stats['period']['available'] ?? 0 }} available)</option>
                            @endif
                            <option value="unpaid" {{ old('leave_type')==='unpaid' ? 'selected' : '' }}>Unpaid Leave
                            </option>
                        </select>
                        @error('leave_type')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Start Date <span
                                    class="text-red-400">*</span></label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" required
                                class="w-full bg-slate-700 border-slate-600 text-white focus:ring-lime-500 focus:border-lime-500 p-3 rounded-lg">
                            @error('start_date')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">End Date <span
                                    class="text-red-400">*</span></label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" required
                                class="w-full bg-slate-700 border-slate-600 text-white focus:ring-lime-500 focus:border-lime-500 p-3 rounded-lg">
                            @error('end_date')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Reason <span
                                class="text-red-400">*</span></label>
                        <textarea name="reason" rows="4" required
                            class="w-full bg-slate-700 border-slate-600 text-white rounded-lg focus:ring-lime-500 focus:border-lime-500 p-3"
                            placeholder="Please provide a reason for your leave request...">{{ old('reason') }}</textarea>
                        @error('reason')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-slate-400 text-xs mt-1">Maximum 1000 characters</p>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm text-blue-300">
                                <p class="font-semibold mb-1">Important Information:</p>
                                <ul class="list-disc list-inside space-y-1 text-blue-200">
                                    <li>Leave requests must be submitted at least 24 hours in advance</li>
                                    <li>Your manager will review and approve/reject the request</li>
                                    <li>You'll be notified once a decision is made</li>
                                    <li>Approved leave will be deducted from your balance</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center gap-4">
                        <button type="submit"
                            class="flex-1 px-6 py-3 bg-lime-500 text-slate-900 rounded-lg font-semibold hover:bg-lime-400 transition">
                            Submit Leave Request
                        </button>
                        <a href="{{ route('employee.leave.index') }}"
                            class="px-6 py-3 bg-slate-600 text-white rounded-lg font-semibold hover:bg-slate-500 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>