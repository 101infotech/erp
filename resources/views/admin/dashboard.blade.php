@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="px-6 md:px-8 space-y-12">
    <!-- ========== HEADER SECTION ========== -->
    <div class="mb-12">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-1">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="text-slate-400 text-sm">{{ now()->format('l, F d, Y') }} • Here's what's happening with
                    your
                    business today.</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500 mb-1">System Status</p>
                <p class="text-sm font-semibold text-green-400">✓ All Systems Operational</p>
            </div>
        </div>
    </div>

    <!-- ========== KEY PERFORMANCE INDICATORS ========== -->
    <div class="mb-12">
        <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-3">Key Metrics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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

            <!-- Team Members -->
            <div
                class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-3 border border-slate-700 hover:border-lime-500/50 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs mb-1.5">Team Members</p>
                        <h2 class="text-2xl font-bold text-white">{{ $hrmStats['total_employees'] ??
                            $stats['total_team_members'] }}</h2>
                        <p class="text-xs text-slate-500 mt-1">Total employees ({{ $hrmStats['active_employees']
                            ?? 0 }}
                            active)</p>
                    </div>
                    <div class="w-10 h-10 bg-lime-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
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
                        <p class="text-xs text-slate-500 mt-1">Published articles</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
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
                        <p class="text-xs text-slate-500 mt-1">Last 7 days</p>
                    </div>
                    <div class="w-10 h-10 bg-orange-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== BUSINESS SUMMARY SECTION ========== -->
        <div class="mb-12">
            <x-dashboard-section-header title="Business Summary" subtitle="Finance and HR snapshots"
                action="{{ route('admin.finance.dashboard') }}" actionLabel="View Finance Dashboard" />
            <div class="flex items-center gap-3 text-xs font-medium">
                <a href="{{ route('admin.finance.dashboard') }}"
                    class="text-lime-400 hover:text-lime-300 inline-flex items-center gap-2">Finance Dashboard
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <span class="text-slate-700 dark:text-slate-600">|</span>
                <a href="{{ route('admin.hrm.employees.index') }}"
                    class="text-blue-300 hover:text-blue-200 inline-flex items-center gap-2">HRM Hub
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Finance Summary -->
            <div class="lg:col-span-2 bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-slate-400 text-xs">Finance snapshot</p>
                        <h3 class="text-base font-semibold text-white">This fiscal year</h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
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
                        <p class="text-yellow-400 text-xs" id="pending-count">0 invoices pending</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="rounded-lg bg-slate-900/40 border border-cyan-500/20 p-3">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="text-xs text-cyan-200/80">Receivables</p>
                                <h4 class="text-lg font-semibold text-white" id="pending-sales-total">NPR 0</h4>
                                <p class="text-[11px] text-slate-400" id="pending-sales-count">Pending invoices
                                </p>
                            </div>
                            <span
                                class="text-[11px] px-2 py-1 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20">Sales</span>
                        </div>
                        <div id="pending-sales-list" class="space-y-2 text-xs text-slate-300">
                            <p class="text-slate-500">Loading receivables...</p>
                        </div>
                    </div>

                    <div class="rounded-lg bg-slate-900/40 border border-orange-500/20 p-3">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="text-xs text-orange-200/80">Payables</p>
                                <h4 class="text-lg font-semibold text-white" id="pending-purchases-total">NPR 0
                                </h4>
                                <p class="text-[11px] text-slate-400" id="pending-purchases-count">Pending
                                    vendor bills
                                </p>
                            </div>
                            <span
                                class="text-[11px] px-2 py-1 rounded-full bg-orange-500/10 text-orange-300 border border-orange-500/20">Purchases</span>
                        </div>
                        <div id="pending-purchases-list" class="space-y-2 text-xs text-slate-300">
                            <p class="text-slate-500">Loading payables...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HRM Summary -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-slate-400 text-xs">HRM snapshot</p>
                        <h3 class="text-base font-semibold text-white">People health</h3>
                    </div>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('admin.hrm.dashboard') ? route('admin.hrm.dashboard') : route('admin.hrm.employees.index') }}"
                        class="text-xs text-lime-400 hover:text-lime-300 font-medium">View HRM</a>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-700/40">
                        <div>
                            <p class="text-xs text-slate-400">Active employees</p>
                            <h4 class="text-lg font-semibold text-white" id="hr-active">{{
                                $hrmStats['active_employees']
                                ?? ($hrmStats['total_employees'] ?? 0) }}</h4>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full bg-lime-500/10 text-lime-300">HRM</span>
                    </div>
                    <div
                        class="flex items-center justify-between p-3 rounded-lg bg-yellow-500/5 border border-yellow-500/20">
                        <div>
                            <p class="text-xs text-yellow-200">Pending leaves</p>
                            <h4 class="text-lg font-semibold text-white" id="hr-pending-leaves">{{
                                $hrmStats['pending_leaves'] ?? 0 }}</h4>
                        </div>
                        <a href="{{ route('admin.hrm.leaves.index') }}"
                            class="text-xs text-yellow-300 hover:text-yellow-200">Review</a>
                    </div>
                    <div
                        class="flex items-center justify-between p-3 rounded-lg bg-blue-500/5 border border-blue-500/20">
                        <div>
                            <p class="text-xs text-blue-200">Draft payrolls</p>
                            <h4 class="text-lg font-semibold text-white" id="hr-draft-payrolls">{{
                                $hrmStats['draft_payrolls'] ?? 0 }}</h4>
                        </div>
                        <a href="{{ route('admin.hrm.payroll.index') }}"
                            class="text-xs text-blue-300 hover:text-blue-200">Open</a>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-red-500/5 border border-red-500/20">
                        <div>
                            <p class="text-xs text-red-200">Attendance flags</p>
                            <h4 class="text-lg font-semibold text-white" id="hr-anomalies">{{
                                $hrmStats['unreviewed_anomalies'] ?? 0 }}</h4>
                        </div>
                        <a href="{{ route('admin.hrm.attendance.index') }}"
                            class="text-xs text-red-300 hover:text-red-200">Investigate</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Finance Quick Actions -->
            <div class="lg:col-span-2 space-y-4">
                <x-dashboard-section-header title="Quick Actions" subtitle="Finance operations" />
                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700">
                    <x-dashboard-quick-action title="New Transaction" subtitle="Add entry" color="cyan"
                        href="{{ route('admin.finance.transactions.create') }}">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </x-dashboard-quick-action>

                    <x-dashboard-quick-action title="View Reports" subtitle="Analytics" color="purple"
                        href="{{ route('admin.finance.reports') }}">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </x-dashboard-quick-action>

                    <x-dashboard-quick-action title="New Sale" subtitle="Create invoice" color="green"
                        href="{{ route('admin.finance.sales.create') }}">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </x-dashboard-quick-action>

                    <x-dashboard-quick-action title="New Purchase" subtitle="Record expense" color="orange"
                        href="{{ route('admin.finance.purchases.create') }}">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </x-dashboard-quick-action>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="space-y-4 mt-6 lg:mt-10">
                <x-dashboard-card title="Recent Transactions" subtitle="Latest activity" icon='<svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>' color="cyan" action="{{ route('admin.finance.transactions.index') }}"
                    actionLabel="View All">
                    <div id="recent-transactions" class="space-y-3">
                        <p class="text-slate-400 text-center py-4 text-sm">Loading...</p>
                    </div>
                </x-dashboard-card>
            </div>
        </div>

        <!-- ========== LEADS CENTER SECTION ========== -->
        <div class="mb-12">
            <x-dashboard-section-header title="Leads Center" subtitle="Manage and track sales leads"
                action="{{ route('admin.leads.dashboard') }}" actionLabel="View Full Leads Dashboard" />
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
                <div id="leads-dashboard-container" class="min-h-96">
                    <p class="text-slate-400 text-center py-8">Loading Leads Module...</p>
                </div>
            </div>
        </div>

        <!-- ========== QUICK ACCESS MODULES ========== -->
        <div class="mb-12">
            <x-dashboard-section-header title="Quick Access" subtitle="Jump to modules" />
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- Leads Management -->
                <a href="{{ route('admin.leads.dashboard') }}"
                    class="group bg-gradient-to-br from-rose-500/20 to-rose-600/10 rounded-xl p-4 cursor-pointer transition-all hover:scale-105 border border-rose-500/20 hover:border-rose-500/40">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-semibold text-rose-300">Leads Center</span>
                        <svg class="w-4 h-4 text-rose-400 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                    <p class="text-xs text-slate-400">Track sales pipeline</p>
                    <p class="text-rose-300 text-xs mt-2 font-medium" id="leads-count">Loading...</p>
                </a>

                <!-- Sites Management -->
                <a href="{{ route('admin.sites.index') }}"
                    class="group bg-gradient-to-br from-blue-500/20 to-blue-600/10 rounded-xl p-4 cursor-pointer transition-all hover:scale-105 border border-blue-500/20 hover:border-blue-500/40">
                    <div class="flex items-center justify-between mb-3">
                        <span
                            class="text-xs font-semibold text-blue-300 px-2.5 py-1 bg-black/20 rounded-full">#Management</span>
                        <svg class="w-4 h-4 text-blue-300 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Sites</h3>
                    <p class="text-xs text-blue-200/80">Manage websites</p>
                </a>

                <!-- HRM Module -->
                <a href="{{ route('admin.hrm.employees.index') }}"
                    class="group bg-gradient-to-br from-lime-500/20 to-lime-600/10 rounded-xl p-4 cursor-pointer transition-all hover:scale-105 border border-lime-500/20 hover:border-lime-500/40">
                    <div class="flex items-center justify-between mb-3">
                        <span
                            class="text-xs font-semibold text-lime-300 px-2.5 py-1 bg-black/20 rounded-full">#HRM</span>
                        <svg class="w-4 h-4 text-lime-300 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">HR Management</h3>
                    <p class="text-xs text-lime-200/80">Employees & payroll</p>
                </a>

                <!-- Attendance -->
                <a href="{{ route('admin.hrm.attendance.index') }}"
                    class="group bg-gradient-to-br from-purple-500/20 to-purple-600/10 rounded-xl p-4 cursor-pointer transition-all hover:scale-105 border border-purple-500/20 hover:border-purple-500/40">
                    <div class="flex items-center justify-between mb-3">
                        <span
                            class="text-xs font-semibold text-purple-300 px-2.5 py-1 bg-black/20 rounded-full">#Tracking</span>
                        <svg class="w-4 h-4 text-purple-300 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Attendance</h3>
                    <p class="text-xs text-purple-200/80">Track employee time</p>
                </a>

                <!-- Content -->
                <a href="{{ route('admin.sites.index') }}"
                    class="group bg-gradient-to-br from-indigo-500/20 to-indigo-600/10 rounded-xl p-4 cursor-pointer transition-all hover:scale-105 border border-indigo-500/20 hover:border-indigo-500/40">
                    <div class="flex items-center justify-between mb-3">
                        <span
                            class="text-xs font-semibold text-indigo-300 px-2.5 py-1 bg-black/20 rounded-full">#Content</span>
                        <svg class="w-4 h-4 text-indigo-300 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Content</h3>
                    <p class="text-xs text-indigo-200/80">Blogs & media</p>
                </a>

            </div>
        </div>

        <!-- ========== PENDING ACTIONS (Conditional) ========== -->
        @if(isset($hrmStats) && ($hrmStats['pending_leaves'] > 0 || $hrmStats['draft_payrolls'] > 0 ||
        $hrmStats['unreviewed_anomalies'] > 0))
        <div class="mb-12">
            <x-dashboard-section-header title="Pending Actions" subtitle="Requires your attention" />
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                @if($hrmStats['pending_leaves'] > 0)
                <a href="{{ route('admin.hrm.leaves.index') }}?status=pending"
                    class="bg-yellow-500/10 rounded-xl p-4 border border-yellow-500/30 hover:border-yellow-500/50 transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-yellow-400">{{ $hrmStats['pending_leaves']
                            }}</span>
                    </div>
                    <h3 class="text-base font-semibold text-white">Pending Leave Requests</h3>
                </a>
                @endif

                @if($hrmStats['draft_payrolls'] > 0)
                <a href="{{ route('admin.hrm.payroll.index') }}?status=draft"
                    class="bg-slate-500/10 rounded-xl p-4 border border-blue-500/30 hover:border-blue-500/50 transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-slate-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-blue-400">{{ $hrmStats['draft_payrolls'] }}</span>
                    </div>
                    <h3 class="text-base font-semibold text-white">Draft Payrolls</h3>
                </a>
                @endif

                @if($hrmStats['unreviewed_anomalies'] > 0)
                <a href="{{ route('admin.hrm.attendance.index') }}"
                    class="bg-red-500/10 rounded-xl p-4 border border-red-500/30 hover:border-red-500/50 transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-red-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-red-400">{{ $hrmStats['unreviewed_anomalies']
                            }}</span>
                    </div>
                    <h3 class="text-base font-semibold text-white">Attendance Anomalies</h3>
                </a>
                @endif

            </div>
        </div>
        @endif

        <!-- ========== AI INSIGHTS SECTION (Full Width) ========== -->
        <div class="mb-6">
            <div
                class="bg-gradient-to-r from-lime-950/20 via-slate-900 to-lime-950/20 rounded-2xl border border-lime-500/20 overflow-hidden backdrop-blur-sm">
                <!-- Header -->
                <div class="bg-gradient-to-r from-lime-500/5 to-transparent px-6 py-4 border-b border-lime-500/10">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg bg-lime-500/10 border border-lime-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
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

                <!-- Content -->
                <div class="p-6 space-y-5">
                    <!-- Main Insight -->
                    <div
                        class="bg-gradient-to-r from-slate-900/40 to-slate-800/40 rounded-xl p-4 border border-lime-500/10">
                        <p class="text-sm text-slate-300 leading-relaxed" id="ai-insight-text">
                            Analyzing finance and HRM signals...
                        </p>
                        <div class="mt-3 flex items-center gap-2 text-[11px] text-lime-300/70">
                            <span class="w-1.5 h-1.5 rounded-full bg-lime-400 animate-pulse"></span>
                            <span>Auto-updating from live data</span>
                        </div>
                    </div>

                    <!-- AI Chat Interface -->
                    <div class="space-y-3">
                        <label class="text-xs font-medium text-slate-400 block">Ask AI for
                            Recommendations</label>
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <input type="text" id="ai-query-input"
                                    class="w-full bg-slate-900/60 border border-slate-700/50 rounded-lg px-4 py-3 text-sm text-white placeholder-slate-500 focus:border-lime-500/50 focus:ring-1 focus:ring-lime-500/20 transition"
                                    placeholder="e.g., Where should we focus spending? What's the revenue trend?">
                                <div
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-600 text-xs pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </div>
                            </div>
                            <button type="button" id="ai-send-btn"
                                class="px-5 py-3 rounded-lg bg-gradient-to-r from-lime-500 to-lime-600 text-slate-950 text-sm font-semibold hover:from-lime-400 hover:to-lime-500 transition-all duration-200 hover:shadow-lg hover:shadow-lime-500/20 flex items-center gap-2">
                                <span>Send</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
                            <svg class="w-3 h-3 text-slate-600" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                            </svg>
                            Analyzes finance trends, HRM metrics, and provides actionable recommendations in
                            real-time
                        </p>
                    </div>

                    <!-- Quick Insights Grid (Optional) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-700/30">
                        <button
                            class="group text-left p-3 rounded-lg bg-slate-900/40 border border-slate-700/50 hover:border-lime-500/30 hover:bg-slate-900/60 transition">
                            <p class="text-xs font-medium text-slate-400 group-hover:text-lime-300 mb-1">
                                Performance</p>
                            <p class="text-xs text-slate-500 group-hover:text-slate-400">How's our cash flow?
                            </p>
                        </button>
                        <button
                            class="group text-left p-3 rounded-lg bg-slate-900/40 border border-slate-700/50 hover:border-lime-500/30 hover:bg-slate-900/60 transition">
                            <p class="text-xs font-medium text-slate-400 group-hover:text-lime-300 mb-1">
                                Efficiency</p>
                            <p class="text-xs text-slate-500 group-hover:text-slate-400">Where are we spending
                                most?</p>
                        </button>
                        <button
                            class="group text-left p-3 rounded-lg bg-slate-900/40 border border-slate-700/50 hover:border-lime-500/30 hover:bg-slate-900/60 transition">
                            <p class="text-xs font-medium text-slate-400 group-hover:text-lime-300 mb-1">Growth
                            </p>
                            <p class="text-xs text-slate-500 group-hover:text-slate-400">Next opportunities?</p>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        const hrmStats = @json($hrmStats ?? []);
    const financeData = @json($financeData ?? null);

    function updateAiInsight(finance, hrm) {
        const target = document.getElementById('ai-insight-text');
        if (!target) return;

        const insights = [];

        if (finance.net_profit > 0) {
            insights.push(`💰 Net profit up: <strong>NPR ${parseFloat(finance.net_profit).toLocaleString('en-NP', { maximumFractionDigits: 0 })}</strong> — maintain current spending patterns.`);
        } else if (finance.net_profit < 0) {
            insights.push('⚠️ <strong>Net loss detected</strong> — Review expenses immediately and optimize operational costs.');
        }

        if (finance.pending_receivables_count > 0) {
            insights.push(`📊 <strong>${finance.pending_receivables_count} invoices pending</strong> (NPR ${parseFloat(finance.pending_receivables || 0).toLocaleString('en-NP', { maximumFractionDigits: 0 })}). Prioritize collections.`);
        }

        if (hrm.pending_leaves > 0) {
            insights.push(`👤 <strong>${hrm.pending_leaves} leave requests</strong> awaiting your approval.`);
        }
        if (hrm.unreviewed_anomalies > 0) {
            insights.push('🔔 <strong>Attendance anomalies</strong> need quick review to maintain team accountability.');
        }

        if (!insights.length) {
            insights.push('✅ <strong>All systems green.</strong> Monitoring live finance and HRM signals. Everything is running smoothly!');
        }

        target.innerHTML = insights.join(' • ');
    }

    // Load Finance Data from server-side
    document.addEventListener('DOMContentLoaded', function() {
        if (financeData && financeData.kpis) {
            const kpis = financeData.kpis;
            
            // Update Total Revenue
            const revenueEl = document.getElementById('total-revenue');
            if (revenueEl) {
                const revenueTotal = kpis.revenue?.total ?? 0;
                revenueEl.textContent = 'NPR ' + parseFloat(revenueTotal).toLocaleString('en-NP', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Update Total Expenses
            const expensesEl = document.getElementById('total-expenses');
            if (expensesEl) {
                const expenseTotal = kpis.expense?.total ?? 0;
                expensesEl.textContent = 'NPR ' + parseFloat(expenseTotal).toLocaleString('en-NP', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Update Net Profit & Margin
            const profitEl = document.getElementById('net-profit');
            const marginEl = document.getElementById('profit-margin');
            const netProfit = kpis.profit?.net ?? 0;
            const profitMargin = kpis.profit?.margin ?? 0;
            if (profitEl) {
                profitEl.textContent = 'NPR ' + parseFloat(netProfit).toLocaleString('en-NP', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
            if (marginEl) {
                marginEl.textContent = parseFloat(profitMargin).toFixed(1) + '% margin';
            }

            // Update Pending Receivables using Sales Pending total
            const pendingEl = document.getElementById('pending-payments');
            const countEl = document.getElementById('pending-count');
            const pendingTotal = kpis.sales?.pending ?? 0;
            if (pendingEl) {
                pendingEl.textContent = 'NPR ' + parseFloat(pendingTotal).toLocaleString('en-NP', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
            
            // Update count from pending_payments if available
            if (financeData.pending_payments?.sales?.count !== undefined) {
                countEl && (countEl.textContent = financeData.pending_payments.sales.count + ' invoices pending');
            } else if (countEl) {
                countEl.textContent = 'Pending invoices';
            }

            // Receivables detail
            const salesSummary = financeData.pending_payments?.sales;
            if (salesSummary) {
                const salesTotalEl = document.getElementById('pending-sales-total');
                const salesCountEl = document.getElementById('pending-sales-count');
                salesTotalEl && (salesTotalEl.textContent = 'NPR ' + parseFloat(salesSummary.total || 0).toLocaleString('en-NP', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                salesCountEl && (salesCountEl.textContent = (salesSummary.count ?? 0) + ' invoices pending');

                const salesListEl = document.getElementById('pending-sales-list');
                if (salesListEl) {
                    if (Array.isArray(salesSummary.items) && salesSummary.items.length) {
                        salesListEl.innerHTML = salesSummary.items.map(item => `
                            <div class="flex items-center justify-between border border-slate-800/70 rounded-lg px-3 py-2 bg-slate-900/60">
                                <div>
                                    <p class="text-white font-medium">${item.customer || item.customer_name || 'Customer'}</p>
                                    <p class="text-[11px] text-slate-400">${item.number || item.invoice_number || 'Invoice'} • ${item.date || 'N/A'}</p>
                                </div>
                                <span class="text-cyan-300 font-semibold">NPR ${parseFloat(item.amount || 0).toLocaleString('en-NP', { minimumFractionDigits: 2 })}</span>
                            </div>
                        `).join('');
                    } else {
                        salesListEl.innerHTML = '<p class="text-slate-500">All receivables cleared.</p>';
                    }
                }
            }

            // Payables detail
            const purchaseSummary = financeData.pending_payments?.purchases;
            if (purchaseSummary) {
                const purchTotalEl = document.getElementById('pending-purchases-total');
                const purchCountEl = document.getElementById('pending-purchases-count');
                purchTotalEl && (purchTotalEl.textContent = 'NPR ' + parseFloat(purchaseSummary.total || 0).toLocaleString('en-NP', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                purchCountEl && (purchCountEl.textContent = (purchaseSummary.count ?? 0) + ' vendor bills pending');

                const purchListEl = document.getElementById('pending-purchases-list');
                if (purchListEl) {
                    if (Array.isArray(purchaseSummary.items) && purchaseSummary.items.length) {
                        purchListEl.innerHTML = purchaseSummary.items.map(item => `
                            <div class="flex items-center justify-between border border-slate-800/70 rounded-lg px-3 py-2 bg-slate-900/60">
                                <div>
                                    <p class="text-white font-medium">${item.vendor || item.vendor_name || 'Vendor'}</p>
                                    <p class="text-[11px] text-slate-400">${item.number || item.bill_number || 'Bill'} • ${item.date || 'N/A'}</p>
                                </div>
                                <span class="text-orange-300 font-semibold">NPR ${parseFloat(item.amount || 0).toLocaleString('en-NP', { minimumFractionDigits: 2 })}</span>
                            </div>
                        `).join('');
                    } else {
                        purchListEl.innerHTML = '<p class="text-slate-500">No pending payables.</p>';
                    }
                }
            }

            // Update AI insight with mapped values
            updateAiInsight({
                net_profit: netProfit || 0,
                profit_margin: profitMargin || 0,
                pending_receivables: pendingTotal || 0,
                pending_receivables_count: financeData.pending_payments?.sales?.count || 0
            }, hrmStats);

            // Populate Recent Transactions
            if (Array.isArray(financeData.recent_transactions)) {
                const transactionsEl = document.getElementById('recent-transactions');
                if (transactionsEl) {
                    const items = financeData.recent_transactions;
                    if (items.length > 0) {
                        transactionsEl.innerHTML = items.map(txn => `
                            <div class="flex items-center justify-between pb-3 border-b border-slate-700/50 last:border-0">
                                <div class="flex-1">
                                    <p class="font-medium text-white">${txn.description || 'Transaction'}</p>
                                    <p class="text-xs text-slate-500">${txn.date || 'N/A'}</p>
                                </div>
                                <span class="font-semibold ${txn.type === 'income' ? 'text-green-400' : 'text-red-400'}">
                                    ${txn.type === 'income' ? '+' : '-'} NPR ${parseFloat(txn.amount || 0).toLocaleString('en-NP', { minimumFractionDigits: 2 })}
                                </span>
                            </div>
                        `).join('');
                    } else {
                        transactionsEl.innerHTML = '<p class="text-slate-400 text-center py-4 text-sm">No recent transactions</p>';
                    }
                }
            }
        } else {
            // No finance data available
            updateAiInsight({
                net_profit: 0,
                profit_margin: 0,
                pending_receivables: 0,
                pending_receivables_count: 0
            }, hrmStats);

            const salesList = document.getElementById('pending-sales-list');
            if (salesList) {
                salesList.innerHTML = '<p class="text-slate-500">Connect finance module to view live receivables.</p>';
            }
            const purchasesList = document.getElementById('pending-purchases-list');
            if (purchasesList) {
                purchasesList.innerHTML = '<p class="text-slate-500">Connect finance module to view live payables.</p>';
            }
        }

        // AI Send Button Handler
        const sendBtn = document.getElementById('ai-send-btn');
        const queryInput = document.getElementById('ai-query-input');
        if (sendBtn && queryInput) {
            sendBtn.addEventListener('click', function() {
                const query = queryInput.value.trim();
                if (query) {
                    console.log('AI Query:', query);
                    // Placeholder for AI API integration
                    queryInput.value = '';
                    queryInput.focus();
                }
            });
            
            queryInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && queryInput.value.trim()) {
                    sendBtn.click();
                }
            });
        }

        // Load Leads Summary
        async function loadLeadsSummary() {
            try {
                const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
                const headers = {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                };
                
                if (token) {
                    headers['Authorization'] = 'Bearer ' + token;
                }

                const response = await fetch('/api/leads/enhanced/special/statistics', {
                    headers: headers
                });

                if (response.ok) {
                    const data = await response.json();
                    const leadsCountEl = document.getElementById('leads-count');
                    if (leadsCountEl && data.total_leads) {
                        leadsCountEl.textContent = `📊 ${data.total_leads} Total Leads • ${data.open_leads} Open`;
                    }
                } else if (response.status === 401) {
                    console.log('Auth token may be invalid');
                    const leadsCountEl = document.getElementById('leads-count');
                    if (leadsCountEl) {
                        leadsCountEl.textContent = '🔒 Login required';
                    }
                }
            } catch (error) {
                console.error('Error loading leads summary:', error);
                const leadsCountEl = document.getElementById('leads-count');
                if (leadsCountEl) {
                    leadsCountEl.textContent = '⚠️ Unable to load';
                }
            }
        }

        // Load leads summary when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadLeadsSummary();
        });

        // Refresh every 30 seconds
        setInterval(loadLeadsSummary, 30000);
    });
    </script>
    @endpush
    @endsection