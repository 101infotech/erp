@extends('admin.layouts.app')

@section('title', 'Finance Dashboard')
@section('page-title', 'Finance Dashboard')

@section('content')
<div class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 -m-8 p-8 min-h-screen">
    <!-- Company Selector & Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Finance Dashboard</h1>
                <p class="text-slate-400">Comprehensive financial overview and analytics</p>
            </div>
            <div class="flex items-center gap-4">
                <select id="company-filter"
                    class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:border-lime-500 focus:ring-1 focus:ring-lime-500">
                    <option value="">All Companies</option>
                    <!-- Will be populated dynamically -->
                </select>
                <input type="date" id="date-from"
                    class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:border-lime-500 focus:ring-1 focus:ring-lime-500">
                <input type="date" id="date-to"
                    class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:border-lime-500 focus:ring-1 focus:ring-lime-500">
            </div>
        </div>
    </div>

    <!-- Finance KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue Card -->
        <div class="bg-gradient-to-br from-green-500/20 to-green-600/10 rounded-2xl p-6 border border-green-500/30">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-green-300 px-3 py-1 bg-black/20 rounded-full">Revenue</span>
            </div>
            <h3 class="text-sm text-green-300 mb-2">Total Revenue</h3>
            <div class="flex items-baseline space-x-2">
                <h2 class="text-3xl font-bold text-white" id="kpi-revenue">रू 0</h2>
            </div>
            <p class="text-green-400 text-xs mt-2" id="kpi-revenue-growth">+0% vs last month</p>
        </div>

        <!-- Total Expenses Card -->
        <div class="bg-gradient-to-br from-red-500/20 to-red-600/10 rounded-2xl p-6 border border-red-500/30">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-red-300 px-3 py-1 bg-black/20 rounded-full">Expenses</span>
            </div>
            <h3 class="text-sm text-red-300 mb-2">Total Expenses</h3>
            <div class="flex items-baseline space-x-2">
                <h2 class="text-3xl font-bold text-white" id="kpi-expenses">रू 0</h2>
            </div>
            <p class="text-red-400 text-xs mt-2" id="kpi-expense-growth">+0% vs last month</p>
        </div>

        <!-- Net Profit Card -->
        <div class="bg-gradient-to-br from-blue-500/20 to-blue-600/10 rounded-2xl p-6 border border-blue-500/30">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-blue-300 px-3 py-1 bg-black/20 rounded-full">Profit</span>
            </div>
            <h3 class="text-sm text-blue-300 mb-2">Net Profit</h3>
            <div class="flex items-baseline space-x-2">
                <h2 class="text-3xl font-bold text-white" id="kpi-profit">रू 0</h2>
            </div>
            <p class="text-blue-400 text-xs mt-2" id="kpi-profit-margin">0% margin</p>
        </div>

        <!-- Pending Payments Card -->
        <div class="bg-gradient-to-br from-yellow-500/20 to-yellow-600/10 rounded-2xl p-6 border border-yellow-500/30">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-yellow-300 px-3 py-1 bg-black/20 rounded-full">Pending</span>
            </div>
            <h3 class="text-sm text-yellow-300 mb-2">Pending Payments</h3>
            <div class="flex items-baseline space-x-2">
                <h2 class="text-3xl font-bold text-white" id="kpi-pending">रू 0</h2>
            </div>
            <p class="text-yellow-400 text-xs mt-2" id="kpi-pending-count">0 invoices</p>
        </div>
    </div>

    <!-- Quick Actions & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Quick Actions -->
        <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700">
            <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.finance.transactions.create') }}"
                    class="flex items-center gap-3 p-3 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition group">
                    <div
                        class="w-10 h-10 bg-lime-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-medium text-sm">New Transaction</p>
                        <p class="text-slate-400 text-xs">Record a transaction</p>
                    </div>
                </a>

                <a href="{{ route('admin.finance.sales.create') }}"
                    class="flex items-center gap-3 p-3 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition group">
                    <div
                        class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-medium text-sm">New Sale/Invoice</p>
                        <p class="text-slate-400 text-xs">Create invoice</p>
                    </div>
                </a>

                <a href="{{ route('admin.finance.purchases.create') }}"
                    class="flex items-center gap-3 p-3 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition group">
                    <div
                        class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-medium text-sm">New Purchase/Bill</p>
                        <p class="text-slate-400 text-xs">Record expense</p>
                    </div>
                </a>

                <a href="{{ route('admin.finance.reports') }}"
                    class="flex items-center gap-3 p-3 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition group">
                    <div
                        class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-medium text-sm">View Reports</p>
                        <p class="text-slate-400 text-xs">Financial reports</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="lg:col-span-2 bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Recent Transactions</h3>
                <a href="{{ route('admin.finance.transactions.index') }}"
                    class="text-lime-400 hover:text-lime-300 text-sm font-medium">View All →</a>
            </div>
            <div id="dashboard-recent-transactions" class="space-y-3">
                <p class="text-slate-400 text-center py-8">Loading transactions...</p>
            </div>
        </div>
    </div>

    <!-- Revenue Trends Chart -->
    <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700 mb-8">
        <h3 class="text-lg font-semibold text-white mb-4">Revenue vs Expenses (Last 6 Months)</h3>
        <div id="revenue-chart" class="h-64 flex items-center justify-center text-slate-400">
            Loading chart...
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load Finance KPIs
        loadKPIs();

        // Load Recent Transactions
        loadRecentTransactions();

        // Load Companies for filter
        loadCompanies();
    });

    function loadKPIs() {
        fetch('/api/v1/finance/dashboard/kpis', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const kpis = data.data;
                document.getElementById('kpi-revenue').textContent = 'रू ' + Number(kpis.total_revenue || 0).toLocaleString('en-NP');
                document.getElementById('kpi-revenue-growth').textContent = (kpis.revenue_growth >= 0 ? '+' : '') + kpis.revenue_growth + '% vs last month';
                document.getElementById('kpi-expenses').textContent = 'रू ' + Number(kpis.total_expenses || 0).toLocaleString('en-NP');
                document.getElementById('kpi-expense-growth').textContent = (kpis.expense_growth >= 0 ? '+' : '') + kpis.expense_growth + '% vs last month';
                document.getElementById('kpi-profit').textContent = 'रू ' + Number(kpis.net_profit || 0).toLocaleString('en-NP');
                document.getElementById('kpi-profit-margin').textContent = Number(kpis.profit_margin || 0).toFixed(1) + '% margin';
                document.getElementById('kpi-pending').textContent = 'रू ' + Number(kpis.pending_sales_amount || 0).toLocaleString('en-NP');
                document.getElementById('kpi-pending-count').textContent = (kpis.pending_sales_count || 0) + ' invoices';
            }
        })
        .catch(error => console.error('Error loading KPIs:', error));
    }

    function loadRecentTransactions() {
        fetch('/api/v1/finance/transactions?page=1&per_page=5', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('dashboard-recent-transactions');
            if (data.success && data.data && data.data.length > 0) {
                container.innerHTML = data.data.map(txn => {
                    const isCredit = txn.type === 'credit';
                    return `
                        <div class="flex items-center justify-between p-3 bg-slate-700/30 rounded-lg hover:bg-slate-700/50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 ${isCredit ? 'bg-green-500/20' : 'bg-red-500/20'} rounded-lg flex items-center justify-center">
                                    <span class="${isCredit ? 'text-green-400' : 'text-red-400'} font-bold text-lg">${isCredit ? '↓' : '↑'}</span>
                                </div>
                                <div>
                                    <p class="text-white text-sm font-medium">${txn.description || 'Transaction'}</p>
                                    <p class="text-slate-400 text-xs">${txn.transaction_date_bs || txn.transaction_date}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="${isCredit ? 'text-green-400' : 'text-red-400'} font-semibold">रू ${Number(txn.amount).toLocaleString('en-NP')}</p>
                                <p class="text-slate-500 text-xs">${txn.account_name || ''}</p>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                container.innerHTML = '<p class="text-slate-400 text-center py-8">No transactions found</p>';
            }
        })
        .catch(error => {
            console.error('Error loading transactions:', error);
            document.getElementById('dashboard-recent-transactions').innerHTML = '<p class="text-red-400 text-center py-8">Failed to load transactions</p>';
        });
    }

    function loadCompanies() {
        fetch('/api/v1/finance/companies?page=1&per_page=100', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const select = document.getElementById('company-filter');
                data.data.forEach(company => {
                    const option = document.createElement('option');
                    option.value = company.id;
                    option.textContent = company.name;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading companies:', error));
    }
</script>
@endpush
@endsection