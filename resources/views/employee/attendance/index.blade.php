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