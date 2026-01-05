@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 -m-8 p-8">
    <!-- Welcome Section -->
    <div class="mb-6 mt-6">
        <h1 class="text-2xl font-semibold text-white mb-1">Welcome back, {{ Auth::user()->name }}!</h1>
        <p class="text-slate-400 text-sm">Here's what's happening with your business today.</p>
    </div>

    <!-- Top Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Sites -->
        <div
            class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 hover:border-blue-500/50 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs mb-1.5">Total Sites</p>
                    <h2 class="text-2xl font-bold text-white">{{ $stats['total_sites'] }}</h2>
                    <p class="text-xs text-slate-500 mt-1">Active websites</p>
                </div>
                <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Team Members -->
        <div
            class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 hover:border-lime-500/50 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs mb-1.5">Team Members</p>
                    <h2 class="text-2xl font-bold text-white">{{ $stats['total_team_members'] }}</h2>
                    <p class="text-xs text-slate-500 mt-1">Total employees</p>
                </div>
                <div class="w-10 h-10 bg-lime-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Blogs -->
        <div
            class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 hover:border-yellow-500/50 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs mb-1.5">Total Blogs</p>
                    <h2 class="text-2xl font-bold text-white">{{ $stats['total_blogs'] }}</h2>
                    <p class="text-xs text-slate-500 mt-1">Published articles</p>
                </div>
                <div class="w-10 h-10 bg-yellow-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- New Contacts -->
        <div
            class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 hover:border-orange-500/50 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs mb-1.5">New Contacts</p>
                    <h2 class="text-2xl font-bold text-white">{{ $stats['new_contact_forms'] }}</h2>
                    <p class="text-xs text-slate-500 mt-1">Last 7 days</p>
                </div>
                <div class="w-10 h-10 bg-orange-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Finance Overview -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-white">Finance Overview</h2>
            <a href="{{ route('admin.finance.dashboard') }}"
                class="text-lime-400 hover:text-lime-300 text-xs font-medium flex items-center gap-2">
                View Full Dashboard
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- Total Revenue -->
            <div class="bg-gradient-to-br from-green-500/20 to-green-600/10 rounded-xl p-4 border border-green-500/30">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
                <p class="text-green-300 text-xs mb-1.5">Total Revenue</p>
                <h2 class="text-2xl font-bold text-white mb-1" id="total-revenue">रू 0</h2>
                <p class="text-green-400 text-xs" id="revenue-growth">+0% vs last month</p>
            </div>

            <!-- Total Expenses -->
            <div class="bg-gradient-to-br from-red-500/20 to-red-600/10 rounded-xl p-4 border border-red-500/30">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                        </svg>
                    </div>
                </div>
                <p class="text-red-300 text-xs mb-1.5">Total Expenses</p>
                <h2 class="text-2xl font-bold text-white mb-1" id="total-expenses">रू 0</h2>
                <p class="text-red-400 text-xs" id="expense-growth">+0% vs last month</p>
            </div>

            <!-- Net Profit -->
            <div class="bg-gradient-to-br from-blue-500/20 to-blue-600/10 rounded-xl p-4 border border-blue-500/30">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-blue-300 text-xs mb-1.5">Net Profit</p>
                <h2 class="text-2xl font-bold text-white mb-1" id="net-profit">रू 0</h2>
                <p class="text-blue-400 text-xs" id="profit-margin">0% margin</p>
            </div>

            <!-- Pending Payments -->
            <div
                class="bg-gradient-to-br from-yellow-500/20 to-yellow-600/10 rounded-xl p-4 border border-yellow-500/30">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-yellow-300 text-xs mb-1.5">Pending Payments</p>
                <h2 class="text-2xl font-bold text-white mb-1" id="pending-payments">रू 0</h2>
                <p class="text-yellow-400 text-xs" id="pending-count">0 invoices pending</p>
            </div>
        </div>

        <!-- Finance Quick Actions & Recent Transactions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Quick Actions -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700">
                <h3 class="text-base font-semibold text-white mb-3">Finance Quick Actions</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.finance.transactions.create') }}"
                        class="flex items-center gap-3 p-3 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition">
                        <div class="w-10 h-10 bg-cyan-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium text-sm">New Transaction</p>
                            <p class="text-slate-400 text-xs">Add entry</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.finance.reports') }}"
                        class="flex items-center gap-3 p-3 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition">
                        <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium text-sm">Reports</p>
                            <p class="text-slate-400 text-xs">View analytics</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.finance.sales.create') }}"
                        class="flex items-center gap-3 p-3 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition">
                        <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium text-sm">New Sale</p>
                            <p class="text-slate-400 text-xs">Create invoice</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.finance.purchases.create') }}"
                        class="flex items-center gap-3 p-3 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition">
                        <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium text-sm">New Purchase</p>
                            <p class="text-slate-400 text-xs">Record expense</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-white">Recent Transactions</h3>
                    <a href="{{ route('admin.finance.transactions.index') }}"
                        class="text-lime-400 hover:text-lime-300 text-xs font-medium">View All →</a>
                </div>
                <div id="recent-transactions" class="space-y-3">
                    <p class="text-slate-400 text-center py-4 text-sm">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Modules -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-white mb-4">Quick Access</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

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
                <h3 class="text-lg font-bold text-white mb-1">Sites</h3>
                <p class="text-xs text-blue-200/80">Manage websites</p>
            </a>

            <!-- HRM Module -->
            <a href="{{ route('admin.hrm.employees.index') }}"
                class="group bg-gradient-to-br from-lime-500/20 to-lime-600/10 rounded-xl p-4 cursor-pointer transition-all hover:scale-105 border border-lime-500/20 hover:border-lime-500/40">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-lime-300 px-2.5 py-1 bg-black/20 rounded-full">#HRM</span>
                    <svg class="w-4 h-4 text-lime-300 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">HR Management</h3>
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
                <h3 class="text-lg font-bold text-white mb-1">Attendance</h3>
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
                <h3 class="text-lg font-bold text-white mb-1">Content</h3>
                <p class="text-xs text-indigo-200/80">Blogs & media</p>
            </a>

        </div>
    </div>

    <!-- HRM Alerts (only if there are pending items) -->
    @if(isset($hrmStats) && ($hrmStats['pending_leaves'] > 0 || $hrmStats['draft_payrolls'] > 0 ||
    $hrmStats['unreviewed_anomalies'] > 0))
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-white mb-4">Pending Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            @if($hrmStats['pending_leaves'] > 0)
            <a href="{{ route('admin.hrm.leaves.index') }}?status=pending"
                class="bg-yellow-500/10 rounded-xl p-4 border border-yellow-500/30 hover:border-yellow-500/50 transition group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-yellow-400">{{ $hrmStats['pending_leaves'] }}</span>
                </div>
                <h3 class="text-base font-semibold text-white">Pending Leave Requests</h3>
            </a>
            @endif

            @if($hrmStats['draft_payrolls'] > 0)
            <a href="{{ route('admin.hrm.payroll.index') }}?status=draft"
                class="bg-blue-500/10 rounded-xl p-4 border border-blue-500/30 hover:border-blue-500/50 transition group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
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
                    <div class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-red-400">{{ $hrmStats['unreviewed_anomalies'] }}</span>
                </div>
                <h3 class="text-base font-semibold text-white">Attendance Anomalies</h3>
            </a>
            @endif

        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
    // Load Finance Data Comprehensively
    document.addEventListener('DOMContentLoaded', function() {
        const companyId = 1; // First company
        const fiscalYear = '2081'; // Current BS year

        // Fetch KPIs (includes revenue, expenses, profit, margin)
        fetch(`/api/v1/finance/dashboard/kpis?company_id=${companyId}&fiscal_year=${fiscalYear}`, {
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    // Update Total Revenue
                    const revenueEl = document.getElementById('total-revenue');
                    if (revenueEl) {
                        revenueEl.textContent = 'रू ' + parseFloat(data.data.total_revenue || 0).toLocaleString(
                            'en-NP', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                    }

                    // Update Total Expenses
                    const expensesEl = document.getElementById('total-expenses');
                    if (expensesEl) {
                        expensesEl.textContent = 'रू ' + parseFloat(data.data.total_expenses || 0).toLocaleString(
                            'en-NP', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                    }

                    // Update Net Profit
                    const profitEl = document.getElementById('net-profit');
                    const marginEl = document.getElementById('profit-margin');
                    if (profitEl) {
                        profitEl.textContent = 'रू ' + parseFloat(data.data.net_profit || 0).toLocaleString('en-NP', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                    if (marginEl && data.data.profit_margin) {
                        marginEl.textContent = parseFloat(data.data.profit_margin).toFixed(1) + '% margin';
                    }

                    // Update Pending Payments
                    const pendingEl = document.getElementById('pending-payments');
                    const countEl = document.getElementById('pending-count');
                    if (pendingEl && data.data.pending_receivables) {
                        pendingEl.textContent = 'रू ' + parseFloat(data.data.pending_receivables).toLocaleString(
                            'en-NP', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                    }
                    if (countEl && data.data.pending_receivables_count) {
                        countEl.textContent = data.data.pending_receivables_count + ' invoices pending';
                    }
                }
            })
            .catch(error => console.error('Error loading finance KPIs:', error));

        // Fetch Recent Transactions
        fetch(`/api/v1/finance/transactions?company_id=${companyId}&fiscal_year=${fiscalYear}&limit=5`, {
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.length > 0) {
                    const transactionsEl = document.getElementById('recent-transactions');
                    if (transactionsEl) {
                        transactionsEl.innerHTML = data.data.map(txn => `
                            <div class="flex items-center justify-between pb-3 border-b border-slate-700/50 last:border-0">
                                <div class="flex-1">
                                    <p class="font-medium text-white">${txn.description || 'Transaction'}</p>
                                    <p class="text-xs text-slate-500">${txn.transaction_date_bs || 'N/A'}</p>
                                </div>
                                <span class="font-semibold ${txn.transaction_type === 'income' ? 'text-green-400' : 'text-red-400'}">
                                    ${txn.transaction_type === 'income' ? '+' : '-'} रू ${parseFloat(txn.amount || 0).toLocaleString('en-NP', { minimumFractionDigits: 2 })}
                                </span>
                            </div>
                        `).join('');
                    }
                } else {
                    const transactionsEl = document.getElementById('recent-transactions');
                    if (transactionsEl) {
                        transactionsEl.innerHTML =
                            '<p class="text-slate-400 text-center py-4 text-sm">No recent transactions</p>';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading transactions:', error);
                const transactionsEl = document.getElementById('recent-transactions');
                if (transactionsEl) {
                    transactionsEl.innerHTML = '<p class="text-slate-400 text-center py-4 text-sm">Error loading transactions</p>';
                }
            });
    });
</script>
@endpush

@endsection