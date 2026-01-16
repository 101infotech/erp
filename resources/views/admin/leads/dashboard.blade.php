@extends('admin.layouts.app')

@section('title', 'Leads Dashboard')
@section('page-title', 'Leads Dashboard')

@section('content')
<div class="px-6 md:px-8 py-6 space-y-8">
    <!-- ========== HEADER SECTION ========== -->
    <div>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-1">Leads Center</h1>
                <p class="text-slate-400 text-sm">Manage and track your sales pipeline</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.leads.create') }}"
                    class="px-4 py-2 bg-rose-500 text-white rounded-lg font-medium hover:bg-rose-600 transition inline-flex items-center gap-2">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    New Lead
                </a>
                <a href="{{ route('admin.leads.index') }}"
                    class="px-4 py-2 bg-slate-700 text-white rounded-lg font-medium hover:bg-slate-600 transition">
                    View All Leads
                </a>
            </div>
        </div>
    </div>

    <!-- ========== STATISTICS CARDS ========== -->
    <div class="mb-8">
        <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-3">Key Metrics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Leads -->
            <div
                class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 hover:border-rose-500/50 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs mb-1.5">Total Leads</p>
                        <h2 class="text-2xl font-bold text-white" id="total-leads">{{ $analytics['total_leads'] ?? 0
                            }}</h2>
                        <p class="text-xs text-slate-500 mt-1">All time</p>
                    </div>
                    <div class="w-10 h-10 bg-rose-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" />
                            <polyline points="10 12 12 14 14 12" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Leads -->
            <div
                class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 hover:border-blue-500/50 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs mb-1.5">Active Leads</p>
                        <h2 class="text-2xl font-bold text-white" id="active-leads">{{ $analytics['active_leads'] ??
                            0 }}</h2>
                        <p class="text-xs text-slate-500 mt-1">In progress</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="16 12 12 8 8 12" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Positive Clients -->
            <div
                class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 hover:border-green-500/50 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs mb-1.5">Positive Clients</p>
                        <h2 class="text-2xl font-bold text-white" id="positive-leads">{{
                            $analytics['positive_clients'] ?? 0 }}</h2>
                        <p class="text-xs text-slate-500 mt-1">Converted</p>
                    </div>
                    <div class="w-10 h-10 bg-green-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Conversion Rate -->
            <div
                class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 hover:border-purple-500/50 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs mb-1.5">Conversion Rate</p>
                        <h2 class="text-2xl font-bold text-white" id="conversion-rate">{{
                            $analytics['conversion_rate'] ?? 0 }}%</h2>
                        <p class="text-xs text-slate-500 mt-1">Success rate</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 17" />
                            <polyline points="17 6 23 6 23 12" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== MAIN CONTENT ========== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Status Distribution -->
        <div class="lg:col-span-2 bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Status Distribution</h3>
            </div>
            <div class="space-y-3" id="status-distribution">
                <p class="text-slate-400 text-center py-8">Loading...</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
            <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.leads.create') }}"
                    class="block px-4 py-3 bg-rose-500/10 border border-rose-500/30 rounded-lg hover:bg-rose-500/20 transition text-rose-300 font-medium text-sm flex items-center space-x-2">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    <span>New Lead</span>
                </a>
                <a href="{{ route('admin.leads.index') }}"
                    class="block px-4 py-3 bg-blue-500/10 border border-blue-500/30 rounded-lg hover:bg-blue-500/20 transition text-blue-300 font-medium text-sm flex items-center space-x-2">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="8" y1="6" x2="21" y2="6" />
                        <line x1="8" y1="12" x2="21" y2="12" />
                        <line x1="8" y1="18" x2="21" y2="18" />
                        <line x1="3" y1="6" x2="3.01" y2="6" />
                        <line x1="3" y1="12" x2="3.01" y2="12" />
                        <line x1="3" y1="18" x2="3.01" y2="18" />
                    </svg>
                    <span>View All Leads</span>
                </a>
                <a href="{{ route('admin.leads.analytics') }}"
                    class="block px-4 py-3 bg-green-500/10 border border-green-500/30 rounded-lg hover:bg-green-500/20 transition text-green-300 font-medium text-sm flex items-center space-x-2">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="2" x2="12" y2="22" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                    <span>Full Analytics</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ========== ADDITIONAL STATS ========== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Revenue Stats -->
        <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Revenue</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-slate-400 text-xs mb-1">Total Revenue</p>
                    <p class="text-2xl font-bold text-green-400">Rs. {{
                        number_format($analytics['revenue']['total_revenue'] ?? 0) }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs mb-1">Average Charge</p>
                    <p class="text-xl font-bold text-slate-300">Rs. {{
                        number_format($analytics['revenue']['average_charge'] ?? 0) }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs mb-1">Paid Inspections</p>
                    <p class="text-xl font-bold text-slate-300">{{ $analytics['revenue']['paid_inspections'] ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Lead Sources -->
        <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Top Sources</h3>
            <div class="space-y-2 text-sm" id="lead-sources">
                <p class="text-slate-400 text-center py-4">Loading...</p>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Recent Leads</h3>
            <div class="space-y-2 text-sm" id="recent-leads">
                <p class="text-slate-400 text-center py-4">Loading...</p>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
    // Display status distribution
    const statusDistribution = @json($analytics['status_distribution'] ?? []);
    const statusContainer = document.getElementById('status-distribution');
    const totalLeads = @json($analytics['total_leads'] ?? 0);
    
    if (statusDistribution && statusDistribution.length > 0) {
        statusContainer.innerHTML = statusDistribution.map(item => `
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/50 last:border-0">
                <div>
                    <p class="font-medium text-white">${item.status || 'Unknown'}</p>
                    <p class="text-xs text-slate-500">${item.count} leads</p>
                </div>
                <div class="text-right">
                    <span class="text-lg font-bold text-slate-300">${item.count}</span>
                    <p class="text-xs text-slate-500">${totalLeads > 0 ? ((item.count / totalLeads) * 100).toFixed(1) : 0}%</p>
                </div>
            </div>
        `).join('');
    } else {
        statusContainer.innerHTML = '<p class="text-slate-400 text-center py-4">No data available</p>';
    }

    // Display lead sources (from services)
    const services = @json($analytics['services'] ?? []);
    const sourcesContainer = document.getElementById('lead-sources');
    
    if (services && services.length > 0) {
        sourcesContainer.innerHTML = services.slice(0, 5).map(item => `
            <div class="flex items-center justify-between py-2 border-b border-slate-700/30 last:border-0">
                <span class="text-slate-300">${item.service_requested || 'Unknown'}</span>
                <span class="font-semibold text-rose-400">${item.count}</span>
            </div>
        `).join('');
    } else {
        sourcesContainer.innerHTML = '<p class="text-slate-400 text-center py-2 text-sm">No data available</p>';
    }

    // Display recent leads (from status distribution, get first few active)
    const recentLeads = @json($analytics['recent_leads'] ?? []) || [];
    const recentContainer = document.getElementById('recent-leads');
    
    if (recentLeads && recentLeads.length > 0) {
        recentContainer.innerHTML = recentLeads.slice(0, 5).map(lead => `
            <div class="flex items-center justify-between py-2 border-b border-slate-700/30 last:border-0">
                <div class="flex-1">
                    <p class="text-white font-medium truncate">${lead.client_name || 'No Name'}</p>
                    <p class="text-xs text-slate-500">${lead.service_requested || 'Service'}</p>
                </div>
            </div>
        `).join('');
    } else {
        recentContainer.innerHTML = '<p class="text-slate-400 text-center py-2 text-sm">No data available</p>';
    }
</script>
@endpush
@endsection