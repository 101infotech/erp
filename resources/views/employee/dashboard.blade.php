<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if(isset($message))
            <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-6 mb-6">
                <p class="text-yellow-400">{{ $message }}</p>
            </div>
            @else
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Welcome, {{ $employee->full_name }}</h1>
                <p class="text-slate-400">Employee Code: {{ $employee->code }} | {{ $employee->department->name ?? 'No
                    Department' }}</p>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Attendance This Month -->
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-slate-400 text-sm font-medium">This Month Attendance</h3>
                        <svg class="w-8 h-8 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-white mb-1">{{ $stats['attendance']['days_present'] }} days</p>
                    <p class="text-sm text-slate-400">{{ $stats['attendance']['total_hours'] }} total hours</p>
                </div>

                <!-- Leave Balance -->
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-slate-400 text-sm font-medium">Available Leave</h3>
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-white mb-1">{{ $stats['leaves']['annual']['available'] ?? 0 }}
                        days</p>
                    <p class="text-sm text-slate-400">Annual leave remaining</p>
                </div>

                <!-- Last Salary -->
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-slate-400 text-sm font-medium">Last Payment</h3>
                        <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-white mb-1">NPR {{
                        number_format($stats['payroll']['last_payment'], 2) }}</p>
                    <p class="text-sm text-slate-400">Net salary received</p>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Recent Attendance -->
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-white">Recent Attendance</h3>
                        <a href="{{ route('employee.attendance.index') }}"
                            class="text-lime-400 hover:text-lime-300 text-sm">View All →</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentAttendance as $attendance)
                        <div class="flex items-center justify-between p-3 bg-slate-700/30 rounded-lg">
                            <div>
                                <p class="text-white font-medium">{{ $attendance->date->format('M d, Y') }}</p>
                                <p class="text-sm text-slate-400">{{ $attendance->payroll_hours }} hours</p>
                            </div>
                            @if($attendance->payroll_hours >= 8)
                            <span
                                class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-semibold">Full
                                Day</span>
                            @else
                            <span
                                class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs font-semibold">{{
                                $attendance->payroll_hours }}h</span>
                            @endif
                        </div>
                        @empty
                        <p class="text-slate-400 text-center py-4">No attendance records found</p>
                        @endforelse
                    </div>
                </div>

                <!-- Pending Leave Requests -->
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-white">Leave Requests</h3>
                        <a href="{{ route('employee.leave.create') }}"
                            class="px-4 py-2 bg-lime-500 text-slate-900 rounded-lg text-sm font-semibold hover:bg-lime-400">Request
                            Leave</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($pendingLeaves as $leave)
                        <div class="p-3 bg-slate-700/30 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-white font-medium capitalize">{{ $leave->leave_type }} Leave</p>
                                <span
                                    class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs font-semibold">Pending</span>
                            </div>
                            <p class="text-sm text-slate-400">
                                {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} –
                                {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                ({{ $leave->total_days }} days)
                            </p>
                        </div>
                        @empty
                        <p class="text-slate-400 text-center py-4">No pending leave requests</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Announcements -->
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-white">Recent Announcements</h3>
                        <a href="{{ route('employee.announcements.index') }}"
                            class="text-lime-400 hover:text-lime-300 text-sm">View All →</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentAnnouncements as $announcement)
                        <div class="p-4 bg-slate-700/30 rounded-lg hover:bg-slate-700/50 transition">
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="text-white font-medium flex-1">{{ $announcement->title }}</h4>
                                @php
                                $priorityColors = [
                                'low' => 'bg-blue-500/20 text-blue-400',
                                'normal' => 'bg-green-500/20 text-green-400',
                                'high' => 'bg-red-500/20 text-red-400',
                                ];
                                @endphp
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $priorityColors[$announcement->priority] ?? '' }}">
                                    {{ ucfirst($announcement->priority) }}
                                </span>
                            </div>
                            <p class="text-sm text-slate-400 mb-2">{{ Str::limit($announcement->content, 80) }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">{{ $announcement->created_at->diffForHumans()
                                    }}</span>
                                <a href="{{ route('employee.announcements.show', $announcement) }}"
                                    class="text-lime-400 hover:text-lime-300 text-xs font-medium">Read More →</a>
                            </div>
                        </div>
                        @empty
                        <p class="text-slate-400 text-center py-4">No announcements yet</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Leave Balance Overview -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700">
                <h3 class="text-xl font-bold text-white mb-6">Leave Balance {{ date('Y') }}</h3>
                @if(count($stats['leaves']) > 0)
                <div class="grid grid-cols-1 md:grid-cols-{{ min(count($stats['leaves']), 3) }} gap-6">
                    @foreach($stats['leaves'] as $type => $stat)
                    @php
                    $labels = [
                    'sick' => 'Sick Leave',
                    'casual' => 'Casual Leave',
                    'annual' => 'Annual Leave',
                    'period' => 'Period Leave',
                    'unpaid' => 'Unpaid Leave'
                    ];
                    $label = $labels[$type] ?? ucfirst($type) . ' Leave';
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-slate-300 font-medium">{{ $label }}</span>
                            <span class="text-lime-400 font-bold">{{ $stat['available'] ?? 0 }}/{{
                                $stat['quota'] ?? 0 }}</span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            @php
                            $quota = $stat['quota'] ?? 1;
                            $used = $stat['used'] ?? 0;
                            $percentage = $quota > 0 ? ($used / $quota) * 100 : 0;
                            @endphp
                            <div class="bg-lime-400 h-2 rounded-full" style="width: {{ max(0, 100 - $percentage) }}%">
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">{{ $stat['used'] ?? 0 }} days used this
                            year</p>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4">
                    <p class="text-yellow-400 text-sm text-center">No leave policies have been configured by your
                        administrator yet.</p>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</x-app-layout>