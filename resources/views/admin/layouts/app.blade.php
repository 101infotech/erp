<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Saubhagya Group</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('styles')
</head>

<body class="bg-slate-950">
    <style>
        /* Minimal scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.3);
            border-radius: 2px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.5);
        }

        /* Firefox */
        .sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.3) transparent;
        }
    </style>
    <div class="min-h-screen flex">
        <!-- Sidebar - Fixed/Sticky -->
        <aside
            class="sidebar-scroll fixed left-0 top-0 h-screen w-64 bg-slate-900 border-r border-slate-800 overflow-y-auto z-40">
            <!-- Logo/Brand -->
            <div class="p-4 border-b border-slate-800">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-lime-500 to-lime-600 flex items-center justify-center">
                        <span class="text-slate-950 text-lg font-bold">S</span>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-white">Saubhagya ERP</div>
                        <div class="text-xs text-slate-400">Admin Panel</div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->is('dashboard') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.sites.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.sites.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                        </path>
                    </svg>
                    Sites
                </a>

                <div class="pt-4 mt-4 border-t border-slate-800">
                    <p class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">HRM Module</p>

                    <!-- Who is Clocked In -->
                    <a href="{{ route('admin.hrm.attendance.active-users') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.hrm.attendance.active-users') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                            <circle cx="12" cy="12" r="4" fill="currentColor" class="text-lime-400" />
                        </svg>
                        Who is Clocked In
                    </a>

                    <!-- Team Management -->
                    <a href="{{ route('admin.hrm.employees.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.hrm.employees.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        Team Management
                    </a>

                    <!-- Users -->
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        Users
                    </a>

                    <!-- Organization (Companies & Departments) -->
                    <a href="{{ route('admin.hrm.organization.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.hrm.organization.*') || request()->routeIs('admin.hrm.companies.*') || request()->routeIs('admin.hrm.departments.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        Organization
                    </a>

                    <!-- Payroll -->
                    <a href="{{ route('admin.hrm.payroll.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.hrm.payroll.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Payroll
                    </a>

                    <div class="pt-2 pb-1">
                        <p class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Leave
                            Management
                        </p>
                        <!-- Leave Requests -->
                        <a href="{{ route('admin.hrm.leaves.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.hrm.leaves.*') && !request()->routeIs('admin.hrm.leave-policies.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            Leave Requests
                        </a>
                        <!-- Leave Policies -->
                        <a href="{{ route('admin.hrm.leave-policies.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.hrm.leave-policies.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Leave Policies
                        </a>
                    </div>

                    <div class="pt-2 pb-1">
                        <p class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Communication
                        </p>
                        <!-- Weekly Feedback -->
                        <a href="{{ route('admin.feedback.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.feedback.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Weekly Feedback
                        </a>
                        <!-- Staff Feedback (Complaint Box) -->
                        <a href="{{ route('admin.complaints.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.complaints.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                            Staff Feedback
                        </a>
                        <!-- Announcements -->
                        <a href="{{ route('admin.announcements.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.announcements.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                            Announcements
                        </a>
                    </div>

                    <div class="pt-2 pb-1">
                        <p class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Finance
                            Module
                        </p>
                        <!-- Finance Dashboard -->
                        <a href="{{ route('admin.finance.dashboard') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.finance.dashboard') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            Finance Dashboard
                        </a>
                        <!-- Companies -->
                        <a href="{{ route('admin.finance.companies.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.finance.companies.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            Companies
                        </a>
                        <!-- Accounts -->
                        <a href="{{ route('admin.finance.accounts.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.finance.accounts.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                </path>
                            </svg>
                            Accounts
                        </a>
                        <!-- Transactions -->
                        <a href="{{ route('admin.finance.transactions.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.finance.transactions.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4">
                                </path>
                            </svg>
                            Transactions
                        </a>
                        <!-- Sales -->
                        <a href="{{ route('admin.finance.sales.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.finance.sales.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6">
                                </path>
                            </svg>
                            Sales
                        </a>
                        <!-- Purchases -->
                        <a href="{{ route('admin.finance.purchases.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.finance.purchases.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            Purchases
                        </a>
                        <!-- Customers -->
                        <a href="{{ route('admin.finance.customers.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.finance.customers.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            Customers
                        </a>
                        <!-- Vendors -->
                        <a href="{{ route('admin.finance.vendors.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.finance.vendors.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            Vendors
                        </a>
                        <!-- Budgets -->
                        <a href="{{ route('admin.finance.budgets.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.finance.budgets.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                            Budgets
                        </a>
                        <!-- Recurring Expenses -->
                        <a href="{{ route('admin.finance.recurring-expenses.index') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.finance.recurring-expenses.*') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Recurring Expenses
                        </a>
                        <!-- Reports -->
                        <a href="{{ route('admin.finance.reports') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.finance.reports') ? 'bg-lime-500/10 text-lime-400' : 'text-slate-300 hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Reports
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 ml-64">
            <!-- Header -->
            <header class="bg-slate-900 border-b border-slate-800">
                <div class="flex justify-between items-center px-8 py-4">
                    <h2 class="text-xl font-semibold text-white">@yield('page-title')</h2>
                    <div class="flex items-center space-x-4">
                        <!-- Nepali Date Display -->
                        <div
                            class="flex items-center gap-2 px-4 py-2 bg-slate-800/50 rounded-lg border border-lime-500/30">
                            <svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div class="flex flex-col">
                                <span class="text-xs text-slate-400">Nepali Date</span>
                                <span class="text-sm font-semibold text-lime-300">{{ nepali_date() }}</span>
                            </div>
                        </div>

                        <button class="p-2 text-slate-400 hover:text-slate-200 rounded-lg hover:bg-slate-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                        <x-notification-bell />
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="text-sm font-medium text-slate-300 hover:text-lime-400 px-3 py-2 rounded-lg hover:bg-slate-800">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-8">
                @if(session('success'))
                <div class="mb-6 bg-lime-900/20 border border-lime-700 text-lime-300 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="mb-6 bg-red-900/20 border border-red-700 text-red-300 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>

</html>