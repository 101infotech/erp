@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php
use App\Constants\PermissionConstants;

$financeData = $financeData ?? [];
$stats = $stats ?? [];
$hrmStats = $hrmStats ?? [];
$recentContacts = $recentContacts ?? collect();
$recentBookings = $recentBookings ?? collect();
$pendingLeaves = $pendingLeaves ?? collect();
$moduleAccess = $moduleAccess ?? [];

// Permission checks
$canViewFinance = auth()->user()->hasPermission(PermissionConstants::VIEW_FINANCES);
$canViewHRM = auth()->user()->hasPermission(PermissionConstants::VIEW_EMPLOYEES);
$canViewLeads = auth()->user()->hasPermission(PermissionConstants::VIEW_LEADS);
$canViewProjects = auth()->user()->hasPermission(PermissionConstants::VIEW_PROJECTS);
$canManageUsers = auth()->user()->hasPermission(PermissionConstants::MANAGE_USERS);
$canApproveLeaves = auth()->user()->hasPermission(PermissionConstants::APPROVE_LEAVE_REQUESTS);
@endphp
<div class="px-6 md:px-8 py-6 space-y-8">
    <!-- ========== HEADER SECTION ========== -->
    <div>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="text-slate-400 text-sm">{{ now()->format('l, F d, Y') }} • Here's what's happening with your
                    business today.</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500 mb-2">System Status</p>
                <div class="flex items-center justify-end gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <p class="text-sm font-semibold text-green-400">All Systems Operational</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== QUICK STATS GRID ========== -->
    <div>
        <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Key Metrics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Sites -->
            <a href="javascript:void(0)"
                class="group bg-slate-800/50 backdrop-blur-sm rounded-xl p-5 border border-slate-700 hover:border-blue-500/50 transition-all hover:bg-slate-800/70">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs font-medium mb-2">Total Sites</p>
                        <h3 class="text-4xl font-bold text-white">{{ $stats['total_sites'] ?? 0 }}</h3>
                        <p class="text-xs text-slate-500 mt-2">Active websites</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center group-hover:bg-blue-500/20 transition">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                    </div>
                </div>
            </a>

            <!-- Team Members (HRM) - Show only if has HRM access -->
            @if($canViewHRM)
            <a href="javascript:void(0)"
                class="group bg-slate-800/50 backdrop-blur-sm rounded-xl p-5 border border-slate-700 hover:border-lime-500/50 transition-all hover:bg-slate-800/70">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs font-medium mb-2">Team Members</p>
                        <h3 class="text-4xl font-bold text-white">{{ $hrmStats['total_employees'] ?? 0 }}</h3>
                        <p class="text-xs text-slate-500 mt-2">{{ $hrmStats['active_employees'] ?? 0 }} active</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-lime-500/10 rounded-xl flex items-center justify-center group-hover:bg-lime-500/20 transition">
                        <svg class="w-6 h-6 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </a>
            @endif

            <!-- Total Blogs -->
            <a href="javascript:void(0)"
                class="group bg-slate-800/50 backdrop-blur-sm rounded-xl p-5 border border-slate-700 hover:border-yellow-500/50 transition-all hover:bg-slate-800/70">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs font-medium mb-2">Total Blogs</p>
                        <h3 class="text-4xl font-bold text-white">{{ $stats['total_blogs'] ?? 0 }}</h3>
                        <p class="text-xs text-slate-500 mt-2">Published articles</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-yellow-500/10 rounded-xl flex items-center justify-center group-hover:bg-yellow-500/20 transition">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                </div>
            </a>

            <!-- New Contacts -->
            <a href="javascript:void(0)"
                class="group bg-slate-800/50 backdrop-blur-sm rounded-xl p-5 border border-slate-700 hover:border-orange-500/50 transition-all hover:bg-slate-800/70">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs font-medium mb-2">New Contacts</p>
                        <h3 class="text-4xl font-bold text-white">{{ $stats['new_contact_forms'] ?? 0 }}</h3>
                        <p class="text-xs text-slate-500 mt-2">Last 7 days</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-orange-500/10 rounded-xl flex items-center justify-center group-hover:bg-orange-500/20 transition">
                        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- ========== FINANCE & HRM OVERVIEW ========== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Finance Summary - Show only if has Finance access -->
        @if($canViewFinance)
        <div class="lg:col-span-2 bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-white">Business Summary</h3>
                <a href="{{ route('admin.finance.dashboard') }}"
                    class="text-xs text-lime-400 hover:text-lime-300 font-semibold inline-flex items-center gap-1">
                    View More
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <!-- Revenue -->
                <div
                    class="rounded-lg bg-gradient-to-br from-green-500/10 to-green-500/5 border border-green-500/20 p-4">
                    <p class="text-green-300 text-xs font-semibold mb-2">Revenue</p>
                    <h4 class="text-2xl font-bold text-white">NPR {{ is_array($financeData) ?
                        number_format($financeData['revenue'] ?? $financeData['total_revenue'] ?? 0, 0) : '0' }}</h4>
                    <p class="text-green-400 text-xs mt-2">+0% from last month</p>
                </div>

                <!-- Expenses -->
                <div class="rounded-lg bg-gradient-to-br from-red-500/10 to-red-500/5 border border-red-500/20 p-4">
                    <p class="text-red-300 text-xs font-semibold mb-2">Expenses</p>
                    <h4 class="text-2xl font-bold text-white">NPR {{ is_array($financeData) ?
                        number_format($financeData['expenses'] ?? $financeData['total_expenses'] ?? 0, 0) : '0' }}</h4>
                    <p class="text-red-400 text-xs mt-2">+0% from last month</p>
                </div>

                <!-- Net Profit -->
                <div class="rounded-lg bg-gradient-to-br from-blue-500/10 to-blue-500/5 border border-blue-500/20 p-4">
                    <p class="text-blue-300 text-xs font-semibold mb-2">Net Profit</p>
                    <h4 class="text-2xl font-bold text-white">NPR {{ is_array($financeData) ?
                        number_format(($financeData['revenue'] ?? $financeData['total_revenue'] ?? 0) -
                        ($financeData['expenses'] ?? $financeData['total_expenses'] ?? 0), 0) : '0' }}</h4>
                    <p class="text-blue-400 text-xs mt-2">0% margin</p>
                </div>

                <!-- Pending Receivables -->
                <div
                    class="rounded-lg bg-gradient-to-br from-amber-500/10 to-amber-500/5 border border-amber-500/20 p-4">
                    <p class="text-amber-300 text-xs font-semibold mb-2">Receivables</p>
                    <h4 class="text-2xl font-bold text-white">NPR {{ is_array($financeData) ?
                        number_format($financeData['pending_receivables'] ?? 0, 0) : '0' }}</h4>
                    <p class="text-amber-400 text-xs mt-2">Pending</p>
                </div>
            </div>
        </div>
        @endif

        <!-- HRM Quick Stats - Show only if has HRM access -->
        @if($canViewHRM)
        <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-white">People Health</h3>
                <a href="{{ route('admin.hrm.employees.index') }}"
                    class="text-xs text-lime-400 hover:text-lime-300 font-semibold">View HRM</a>
            </div>

            <div class="space-y-3">
                <div
                    class="flex items-center justify-between p-4 rounded-lg bg-slate-700/40 border border-slate-600/50 hover:border-slate-600 transition">
                    <div class="flex-1">
                        <p class="text-slate-400 text-xs font-medium">Active Employees</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $hrmStats['active_employees'] ?? 0 }}</p>
                    </div>
                    <div class="text-right">
                        <svg class="w-8 h-8 text-lime-400/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>

                <div
                    class="flex items-center justify-between p-4 rounded-lg bg-slate-700/40 border border-slate-600/50 hover:border-slate-600 transition">
                    <div class="flex-1">
                        <p class="text-slate-400 text-xs font-medium">Pending Leaves</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $hrmStats['pending_leaves'] ?? 0 }}</p>
                    </div>
                    <a href="{{ route('admin.hrm.leaves.index') }}"
                        class="text-xs text-yellow-400 hover:text-yellow-300 font-semibold px-2 py-1 rounded bg-yellow-500/10 border border-yellow-500/20">Review</a>
                </div>

                <div
                    class="flex items-center justify-between p-4 rounded-lg bg-slate-700/40 border border-slate-600/50 hover:border-slate-600 transition">
                    <div class="flex-1">
                        <p class="text-slate-400 text-xs font-medium">Draft Payrolls</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $hrmStats['draft_payrolls'] ?? 0 }}</p>
                    </div>
                    <a href="{{ route('admin.hrm.payroll.index') }}"
                        class="text-xs text-blue-400 hover:text-blue-300 font-semibold px-2 py-1 rounded bg-blue-500/10 border border-blue-500/20">Process</a>
                </div>

                <div
                    class="flex items-center justify-between p-4 rounded-lg bg-slate-700/40 border border-slate-600/50 hover:border-slate-600 transition">
                    <div class="flex-1">
                        <p class="text-slate-400 text-xs font-medium">Attendance Issues</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $hrmStats['unreviewed_anomalies'] ?? 0 }}</p>
                    </div>
                    <a href="{{ route('admin.hrm.attendance.index') }}"
                        class="text-xs text-red-400 hover:text-red-300 font-semibold px-2 py-1 rounded bg-red-500/10 border border-red-500/20">Check</a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- ========== RECENT ACTIVITY ========== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Contacts -->
        <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-slate-700 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">Recent Contacts</h3>
                <span
                    class="text-xs px-2 py-1 rounded-full bg-orange-500/10 text-orange-400 border border-orange-500/20">{{
                    $recentContacts->count() }}</span>
            </div>
            @if($recentContacts->count() > 0)
            <div class="divide-y divide-slate-700">
                @foreach($recentContacts as $contact)
                <div class="p-4 hover:bg-slate-700/30 transition cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-white font-semibold text-sm">{{ $contact->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-400">{{ $contact->email ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-500 mt-2">{{ $contact->created_at?->format('M d, Y h:i A') ??
                                'N/A' }}</p>
                        </div>
                        <span
                            class="text-xs px-2 py-1 rounded-full bg-slate-700 text-slate-300 font-medium capitalize">{{
                            $contact->status ?? 'new' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-8 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <p class="text-sm font-medium">No recent contacts</p>
            </div>
            @endif
        </div>

        <!-- Recent Bookings -->
        <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-slate-700 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">Recent Bookings</h3>
                <span class="text-xs px-2 py-1 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">{{
                    $recentBookings->count() }}</span>
            </div>
            @if($recentBookings->count() > 0)
            <div class="divide-y divide-slate-700">
                @foreach($recentBookings as $booking)
                <div class="p-4 hover:bg-slate-700/30 transition cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-white font-semibold text-sm">{{ $booking->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-400">{{ $booking->email ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-500 mt-2">{{ $booking->created_at?->format('M d, Y h:i A') ??
                                'N/A' }}</p>
                        </div>
                        <span
                            class="text-xs px-2 py-1 rounded-full bg-slate-700 text-slate-300 font-medium capitalize">{{
                            $booking->status ?? 'pending' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-8 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-sm font-medium">No recent bookings</p>
            </div>
            @endif
        </div>
    </div>

    <!-- ========== PENDING ACTIONS ========== -->
    @if($canApproveLeaves)
    @if($pendingLeaves->count() > 0)
    <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Pending Leave Requests</h3>
            <span class="text-xs px-2 py-1 rounded-full bg-yellow-500/10 text-yellow-400 border border-yellow-500/20">{{
                $pendingLeaves->count() }} Pending</span>
        </div>
        <div class="divide-y divide-slate-700">
            @foreach($pendingLeaves as $leave)
            <div class="p-4 hover:bg-slate-700/30 transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-white font-semibold text-sm">{{ $leave->employee?->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-slate-400">{{ $leave->leave_type ?? 'N/A' }} Leave</p>
                        <p class="text-xs text-slate-500 mt-2">{{ $leave->from_date?->format('M d') }} - {{
                            $leave->to_date?->format('M d, Y') }}</p>
                    </div>
                    <a href="{{ route('admin.hrm.leaves.show', $leave->id) }}"
                        class="text-xs px-3 py-1 rounded-full bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 font-semibold hover:bg-yellow-500/20 transition">
                        Review
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endif

    <!-- ========== QUICK ACTIONS ========== -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @if($canViewHRM)
        <a href="{{ route('admin.hrm.employees.index') }}"
            class="group bg-slate-800/50 backdrop-blur-sm rounded-xl p-5 border border-slate-700 hover:border-lime-500/50 transition-all hover:bg-slate-800/70 text-center">
            <svg class="w-8 h-8 mx-auto text-lime-400 mb-3 group-hover:scale-110 transition" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z" />
            </svg>
            <p class="text-white font-semibold text-sm">Manage Employees</p>
            <p class="text-xs text-slate-400 mt-1">View & edit staff</p>
        </a>
        @endif

        @if($canViewFinance)
        <a href="{{ route('admin.finance.dashboard') }}"
            class="group bg-slate-800/50 backdrop-blur-sm rounded-xl p-5 border border-slate-700 hover:border-green-500/50 transition-all hover:bg-slate-800/70 text-center">
            <svg class="w-8 h-8 mx-auto text-green-400 mb-3 group-hover:scale-110 transition" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-white font-semibold text-sm">Finance Dashboard</p>
            <p class="text-xs text-slate-400 mt-1">View finances</p>
        </a>
        @endif

        @if($canApproveLeaves)
        <a href="{{ route('admin.hrm.leaves.index') }}"
            class="group bg-slate-800/50 backdrop-blur-sm rounded-xl p-5 border border-slate-700 hover:border-yellow-500/50 transition-all hover:bg-slate-800/70 text-center">
            <svg class="w-8 h-8 mx-auto text-yellow-400 mb-3 group-hover:scale-110 transition" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-white font-semibold text-sm">Leave Requests</p>
            <p class="text-xs text-slate-400 mt-1">Manage leaves</p>
        </a>
        @endif

        <!-- User Accounts - Always visible to admins -->
        <a href="{{ route('admin.users.index') }}"
            class="group bg-slate-800/50 backdrop-blur-sm rounded-xl p-5 border border-slate-700 hover:border-blue-500/50 transition-all hover:bg-slate-800/70 text-center">
            <svg class="w-8 h-8 mx-auto text-blue-400 mb-3 group-hover:scale-110 transition" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="text-white font-semibold text-sm">User Accounts</p>
            <p class="text-xs text-slate-400 mt-1">Manage users</p>
        </a>
    </div>
</div>
@endsection