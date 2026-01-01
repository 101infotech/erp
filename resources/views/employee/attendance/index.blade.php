<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">My Attendance</h1>
                    <p class="text-slate-400">Track your attendance and working hours</p>
                </div>
                <a href="{{ route('employee.dashboard') }}"
                    class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600">
                    ← Back to Dashboard
                </a>
            </div>

            @if(isset($message))
            <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-6">
                <p class="text-yellow-400">{{ $message }}</p>
            </div>
            @else

            <!-- AI Attendance Insights -->
            @if($aiInsight)
            <div
                class="bg-gradient-to-br from-purple-500/10 via-blue-500/10 to-cyan-500/10 border border-purple-500/30 rounded-2xl p-6 mb-8 shadow-lg shadow-purple-500/5">
                <div class="flex items-start gap-4">
                    <div class="bg-gradient-to-br from-purple-500 to-blue-600 p-3 rounded-xl flex-shrink-0 shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-3 flex-wrap">
                            <span class="bg-gradient-to-r from-purple-400 to-blue-400 bg-clip-text text-transparent">AI
                                Attendance Insights</span>
                            <span
                                class="text-sm font-semibold px-3 py-1 bg-gradient-to-r from-purple-500/30 to-blue-500/30 text-white rounded-full border border-purple-400/30">
                                Overall Score: {{ round($aiInsight->overall_score) }}%
                            </span>
                        </h3>

                        <!-- Scores Grid -->
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700/50">
                                <p class="text-xs text-slate-400 mb-1">Punctuality</p>
                                <p
                                    class="text-2xl font-bold {{ $aiInsight->punctuality_score >= 75 ? 'text-green-400' : ($aiInsight->punctuality_score >= 50 ? 'text-yellow-400' : 'text-red-400') }}">
                                    {{ round($aiInsight->punctuality_score) }}%
                                </p>
                            </div>
                            <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700/50">
                                <p class="text-xs text-slate-400 mb-1">Regularity</p>
                                <p
                                    class="text-2xl font-bold {{ $aiInsight->regularity_score >= 75 ? 'text-green-400' : ($aiInsight->regularity_score >= 50 ? 'text-yellow-400' : 'text-red-400') }}">
                                    {{ round($aiInsight->regularity_score) }}%
                                </p>
                            </div>
                            <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700/50">
                                <p class="text-xs text-slate-400 mb-1">Trend</p>
                                <p class="text-lg font-bold text-white">
                                    {{ $aiInsight->getTrendIcon() }} {{ ucfirst($aiInsight->trend) }}
                                </p>
                                @if($aiInsight->trend_change != 0)
                                <p
                                    class="text-xs {{ $aiInsight->trend_change > 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $aiInsight->trend_change > 0 ? '+' : '' }}{{ round($aiInsight->trend_change, 1)
                                    }}%
                                </p>
                                @endif
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="bg-slate-900/30 rounded-lg p-4 mb-3">
                            <p class="text-slate-300 text-sm leading-relaxed">{{ $aiInsight->ai_summary }}</p>
                        </div>

                        <!-- Suggestions -->
                        <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4">
                            <p class="text-blue-300 font-semibold text-sm mb-2">💡 Suggestions to Improve:</p>
                            <div class="text-slate-300 text-sm whitespace-pre-line">{{ $aiInsight->ai_suggestions }}
                            </div>
                        </div>

                        <!-- Metrics -->
                        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div class="bg-slate-800/50 rounded-lg p-3 text-center border border-slate-700/50">
                                <p class="text-xs text-slate-400 mb-1 font-medium">Avg Clock-in</p>
                                <p class="text-lg font-bold text-cyan-400">{{ $aiInsight->avg_clock_in_display ?? 'No
                                    data' }}</p>
                            </div>
                            <div class="bg-slate-800/50 rounded-lg p-3 text-center border border-slate-700/50">
                                <p class="text-xs text-slate-400 mb-1 font-medium">Late Arrivals</p>
                                <p
                                    class="text-lg font-bold {{ $aiInsight->late_arrivals_count > 5 ? 'text-red-400' : ($aiInsight->late_arrivals_count > 0 ? 'text-yellow-400' : 'text-green-400') }}">
                                    {{ $aiInsight->late_arrivals_count }}
                                </p>
                            </div>
                            <div class="bg-slate-800/50 rounded-lg p-3 text-center border border-slate-700/50">
                                <p class="text-xs text-slate-400 mb-1 font-medium">Avg Daily Hours</p>
                                <p class="text-lg font-bold text-blue-400">{{ number_format($aiInsight->avg_daily_hours
                                    ?? 0, 1) }}h</p>
                            </div>
                            <div class="bg-slate-800/50 rounded-lg p-3 text-center border border-slate-700/50">
                                <p class="text-xs text-slate-400 mb-1 font-medium">Absent Days</p>
                                <p
                                    class="text-lg font-bold {{ $aiInsight->absent_days_count > 3 ? 'text-red-400' : ($aiInsight->absent_days_count > 0 ? 'text-yellow-400' : 'text-green-400') }}">
                                    {{ $aiInsight->absent_days_count }}
                                </p>
                            </div>
                        </div>

                        <p class="text-xs text-slate-500 mt-3">
                            📊 Analysis based on last 30 days • Updated {{ $aiInsight->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
                    <p class="text-slate-400 text-sm mb-2">Total Days</p>
                    <p class="text-3xl font-bold text-white">{{ $stats['total_days'] }}</p>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
                    <p class="text-slate-400 text-sm mb-2">Present Days</p>
                    <p class="text-3xl font-bold text-lime-400">{{ $stats['present_days'] }}</p>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
                    <p class="text-slate-400 text-sm mb-2">Total Hours</p>
                    <p class="text-3xl font-bold text-blue-400">{{ number_format($stats['total_hours'], 1) }}</p>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
                    <p class="text-slate-400 text-sm mb-2">Avg Hours/Day</p>
                    <p class="text-3xl font-bold text-purple-400">{{ number_format($stats['average_hours'], 1) }}</p>
                </div>
            </div>

            <!-- Date Filter -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700 mb-8">
                <form method="GET" action="{{ route('employee.attendance.index') }}" class="flex items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate ?? '' }}"
                            class="w-full bg-slate-700 border-slate-600 text-white rounded-lg focus:ring-lime-500 focus:border-lime-500">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-slate-300 mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate ?? '' }}"
                            class="w-full bg-slate-700 border-slate-600 text-white rounded-lg focus:ring-lime-500 focus:border-lime-500">
                    </div>
                    <button type="submit"
                        class="px-6 py-2 bg-lime-500 text-slate-900 rounded-lg font-semibold hover:bg-lime-400">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Attendance Table -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-700/50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Date</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Day</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Tracked Hours</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Payroll Hours</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Overtime</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            @forelse($attendances as $attendance)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-medium">
                                    {{ $attendance->date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                    {{ $attendance->date->format('l') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300 text-right">
                                    {{ number_format($attendance->tracked_hours, 2) }}h
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-lime-400 text-right font-semibold">
                                    {{ number_format($attendance->payroll_hours, 2) }}h
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    @if($attendance->overtime_hours > 0)
                                    <span class="text-blue-400 font-semibold">{{
                                        number_format($attendance->overtime_hours, 2) }}h</span>
                                    @else
                                    <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($attendance->payroll_hours >= 8)
                                    <span
                                        class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-semibold">Full
                                        Day</span>
                                    @elseif($attendance->payroll_hours > 0)
                                    <span
                                        class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs font-semibold">Partial</span>
                                    @else
                                    <span
                                        class="px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-xs font-semibold">Absent</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    No attendance records found for the selected period
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