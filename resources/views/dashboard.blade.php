<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Top Navigation -->
        <nav class="bg-slate-900/50 backdrop-blur-sm border-b border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo & Nav Items -->
                    <div class="flex items-center space-x-8">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-lime-400 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-slate-900" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="hidden md:flex space-x-4">
                            <a href="#"
                                class="px-4 py-2 bg-lime-400 text-slate-900 rounded-full font-semibold text-sm">Dashboard</a>
                            <a href="#"
                                class="px-4 py-2 text-slate-300 hover:text-white font-medium text-sm">Projects</a>
                            <a href="#" class="px-4 py-2 text-slate-300 hover:text-white font-medium text-sm">Knowledge
                                base</a>
                            <a href="#" class="px-4 py-2 text-slate-300 hover:text-white font-medium text-sm">Users</a>
                            <a href="#"
                                class="px-4 py-2 text-slate-300 hover:text-white font-medium text-sm">Analytics</a>
                        </div>
                    </div>

                    <!-- Right Side Actions -->
                    <div class="flex items-center space-x-4">
                        <button class="text-slate-300 hover:text-white">
                            <span class="text-sm">Invite user</span>
                        </button>
                        <button class="text-slate-300 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>
                        <div class="w-8 h-8 bg-slate-700 rounded-full"></div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Admin Access Banner (if admin) -->
            @if(Auth::user()->role === 'admin')
            <div class="mb-6 bg-gradient-to-r from-lime-500/10 to-lime-600/10 border border-lime-500/30 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-lime-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-lime-400 font-semibold">Admin Access Enabled</p>
                            <p class="text-slate-300 text-sm">You have access to the admin panel with full system
                                controls</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.dashboard') }}"
                        class="px-6 py-3 bg-lime-500 text-slate-950 rounded-lg font-semibold hover:bg-lime-400 transition flex items-center space-x-2">
                        <span>Open Admin Panel</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
            @endif

            <!-- Dashboard Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-6">Dashboard</h1>

                <!-- Stats Summary -->
                <div x-data="dashboardStats()" x-init="loadStats()" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 text-sm mb-2">Total budget of all projects</p>
                                <div class="flex items-baseline space-x-3">
                                    <h2 class="text-3xl font-bold text-white"
                                        x-text="'$' + stats.total_budget.toLocaleString()">$0</h2>
                                    <span class="text-green-400 text-sm font-semibold"
                                        x-text="'+' + stats.budget_change_week + '% week'">+0% week</span>
                                </div>
                            </div>
                            <div class="w-12 h-12 bg-slate-700 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-lime-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 text-sm mb-2">Total number of completed tasks</p>
                                <div class="flex items-baseline space-x-3">
                                    <h2 class="text-3xl font-bold text-white"
                                        x-text="stats.total_completed_tasks.toLocaleString()">0</h2>
                                    <span class="text-lime-400 text-sm font-semibold"
                                        x-text="'+' + stats.tasks_completed_today + ' today'">+0 today</span>
                                </div>
                            </div>
                            <div class="w-12 h-12 bg-slate-700 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-lime-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projects Grid -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">Projects <span class="text-slate-500 text-lg">(88)</span>
                    </h2>
                    <button
                        class="w-12 h-12 bg-slate-800 rounded-full flex items-center justify-center text-lime-400 hover:bg-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>

                <div x-data="projectGrid()" x-init="loadProjects()"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="project in projects" :key="project.id">
                        <div class="group relative bg-gradient-to-br rounded-2xl p-6 cursor-pointer transition-transform hover:scale-105 hover:shadow-2xl"
                            :style="`background: linear-gradient(135deg, ${project.category_color}CC, ${project.category_color}66)`">
                            <!-- Category Tag -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-semibold text-white/90 px-3 py-1 bg-black/20 rounded-full"
                                    x-text="'#' + project.category"></span>
                                <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </div>

                            <!-- Project Name -->
                            <h3 class="text-xl font-bold text-white mb-2" x-text="project.name"></h3>

                            <!-- Completed Tasks -->
                            <p class="text-sm text-white/80 mb-4">
                                Completed tasks: <span x-text="project.completed_tasks"></span>
                            </p>

                            <!-- Budget -->
                            <p class="text-3xl font-bold text-white mb-4"
                                x-text="'$' + project.budget.toLocaleString()"></p>

                            <!-- Team Members -->
                            <div class="flex items-center space-x-2">
                                <div class="flex -space-x-2">
                                    <template x-for="member in project.team_members.slice(0, 3)" :key="member.id">
                                        <img :src="member.avatar" :alt="member.name"
                                            class="w-8 h-8 rounded-full border-2 border-white/20" />
                                    </template>
                                </div>
                                <span x-show="project.member_count > 3"
                                    class="text-xs font-semibold text-white/90 px-2 py-1 bg-black/20 rounded-full"
                                    x-text="'+' + (project.member_count - 3)"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Projects This Year & Yearly Profit -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Projects This Year -->
                <div x-data="dashboardStats()" x-init="loadStats()"
                    class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700">
                    <h3 class="text-xl font-bold text-white mb-6">Projects this year</h3>

                    <div class="space-y-4">
                        <div>
                            <p class="text-slate-400 text-sm mb-1">Average tasks value</p>
                            <p class="text-3xl font-bold text-lime-400"
                                x-text="'$' + stats.average_tasks_value.toLocaleString()">$0</p>
                            <p class="text-xs text-slate-500 mt-1">$321,339 less than last year</p>
                        </div>

                        <div>
                            <p class="text-slate-400 text-sm mb-1">Average tasks per project</p>
                            <p class="text-3xl font-bold text-lime-400" x-text="stats.average_tasks_per_project">0</p>
                            <p class="text-xs text-slate-500 mt-1">61.4 less than last year</p>
                        </div>

                        <div>
                            <p class="text-slate-400 text-sm mb-1">New projects</p>
                            <p class="text-3xl font-bold text-lime-400" x-text="stats.new_projects_count">0</p>
                            <p class="text-xs text-slate-500 mt-1">68 less than last year</p>
                        </div>
                    </div>
                </div>

                <!-- Yearly Profit Chart -->
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700">
                    <h3 class="text-xl font-bold text-white mb-6">Yearly profit <span
                            class="text-slate-500 text-sm">(96%)</span></h3>
                    <div x-data="yearlyChart()" x-init="loadChart()" class="h-64">
                        <canvas id="yearlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function dashboardStats() {
            return {
                stats: {
                    total_budget: 0,
                    budget_change_week: 0,
                    total_completed_tasks: 0,
                    tasks_completed_today: 0,
                    average_tasks_value: 0,
                    average_tasks_per_project: 0,
                    new_projects_count: 0,
                },
                async loadStats() {
                    try {
                        const response = await fetch('/api/v1/dashboard/stats', {
                            headers: {
                                'Accept': 'application/json',
                            }
                        });
                        if (response.ok) {
                            this.stats = await response.json();
                        }
                    } catch (error) {
                        console.error('Error loading stats:', error);
                    }
                }
            }
        }

        function projectGrid() {
            return {
                projects: [],
                async loadProjects() {
                    try {
                        const response = await fetch('/api/v1/dashboard/projects', {
                            headers: {
                                'Accept': 'application/json',
                            }
                        });
                        if (response.ok) {
                            this.projects = await response.json();
                        }
                    } catch (error) {
                        console.error('Error loading projects:', error);
                    }
                }
            }
        }

        function yearlyChart() {
            return {
                chart: null,
                async loadChart() {
                    try {
                        const response = await fetch('/api/v1/dashboard/yearly-profit', {
                            headers: {
                                'Accept': 'application/json',
                            }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.renderChart(data);
                        }
                    } catch (error) {
                        console.error('Error loading chart data:', error);
                    }
                },
                renderChart(data) {
                    const ctx = document.getElementById('yearlyChart');
                    if (this.chart) {
                        this.chart.destroy();
                    }
                    this.chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.map(d => d.month),
                            datasets: [{
                                label: 'Monthly Budget',
                                data: data.map(d => d.value),
                                backgroundColor: '#a3e635',
                                borderRadius: 8,
                                barThickness: 30,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.05)'
                                    },
                                    ticks: {
                                        color: '#94a3b8'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: '#94a3b8'
                                    }
                                }
                            }
                        }
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>