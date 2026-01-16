@extends('admin.layouts.app')

@section('title', 'Leads Analytics')
@section('page-title', 'Leads Analytics')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="px-6 md:px-8 py-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-white">Leads Analytics & Insights</h2>
            <p class="text-slate-400 mt-1">Track performance, conversions, and revenue metrics</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.leads.index') }}"
                class="px-4 py-2 bg-slate-700 text-white rounded-lg font-medium hover:bg-slate-600 transition flex items-center space-x-2">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12" />
                    <polyline points="12 19 5 12 12 5" />
                </svg>
                <span>Back to Leads</span>
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Leads -->
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Total Leads</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $analytics['summary']['total_leads'] }}</p>
                    <p class="text-sm text-lime-400 mt-1">All time</p>
                </div>
                <div class="w-12 h-12 bg-lime-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-lime-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Leads -->
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Active Leads</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $analytics['summary']['active_leads'] }}</p>
                    <p class="text-sm text-yellow-400 mt-1">In progress</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Conversion Rate -->
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Conversion Rate</p>
                    <p class="text-3xl font-bold text-white mt-2">{{
                        number_format($analytics['summary']['conversion_rate'], 1) }}%</p>
                    <p class="text-sm text-emerald-400 mt-1">To positive</p>
                </div>
                <div class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Total Revenue</p>
                    <p class="text-3xl font-bold text-white mt-2">Rs. {{
                        number_format($analytics['revenue']['total_revenue'], 0) }}</p>
                    <p class="text-sm text-blue-400 mt-1">From inspections</p>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Distribution -->
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Lead Status Distribution</h3>
            <div class="h-64">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Service Breakdown -->
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Service Type Breakdown</h3>
            <div class="h-64">
                <canvas id="serviceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Monthly Trends -->
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Monthly Lead Trends</h3>
        <div class="h-80">
            <canvas id="trendsChart"></canvas>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Services -->
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Top Requested Services</h3>
            <div class="space-y-3">
                @foreach($analytics['services'] as $service)
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-white text-sm">{{ $service->service_requested }}</span>
                            <span class="text-slate-400 text-sm">{{ $service->count }} leads</span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            @php
                            $percentage = ($service->count / $analytics['summary']['total_leads']) * 100;
                            @endphp
                            <div class="bg-lime-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Staff Performance -->
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Staff Performance</h3>
            <div class="space-y-3">
                @forelse($analytics['staff_performance'] as $staff)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-lime-500/20 rounded-full flex items-center justify-center">
                            <span class="text-lime-400 font-semibold text-sm">
                                {{ strtoupper(substr($staff->assigned_to_name ?? 'U', 0, 2)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-white text-sm font-medium">{{ $staff->assigned_to_name ?? 'Unassigned' }}</p>
                            <p class="text-slate-400 text-xs">{{ $staff->total_leads }} leads assigned</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-white text-sm font-medium">{{ $staff->completed_leads }} completed</p>
                        <p class="text-slate-400 text-xs">
                            {{ $staff->total_leads > 0 ? number_format(($staff->completed_leads / $staff->total_leads) *
                            100, 1) : 0 }}% success
                        </p>
                    </div>
                </div>
                @empty
                <p class="text-slate-400 text-sm">No staff data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Revenue Metrics -->
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Revenue Metrics</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-slate-400 text-sm mb-2">Total Revenue</p>
                <p class="text-2xl font-bold text-lime-400">Rs. {{ number_format($analytics['revenue']['total_revenue'])
                    }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-sm mb-2">Average Per Lead</p>
                <p class="text-2xl font-bold text-white">Rs. {{ number_format($analytics['revenue']['average_charge'])
                    }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-sm mb-2">Highest Charge</p>
                <p class="text-2xl font-bold text-emerald-400">Rs. {{
                    number_format($analytics['revenue']['highest_charge']) }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-sm mb-2">Paid Inspections</p>
                <p class="text-2xl font-bold text-blue-400">{{ $analytics['revenue']['paid_inspections'] }}</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Chart.js default configuration
    Chart.defaults.color = '#cbd5e1';
    Chart.defaults.borderColor = '#1e293b';

    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($analytics['status_distribution']->pluck('status')) !!},
            datasets: [{
                data: {!! json_encode($analytics['status_distribution']->pluck('count')) !!},
                backgroundColor: [
                    '#84cc16', '#eab308', '#3b82f6', '#8b5cf6', 
                    '#ec4899', '#ef4444', '#f97316', '#14b8a6'
                ],
                borderWidth: 2,
                borderColor: '#0f172a'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#cbd5e1',
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // Service Type Chart
    const serviceCtx = document.getElementById('serviceChart').getContext('2d');
    new Chart(serviceCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($analytics['services']->pluck('service_requested')) !!},
            datasets: [{
                data: {!! json_encode($analytics['services']->pluck('count')) !!},
                backgroundColor: [
                    '#84cc16', '#eab308', '#3b82f6', '#8b5cf6', '#ec4899',
                    '#ef4444', '#f97316', '#14b8a6', '#06b6d4', '#6366f1'
                ],
                borderWidth: 2,
                borderColor: '#0f172a'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#cbd5e1',
                        padding: 10,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Monthly Trends Chart
    const trendsCtx = document.getElementById('trendsChart').getContext('2d');
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($analytics['monthly_trends']->pluck('month')) !!},
            datasets: [
                {
                    label: 'New Leads',
                    data: {!! json_encode($analytics['monthly_trends']->pluck('count')) !!},
                    borderColor: '#84cc16',
                    backgroundColor: 'rgba(132, 204, 22, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Completed',
                    data: {!! json_encode($analytics['monthly_trends']->pluck('completed')->map(fn($val) => $val ?? 0)) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#cbd5e1',
                        padding: 15,
                        font: {
                            size: 13
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#cbd5e1'
                    },
                    grid: {
                        color: '#1e293b'
                    }
                },
                x: {
                    ticks: {
                        color: '#cbd5e1'
                    },
                    grid: {
                        color: '#1e293b'
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection