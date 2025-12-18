<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if(isset($message))
            <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-6 mb-6">
                <p class="text-yellow-400">{{ $message }}</p>
            </div>
            @else
            <!-- Welcome Section with Profile Card -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Welcome Card -->
                <div class="lg:col-span-2">
                    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6">
                        <h1 class="text-3xl font-bold text-white mb-2">Welcome back, {{ $employee->full_name }}</h1>
                        <p class="text-slate-300 text-sm">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-2 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                                </svg>
                                Employee Code: <strong class="ml-1">{{ $employee->code }}</strong>
                            </span>
                            •
                            <span class="inline-flex items-center ml-2">
                                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.582a2 2 0 00-1.897 1.13A6 6 0 0112 21m0 0h6.582a2 2 0 001.897-1.13A6 6 0 0012 21m0 0H6.418A2 2 0 004.52 22.13a6 6 0 00.879-1.13m8 0a2 2 0 00-1.897-1.13h-5.582a2 2 0 00-1.897 1.13" />
                                </svg>
                                {{ $employee->department->name ?? 'No Department' }}
                            </span>
                        </p>
                        <p class="text-xs text-slate-500 mt-3">{{ now()->format('l, F d, Y') }} • Last login: {{ $employee->user->last_login_at ? $employee->user->last_login_at->diffForHumans() : 'N/A' }}</p>
                    </div>
                </div>

                <!-- Quick Action Card -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6 flex flex-col justify-center">
                    <div class="text-center">
                        <a href="{{ route('employee.profile.show') }}" class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-lime-500/20 text-lime-400 mb-3 hover:bg-lime-500/30 transition">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </a>
                        <h3 class="text-white font-semibold text-sm mb-2">Your Profile</h3>
                        <a href="{{ route('employee.profile.show') }}" class="text-lime-400 hover:text-lime-300 text-xs font-medium">View Full Profile →</a>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <!-- Attendance This Month -->
                <div class="group bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl p-5 border border-slate-700 hover:border-lime-500/30 transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-lg bg-lime-500/20 flex items-center justify-center group-hover:bg-lime-500/30 transition">
                            <svg class="w-6 h-6 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 13l2 2 4-4" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">This Month</span>
                    </div>
                    <p class="text-2xl font-bold text-white mb-1">{{ $stats['attendance']['days_present'] }}</p>
                    <p class="text-xs text-slate-400">Days present • {{ $stats['attendance']['total_hours'] }}h</p>
                </div>

                <!-- Leave Balance -->
                <div class="group bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl p-5 border border-slate-700 hover:border-blue-500/30 transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center group-hover:bg-blue-500/30 transition">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Annual</span>
                    </div>
                    <p class="text-2xl font-bold text-white mb-1">{{ $stats['leaves']['annual']['available'] ?? 0 }}</p>
                    <p class="text-xs text-slate-400">Days available</p>
                </div>

                <!-- Last Payment -->
                <div class="group bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl p-5 border border-slate-700 hover:border-green-500/30 transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center group-hover:bg-green-500/30 transition">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Salary</span>
                    </div>
                    <p class="text-2xl font-bold text-white mb-1">NPR {{ number_format($stats['payroll']['last_payment'], 2) }}</p>
                    <p class="text-xs text-slate-400">Last payment</p>
                </div>

                <!-- Average Hours -->
                <div class="group bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl p-5 border border-slate-700 hover:border-purple-500/30 transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center group-hover:bg-purple-500/30 transition">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Avg Daily</span>
                    </div>
                    <p class="text-2xl font-bold text-white mb-1">{{ $stats['attendance']['average_hours'] }}</p>
                    <p class="text-xs text-slate-400">Hours per day</p>
                </div>
            </div>

            <!-- Main Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Recent Attendance Section -->
                <div class="lg:col-span-2 bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700 overflow-hidden">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-lg bg-lime-500/20 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Recent Attendance</h3>
                                <p class="text-xs text-slate-400">Last 3 days</p>
                            </div>
                        </div>
                        <a href="{{ route('employee.attendance.index') }}" class="text-lime-400 hover:text-lime-300 text-sm font-medium flex items-center gap-1">
                            View All
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-4 space-y-2 max-h-80 overflow-y-auto">
                        @forelse($recentAttendance as $attendance)
                        <div class="flex items-center justify-between p-3 bg-slate-700/20 rounded-lg hover:bg-slate-700/30 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-700/50 flex items-center justify-center group-hover:bg-slate-700 transition">
                                    <svg class="w-5 h-5 text-slate-400 group-hover:text-lime-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-white font-medium text-sm">{{ $attendance->date->format('l, M d') }}</p>
                                    <p class="text-xs text-slate-400">{{ $attendance->payroll_hours }} hours</p>
                                </div>
                            </div>
                            @if($attendance->payroll_hours >= 8)
                            <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-semibold">Full Day</span>
                            @elseif($attendance->payroll_hours > 0)
                            <span class="px-3 py-1 bg-amber-500/20 text-amber-400 rounded-full text-xs font-semibold">{{ $attendance->payroll_hours }}h</span>
                            @else
                            <span class="px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-xs font-semibold">Absent</span>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <p class="text-slate-400 text-sm">No attendance records found</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Leave & Pending Requests -->
                <div class="space-y-6">
                    <!-- Leave Requests -->
                    <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700 overflow-hidden">
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 12a5 5 0 1110 0A5 5 0 017 12z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white">Leave Requests</h3>
                                    <p class="text-xs text-slate-400">Pending</p>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="px-6 py-4">
                            @forelse($pendingLeaves->take(3) as $leave)
                            <div class="mb-3 last:mb-0 p-3 bg-slate-700/20 rounded-lg hover:bg-slate-700/30 transition">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-white font-medium text-sm capitalize">{{ $leave->leave_type }} Leave</p>
                                    <span class="px-2 py-0.5 bg-yellow-500/20 text-yellow-400 rounded text-xs font-semibold">Pending</span>
                                </div>
                                <p class="text-xs text-slate-400">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} – {{ \Carbon\Carbon::parse($leave->end_date)->format('M d') }} ({{ $leave->total_days }} days)
                                </p>
                            </div>
                            @empty
                            <div class="text-center py-6">
                                <p class="text-slate-400 text-sm mb-3">No pending leave requests</p>
                                <a href="{{ route('employee.leave.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 text-xs font-semibold rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Request Leave
                                </a>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('employee.leave.create') }}" class="flex flex-col items-center justify-center p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg hover:border-blue-500/50 hover:bg-blue-500/15 transition text-center">
                            <svg class="w-6 h-6 text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <p class="text-xs font-semibold text-blue-400">Request Leave</p>
                        </a>
                        <a href="{{ route('employee.resource-requests.index') }}" class="flex flex-col items-center justify-center p-4 bg-purple-500/10 border border-purple-500/30 rounded-lg hover:border-purple-500/50 hover:bg-purple-500/15 transition text-center">
                            <svg class="w-6 h-6 text-purple-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <p class="text-xs font-semibold text-purple-400">Resources</p>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Payroll & Announcements -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Recent Payroll -->
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700 overflow-hidden">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-lg bg-green-500/20 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Recent Payroll</h3>
                                <p class="text-xs text-slate-400">Last 3 records</p>
                            </div>
                        </div>
                        <a href="{{ route('employee.payroll.index') }}" class="text-lime-400 hover:text-lime-300 text-sm font-medium flex items-center gap-1">
                            View All
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-4 space-y-3">
                        @forelse($recentPayrolls as $payroll)
                        <div class="flex items-center justify-between p-3 bg-slate-700/20 rounded-lg hover:bg-slate-700/30 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-700/50 flex items-center justify-center group-hover:bg-slate-700 transition">
                                    <svg class="w-5 h-5 text-slate-400 group-hover:text-green-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-white font-medium text-sm">{{ \Carbon\Carbon::parse($payroll->period_start_bs)->format('M Y') }}</p>
                                    <p class="text-xs text-slate-400">Net: NPR {{ number_format($payroll->net_salary, 2) }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-semibold">Paid</span>
                        </div>
                        @empty
                        <div class="text-center py-6">
                            <p class="text-slate-400 text-sm">No payroll records found</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Announcements -->
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700 overflow-hidden">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Announcements</h3>
                                <p class="text-xs text-slate-400">Latest updates</p>
                            </div>
                        </div>
                        <a href="{{ route('employee.announcements.index') }}" class="text-lime-400 hover:text-lime-300 text-sm font-medium flex items-center gap-1">
                            View All
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-4 space-y-3 max-h-80 overflow-y-auto">
                        @forelse($recentAnnouncements as $announcement)
                        <div class="p-4 bg-slate-700/20 rounded-lg hover:bg-slate-700/30 transition border-l-4 {{ ['low' => 'border-blue-500', 'normal' => 'border-green-500', 'high' => 'border-red-500'][$announcement->priority] ?? 'border-slate-500' }}">
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="text-white font-semibold text-sm flex-1 leading-tight">{{ $announcement->title }}</h4>
                                @php
                                $priorityColors = [
                                'low' => 'bg-blue-500/20 text-blue-400',
                                'normal' => 'bg-green-500/20 text-green-400',
                                'high' => 'bg-red-500/20 text-red-400',
                                ];
                                @endphp
                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded {{ $priorityColors[$announcement->priority] ?? '' }}">
                                    {{ ucfirst($announcement->priority) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mb-2 line-clamp-2">{{ $announcement->content }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">{{ $announcement->created_at->diffForHumans() }}</span>
                                <a href="{{ route('employee.announcements.show', $announcement) }}" class="text-lime-400 hover:text-lime-300 text-xs font-semibold">Read →</a>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-6">
                            <p class="text-slate-400 text-sm">No announcements yet</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Leave Balance Summary -->
            <!-- Leave Balance Summary -->
            <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl border border-slate-700 overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-lg bg-indigo-500/20 flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Leave Balance {{ date('Y') }}</h3>
                            <p class="text-xs text-slate-400">Annual and other leave types</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="px-6 py-6">
                    @if(count($stats['leaves']) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        @foreach($stats['leaves'] as $type => $stat)
                        @php
                        $labels = [
                        'sick' => 'Sick Leave',
                        'casual' => 'Casual Leave',
                        'annual' => 'Annual Leave',
                        'period' => 'Period Leave',
                        'unpaid' => 'Unpaid Leave'
                        ];
                        $colors = [
                        'sick' => 'from-red-500/20 to-red-600/10',
                        'casual' => 'from-blue-500/20 to-blue-600/10',
                        'annual' => 'from-green-500/20 to-green-600/10',
                        'period' => 'from-purple-500/20 to-purple-600/10',
                        'unpaid' => 'from-orange-500/20 to-orange-600/10'
                        ];
                        $label = $labels[$type] ?? ucfirst($type) . ' Leave';
                        @endphp
                        <div class="bg-gradient-to-br {{ $colors[$type] ?? 'from-slate-700 to-slate-800' }} rounded-lg p-4 border border-slate-700">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-slate-300 font-semibold text-sm">{{ $label }}</span>
                                <span class="px-2 py-1 bg-slate-900/50 rounded text-xs font-bold text-white">{{ $stat['used'] ?? 0 }}/{{ $stat['quota'] ?? 0 }}</span>
                            </div>
                            <div class="mb-3">
                                <div class="relative w-full bg-slate-700 rounded-full h-2 overflow-hidden">
                                    @php
                                    $quota = $stat['quota'] ?? 1;
                                    $used = $stat['used'] ?? 0;
                                    $percentage = min(($used / $quota) * 100, 100);
                                    $barColors = [
                                    'sick' => 'bg-red-400',
                                    'casual' => 'bg-blue-400',
                                    'annual' => 'bg-green-400',
                                    'period' => 'bg-purple-400',
                                    'unpaid' => 'bg-orange-400'
                                    ];
                                    @endphp
                                    <div class="{{ $barColors[$type] ?? 'bg-slate-400' }} h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400 text-center">
                                <span class="font-semibold text-white">{{ $stat['available'] ?? 0 }}</span> available
                            </p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-6 text-center">
                        <svg class="w-12 h-12 text-yellow-400 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-yellow-400 text-sm font-medium">No leave policies configured yet</p>
                        <p class="text-yellow-300/70 text-xs mt-1">Contact your HR administrator to set up leave policies</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>