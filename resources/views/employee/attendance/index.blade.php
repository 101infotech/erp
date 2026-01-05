<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-white mb-1">My Attendance</h1>
                    <p class="text-sm text-slate-400">Track your attendance and working hours</p>
                </div>
                <a href="{{ route('employee.dashboard') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 text-slate-300 rounded-lg hover:bg-slate-700 text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>

            @if(isset($message))
            <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-yellow-400 text-sm">{{ $message }}</p>
                </div>
            </div>
            @else

            <!-- AI Attendance Insights -->
            @if($aiInsight)
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-xl p-5 mb-6">
                <div class="flex items-start gap-4">
                    <div class="bg-gradient-to-br from-purple-500 to-blue-600 p-2.5 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            <h3 class="text-lg font-semibold text-white">AI Attendance Insights</h3>
                            <span
                                class="text-xs font-semibold px-2.5 py-1 bg-purple-500/20 text-purple-300 rounded-full border border-purple-400/30">
                                Score: {{ round($aiInsight->overall_score) }}%
                            </span>
                        </div>

                        <!-- Scores Grid -->
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700/50">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs text-slate-400">Punctuality</p>
                                </div>
                                <p
                                    class="text-xl font-bold {{ $aiInsight->punctuality_score >= 75 ? 'text-green-400' : ($aiInsight->punctuality_score >= 50 ? 'text-yellow-400' : 'text-red-400') }}">
                                    {{ round($aiInsight->punctuality_score) }}%
                                </p>
                            </div>
                            <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700/50">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs text-slate-400">Regularity</p>
                                </div>
                                <p
                                    class="text-xl font-bold {{ $aiInsight->regularity_score >= 75 ? 'text-green-400' : ($aiInsight->regularity_score >= 50 ? 'text-yellow-400' : 'text-red-400') }}">
                                    {{ round($aiInsight->regularity_score) }}%
                                </p>
                            </div>
                            <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700/50">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    <p class="text-xs text-slate-400">Trend</p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <p class="text-lg font-semibold text-white">{{ $aiInsight->getTrendIcon() }} {{
                                        ucfirst($aiInsight->trend) }}</p>
                                    @if($aiInsight->trend_change != 0)
                                    <p
                                        class="text-xs {{ $aiInsight->trend_change > 0 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $aiInsight->trend_change > 0 ? '+' : '' }}{{ round($aiInsight->trend_change,
                                        1) }}%
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="bg-slate-900/30 rounded-lg p-3 mb-3">
                            <p class="text-slate-300 text-sm leading-relaxed">{{ $aiInsight->ai_summary }}</p>
                        </div>

                        <!-- Suggestions -->
                        <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3 mb-3">
                            <div class="flex items-start gap-2 mb-2">
                                <svg class="w-4 h-4 text-blue-400 mt-0.5 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                                <p class="text-blue-300 font-medium text-sm">Suggestions to Improve:</p>
                            </div>
                            <div class="text-slate-300 text-sm whitespace-pre-line pl-6">{{ $aiInsight->ai_suggestions
                                }}</div>
                        </div>

                        <!-- Metrics -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div class="bg-slate-800/50 rounded-lg p-3 border border-slate-700/50">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    <p class="text-xs text-slate-400">Avg Clock-in</p>
                                </div>
                                <p class="text-base font-bold text-cyan-400">{{ $aiInsight->avg_clock_in_display ?? 'No
                                    data' }}</p>
                            </div>
                            <div class="bg-slate-800/50 rounded-lg p-3 border border-slate-700/50">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs text-slate-400">Late Arrivals</p>
                                </div>
                                <p
                                    class="text-base font-bold {{ $aiInsight->late_arrivals_count > 5 ? 'text-red-400' : ($aiInsight->late_arrivals_count > 0 ? 'text-yellow-400' : 'text-green-400') }}">
                                    {{ $aiInsight->late_arrivals_count }}
                                </p>
                            </div>
                            <div class="bg-slate-800/50 rounded-lg p-3 border border-slate-700/50">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs text-slate-400">Avg Daily Hours</p>
                                </div>
                                <p class="text-base font-bold text-blue-400">{{
                                    number_format($aiInsight->avg_daily_hours ?? 0, 1) }}h</p>
                            </div>
                            <div class="bg-slate-800/50 rounded-lg p-3 border border-slate-700/50">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <p class="text-xs text-slate-400">Absent Days</p>
                                </div>
                                <p
                                    class="text-base font-bold {{ $aiInsight->absent_days_count > 3 ? 'text-red-400' : ($aiInsight->absent_days_count > 0 ? 'text-yellow-400' : 'text-green-400') }}">
                                    {{ $aiInsight->absent_days_count }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mt-3">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-xs text-slate-500">
                                Analysis based on last 30 days • Updated {{ $aiInsight->updated_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-lg p-4 border border-slate-700">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-slate-400 text-xs font-medium">Total Days</p>
                    </div>
                    <p class="text-2xl font-bold text-white">{{ $stats['total_days'] }}</p>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-lg p-4 border border-slate-700">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-slate-400 text-xs font-medium">Present Days</p>
                    </div>
                    <p class="text-2xl font-bold text-lime-400">{{ $stats['present_days'] }}</p>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-lg p-4 border border-slate-700">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-slate-400 text-xs font-medium">Total Hours</p>
                    </div>
                    <p class="text-2xl font-bold text-blue-400">{{ number_format($stats['total_hours'], 1) }}</p>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-lg p-4 border border-slate-700">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <p class="text-slate-400 text-xs font-medium">Avg Hours/Day</p>
                    </div>
                    <p class="text-2xl font-bold text-purple-400">{{ number_format($stats['average_hours'], 1) }}</p>
                </div>
            </div>

            <!-- Date Filter -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-lg p-4 border border-slate-700 mb-6">
                <form method="GET" action="{{ route('employee.attendance.index') }}"
                    class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate ?? '' }}"
                            class="w-full bg-slate-700/50 border-slate-600 text-white rounded-lg text-sm focus:ring-lime-500 focus:border-lime-500 px-3 py-2">
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate ?? '' }}"
                            class="w-full bg-slate-700/50 border-slate-600 text-white rounded-lg text-sm focus:ring-lime-500 focus:border-lime-500 px-3 py-2">
                    </div>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-lime-500 text-slate-900 rounded-lg font-medium hover:bg-lime-400 text-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </button>
                </form>
            </div>

            <!-- Attendance Table -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-lg border border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-700/50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Date
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Day
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Tracked Hours
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Payroll Hours
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Overtime
                                </th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            @forelse($attendances as $attendance)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-white font-medium">
                                    {{ $attendance->date->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-300">
                                    {{ $attendance->date->format('l') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-300 text-right">
                                    {{ number_format($attendance->tracked_hours, 2) }}h
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-lime-400 text-right font-semibold">
                                    {{ number_format($attendance->payroll_hours, 2) }}h
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                                    @if($attendance->overtime_hours > 0)
                                    <span class="text-blue-400 font-semibold">{{
                                        number_format($attendance->overtime_hours, 2) }}h</span>
                                    @else
                                    <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    @if($attendance->payroll_hours >= 8)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Full Day
                                    </span>
                                    @elseif($attendance->payroll_hours > 0)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Partial
                                    </span>
                                    @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-500/20 text-red-400 rounded-full text-xs font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Absent
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center">
                                    <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-slate-400 text-sm">No attendance records found for the selected
                                        period</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>