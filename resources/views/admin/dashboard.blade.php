@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="px-6 md:px-8 space-y-6">
    <!-- ========== HEADER SECTION ========== -->
    <div>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-1">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="text-slate-400 text-sm">{{ now()->format('l, F d, Y') }} • Here's what's happening with your
                    business today.</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500 mb-1">System Status</p>
                <p class="text-sm font-semibold text-green-400">✓ All Systems Operational</p>
            </div>
        </div>
    </div>

    <!-- ========== PRIORITY 1: PENDING ACTIONS (High Priority) ========== -->
    @if(isset($hrmStats) && (($hrmStats['pending_leaves'] ?? 0) > 0 || ($hrmStats['draft_payrolls'] ?? 0) > 0 ||
    ($hrmStats['unreviewed_anomalies'] ?? 0) > 0))
    <div>
        <x-dashboard-section-header title="⚡ Pending Actions" subtitle="Requires immediate attention" />
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(($hrmStats['pending_leaves'] ?? 0) > 0)
            <a href="{{ route('admin.hrm.leaves.index') }}?status=pending"
                class="bg-yellow-500/10 rounded-xl p-4 border border-yellow-500/30 hover:border-yellow-500/50 transition group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-yellow-400">{{ $hrmStats['pending_leaves'] ?? 0 }}</span>
                </div>
                <h3 class="text-base font-semibold text-white">Pending Leave Requests</h3>
            </a>
            @endif

            @if(($hrmStats['draft_payrolls'] ?? 0) > 0)
            <a href="{{ route('admin.hrm.payroll.index') }}?status=draft"
                class="bg-slate-500/10 rounded-xl p-4 border border-blue-500/30 hover:border-blue-500/50 transition group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-slate-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-blue-400">{{ $hrmStats['draft_payrolls'] ?? 0 }}</span>
                </div>
                <h3 class="text-base font-semibold text-white">Draft Payrolls</h3>
            </a>
            @endif

            @if(($hrmStats['unreviewed_anomalies'] ?? 0) > 0)
            <a href="{{ route('admin.hrm.attendance.index') }}"
                class="bg-red-500/10 rounded-xl p-4 border border-red-500/30 hover:border-red-500/50 transition group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-red-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-red-400">{{ $hrmStats['unreviewed_anomalies'] ?? 0 }}</span>
                </div>
                <h3 class="text-base font-semibold text-white">Attendance Anomalies</h3>
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- ========== PRIORITY 2: KEY METRICS ========== -->
    <div>
        <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-3">Key Metrics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Team Members -->
            <div
                class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 hover:border-lime-500/50 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs mb-1.5">Team Members</p>
                        <h2 class="text-2xl font-bold text-white">{{ $hrmStats['total_employees'] ??
                            $stats['total_team_members'] }}</h2>
                        <p class="text-xs text-slate-500 mt-1">{{ $hrmStats['active_employees'] ?? 0 }} active</p>
                    </div>
                    <div class="w-10 h-10 bg-lime-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Sites -->
            <div
                class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 hover:border-blue-500/50 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs mb-1.5">Total Sites</p>
                        <h2 class="text-2xl font-bold text-white">{{ $stats['total_sites'] }}</h2>
                        <p class="text-xs text-slate-500 mt-1">Active websites</p>
                    </div>
                    <div class="w-10 h-10 bg-slate-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- New Contacts -->
            <div
                class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 hover:border-orange-500/50 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs mb-1.5">New Contacts</p>
                        <h2 class="text-2xl font-bold text-white">{{ $stats['new_contact_forms'] }}</h2>
                        <p class="text-xs text-slate-500 mt-1">Pending</p>
                    </div>
                    <div class="w-10 h-10 bg-orange-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Blogs -->
            <div
                class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 hover:border-yellow-500/50 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs mb-1.5">Total Blogs</p>
                        <h2 class="text-2xl font-bold text-white">{{ $stats['total_blogs'] }}</h2>
                        <p class="text-xs text-slate-500 mt-1">Published</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== PRIORITY 3: BUSINESS OVERVIEW (Finance + HRM) ========== -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-white">Business Overview</h2>
                <p class="text-sm text-slate-400">Finance and HR snapshots</p>
            </div>
            <div class="flex items-center gap-3 text-xs font-medium">
                <a href="{{ route('admin.finance.dashboard') }}"
                    class="text-lime-400 hover:text-lime-300 inline-flex items-center gap-2">
                    Finance Dashboard
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <span class="text-slate-700">|</span>
                <a href="{{ route('admin.hrm.employees.index') }}"
                    class="text-blue-300 hover:text-blue-200 inline-flex items-center gap-2">
                    HRM Hub
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Finance Summary -->
            <div class="lg:col-span-2 bg-slate-800/50 backdrop-blur-sm rounded-xl p-5 border border-slate-700">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-slate-400 text-xs">Finance snapshot</p>
                        <h3 class="text-base font-semibold text-white">This fiscal year</h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div class="rounded-lg bg-green-500/5 border border-green-500/20 p-3">
                        <p class="text-green-300 text-xs mb-1">Revenue</p>
                        <h2 class="text-xl font-bold text-white" id="total-revenue">NPR 0</h2>
                        <p class="text-green-400 text-xs" id="revenue-growth">+0% vs last month</p>
                    </div>
                    <div class="rounded-lg bg-red-500/5 border border-red-500/20 p-3">
                        <p class="text-red-300 text-xs mb-1">Expenses</p>
                        <h2 class="text-xl font-bold text-white" id="total-expenses">NPR 0</h2>
                        <p class="text-red-400 text-xs" id="expense-growth">+0% vs last month</p>
                    </div>
                    <div class="rounded-lg bg-blue-500/5 border border-blue-500/20 p-3">
                        <p class="text-blue-300 text-xs mb-1">Net Profit</p>
                        <h2 class="text-xl font-bold text-white" id="net-profit">NPR 0</h2>
                        <p class="text-blue-400 text-xs" id="profit-margin">0% margin</p>
                    </div>
                    <div class="rounded-lg bg-yellow-500/5 border border-yellow-500/20 p-3">
                        <p class="text-yellow-300 text-xs mb-1">Pending Receivables</p>
                        <h2 class="text-xl font-bold text-white" id="pending-payments">NPR 0</h2>
                        <p class="text-yellow-400 text-xs" id="pending-count">0 pending</p>
                    </div>
                </div>
            </div>

            <!-- HRM Summary -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-5 border border-slate-700">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-slate-400 text-xs">HRM snapshot</p>
                        <h3 class="text-base font-semibold text-white">People health</h3>
                    </div>
                    <a href="{{ route('admin.hrm.employees.index') }}"
                        class="text-xs text-lime-400 hover:text-lime-300 font-medium">View HRM</a>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-700/40">
                        <div>
                            <p class="text-xs text-slate-400">Active employees</p>
                            <h4 class="text-lg font-semibold text-white">{{ $hrmStats['active_employees'] ??
                                ($hrmStats['total_employees'] ?? 0) }}</h4>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full bg-lime-500/10 text-lime-300">HRM</span>
                    </div>
                    <div
                        class="flex items-center justify-between p-3 rounded-lg bg-yellow-500/5 border border-yellow-500/20">
                        <div>
                            <p class="text-xs text-yellow-200">Pending leaves</p>
                            <h4 class="text-lg font-semibold text-white">{{ $hrmStats['pending_leaves'] ?? 0 }}</h4>
                        </div>
                        <a href="{{ route('admin.hrm.leaves.index') }}"
                            class="text-xs text-yellow-300 hover:text-yellow-200">Review</a>
                    </div>
                    <div
                        class="flex items-center justify-between p-3 rounded-lg bg-blue-500/5 border border-blue-500/20">
                        <div>
                            <p class="text-xs text-blue-200">Draft payrolls</p>
                            <h4 class="text-lg font-semibold text-white">{{ $hrmStats['draft_payrolls'] ?? 0 }}</h4>
                        </div>
                        <a href="{{ route('admin.hrm.payroll.index') }}"
                            class="text-xs text-blue-300 hover:text-blue-200">Open</a>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-red-500/5 border border-red-500/20">
                        <div>
                            <p class="text-xs text-red-200">Attendance flags</p>
                            <h4 class="text-lg font-semibold text-white">{{ $hrmStats['unreviewed_anomalies'] ?? 0 }}
                            </h4>
                        </div>
                        <a href="{{ route('admin.hrm.attendance.index') }}"
                            class="text-xs text-red-300 hover:text-red-200">Investigate</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== PRIORITY 4: LEADS CENTER ========== -->
    <div>
        <x-dashboard-section-header title="Leads Center" subtitle="Manage and track sales leads"
            action="{{ route('admin.leads.dashboard') }}" actionLabel="View Full Dashboard" />
        <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
            @if(isset($leadsStats) && isset($leadsStats['total_leads']) && $leadsStats['total_leads'] > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-slate-700/40 rounded-lg p-4">
                    <p class="text-slate-400 text-xs mb-1">Total Leads</p>
                    <h3 class="text-2xl font-bold text-white">{{ $leadsStats['total_leads'] }}</h3>
                </div>
                <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                    <p class="text-green-300 text-xs mb-1">Open Leads</p>
                    <h3 class="text-2xl font-bold text-white">{{ $leadsStats['open_leads'] }}</h3>
                </div>
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4">
                    <p class="text-blue-300 text-xs mb-1">Conversion Rate</p>
                    <h3 class="text-2xl font-bold text-white">{{ $leadsStats['total_leads'] > 0 ?
                        round(($leadsStats['total_leads'] - $leadsStats['open_leads']) / $leadsStats['total_leads'] *
                        100) : 0 }}%</h3>
                </div>
            </div>

            @if($leadsStats['recent_leads']->count() > 0)
            <div>
                <h4 class="text-sm font-semibold text-slate-300 mb-3">Recent Leads</h4>
                <div class="space-y-3">
                    @foreach($leadsStats['recent_leads'] as $lead)
                    <a href="{{ route('admin.leads.index') }}"
                        class="flex items-center justify-between p-4 bg-slate-900/40 rounded-lg border border-slate-700 hover:border-slate-600 transition">
                        <div class="flex-1">
                            <h5 class="font-semibold text-white">{{ $lead->client_name }}</h5>
                            <p class="text-sm text-slate-400">{{ $lead->service_requested }} • {{ $lead->location }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                @if($lead->leadStage)
                                <span class="text-xs px-2 py-0.5 rounded bg-blue-500/10 text-blue-300">{{
                                    $lead->leadStage->stage_name }}</span>
                                @endif
                                <span
                                    class="text-xs px-2 py-0.5 rounded bg-{{ $lead->priority === 'high' ? 'red' : ($lead->priority === 'medium' ? 'yellow' : 'slate') }}-500/10 text-{{ $lead->priority === 'high' ? 'red' : ($lead->priority === 'medium' ? 'yellow' : 'slate') }}-300">
                                    {{ ucfirst($lead->priority) }} Priority
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500">{{ $lead->created_at->diffForHumans() }}</p>
                            @if($lead->quoted_amount)
                            <p class="text-sm font-semibold text-lime-400 mt-1">NPR {{
                                number_format($lead->quoted_amount) }}</p>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.leads.index') }}"
                        class="inline-flex items-center gap-2 text-sm text-lime-400 hover:text-lime-300">
                        View All Leads
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
            @endif
            @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-semibold text-white mb-2">No Leads Yet</h3>
                <p class="text-slate-400 mb-4">Start tracking your sales pipeline by creating your first lead.</p>
                <a href="{{ route('admin.leads.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-lime-500 text-slate-900 rounded-lg font-semibold hover:bg-lime-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create First Lead
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- ========== PRIORITY 5: QUICK ACCESS MODULES ========== -->
    <div>
        <x-dashboard-section-header title="Quick Access" subtitle="Jump to modules" />
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.leads.dashboard') }}"
                class="group bg-gradient-to-br from-rose-500/20 to-rose-600/10 rounded-xl p-4 hover:scale-105 transition border border-rose-500/20 hover:border-rose-500/40">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-semibold text-rose-300">Leads Center</span>
                    <svg class="w-4 h-4 text-rose-400 group-hover:translate-x-1 transition" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <p class="text-xs text-slate-400">Track sales pipeline</p>
            </a>

            <a href="{{ route('admin.sites.index') }}"
                class="group bg-gradient-to-br from-blue-500/20 to-blue-600/10 rounded-xl p-4 hover:scale-105 transition border border-blue-500/20 hover:border-blue-500/40">
                <div class="flex items-center justify-between mb-3">
                    <span
                        class="text-xs font-semibold text-blue-300 px-2.5 py-1 bg-black/20 rounded-full">#Management</span>
                    <svg class="w-4 h-4 text-blue-300 group-hover:translate-x-1 transition" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Sites</h3>
                <p class="text-xs text-blue-200/80">Manage websites</p>
            </a>

            <a href="{{ route('admin.hrm.employees.index') }}"
                class="group bg-gradient-to-br from-lime-500/20 to-lime-600/10 rounded-xl p-4 hover:scale-105 transition border border-lime-500/20 hover:border-lime-500/40">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-lime-300 px-2.5 py-1 bg-black/20 rounded-full">#HRM</span>
                    <svg class="w-4 h-4 text-lime-300 group-hover:translate-x-1 transition" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">HR Management</h3>
                <p class="text-xs text-lime-200/80">Employees & payroll</p>
            </a>

            <a href="{{ route('admin.hrm.attendance.index') }}"
                class="group bg-gradient-to-br from-purple-500/20 to-purple-600/10 rounded-xl p-4 hover:scale-105 transition border border-purple-500/20 hover:border-purple-500/40">
                <div class="flex items-center justify-between mb-3">
                    <span
                        class="text-xs font-semibold text-purple-300 px-2.5 py-1 bg-black/20 rounded-full">#Tracking</span>
                    <svg class="w-4 h-4 text-purple-300 group-hover:translate-x-1 transition" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Attendance</h3>
                <p class="text-xs text-purple-200/80">Track employee time</p>
            </a>
        </div>
    </div>

    <!-- ========== PRIORITY 6: AI INSIGHTS ========== -->
    <div>
        <div
            class="bg-gradient-to-r from-lime-950/20 via-slate-900 to-lime-950/20 rounded-2xl border border-lime-500/20 overflow-hidden">
            <div class="bg-gradient-to-r from-lime-500/5 to-transparent px-6 py-4 border-b border-lime-500/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-lg bg-lime-500/10 border border-lime-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5.36 4.24l-.707.707M5.34 5.34l.707.707" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white">AI Insights</h2>
                            <p class="text-xs text-slate-400">Powered by real-time analytics</p>
                        </div>
                    </div>
                    <span
                        class="px-3 py-1 rounded-full bg-lime-500/10 border border-lime-500/20 text-xs font-medium text-lime-300">Live
                        Monitor</span>
                </div>
            </div>

            <div class="p-6">
                <div
                    class="bg-gradient-to-r from-slate-900/40 to-slate-800/40 rounded-xl p-4 border border-lime-500/10">
                    <p class="text-sm text-slate-300 leading-relaxed" id="ai-insight-text">
                        ✅ <strong>All systems green.</strong> Monitoring live finance and HRM signals.
                    </p>
                    <div class="mt-3 flex items-center gap-2 text-[11px] text-lime-300/70">
                        <span class="w-1.5 h-1.5 rounded-full bg-lime-400 animate-pulse"></span>
                        <span>Auto-updating from live data</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const hrmStats = @json($hrmStats ?? []);
    const financeData = @json($financeData ?? null);

    // Update finance data when page loads
    document.addEventListener('DOMContentLoaded', function() {
        if (financeData && financeData.kpis) {
            const kpis = financeData.kpis;
            
            // Revenue
            const revenueEl = document.getElementById('total-revenue');
            if (revenueEl && kpis.revenue) {
                revenueEl.textContent = 'NPR ' + parseFloat(kpis.revenue.total || 0).toLocaleString('en-NP', { minimumFractionDigits: 2 });
            }

            // Expenses
            const expensesEl = document.getElementById('total-expenses');
            if (expensesEl && kpis.expense) {
                expensesEl.textContent = 'NPR ' + parseFloat(kpis.expense.total || 0).toLocaleString('en-NP', { minimumFractionDigits: 2 });
            }

            // Net Profit
            const profitEl = document.getElementById('net-profit');
            const marginEl = document.getElementById('profit-margin');
            if (profitEl && kpis.profit) {
                profitEl.textContent = 'NPR ' + parseFloat(kpis.profit.net || 0).toLocaleString('en-NP', { minimumFractionDigits: 2 });
                if (marginEl) {
                    marginEl.textContent = parseFloat(kpis.profit.margin || 0).toFixed(1) + '% margin';
                }
            }

            // Pending Receivables
            const pendingEl = document.getElementById('pending-payments');
            const countEl = document.getElementById('pending-count');
            if (pendingEl && kpis.sales) {
                pendingEl.textContent = 'NPR ' + parseFloat(kpis.sales.pending || 0).toLocaleString('en-NP', { minimumFractionDigits: 2 });
            }
            if (countEl && financeData.pending_payments?.sales) {
                countEl.textContent = (financeData.pending_payments.sales.count || 0) + ' pending';
            }
        }
    });
</script>
@endpush
@endsection