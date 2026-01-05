@extends('admin.layouts.app')

@section('title', 'Service Leads')
@section('page-title', 'Service Leads Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
    .dataTables_wrapper {
        color: #cbd5e1;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #cbd5e1 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #84cc16 !important;
        color: #0f172a !important;
        border-color: #84cc16 !important;
    }

    table.dataTable thead th {
        background-color: #1e293b;
        color: #f1f5f9;
        border-bottom: 2px solid #334155;
    }

    table.dataTable tbody tr {
        background-color: #0f172a;
        border-bottom: 1px solid #1e293b;
    }

    table.dataTable tbody tr:hover {
        background-color: #1e293b;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white">Service Leads</h2>
            <p class="text-slate-400 mt-1">Manage and track service requests for inspections, construction, and
                renovation projects</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.leads.analytics') }}"
                class="px-4 py-2 bg-slate-700 text-white rounded-lg font-medium hover:bg-slate-600 transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Analytics</span>
            </a>
            <a href="{{ route('admin.leads.create') }}"
                class="px-4 py-2 bg-lime-500 text-slate-950 rounded-lg font-medium hover:bg-lime-400 transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>New Lead</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by client, location, or service..."
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">

            <select name="status"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                <option value="">All Status</option>
                @foreach($statuses as $status)
                <option value="{{ $status->status_key }}" {{ request('status')==$status->status_key ? 'selected' : ''
                    }}>
                    {{ $status->display_name }}
                </option>
                @endforeach
            </select>

            <select name="assigned_to"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                <option value="">All Assigned</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('assigned_to')==$user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
                @endforeach
            </select>

            <button type="submit"
                class="px-4 py-2 text-sm bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-medium transition">
                Apply Filters
            </button>
        </div>
    </form>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Total Leads</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $leads->total() }}</p>
                </div>
                <div class="w-10 h-10 bg-lime-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        @php
        $pendingCount = \App\Models\ServiceLead::whereIn('status', ['Intake', 'Contacted', 'Inspection
        Booked'])->count();
        $completedCount = \App\Models\ServiceLead::whereIn('status', ['Positive', 'Reports Sent'])->count();
        $todayCount = \App\Models\ServiceLead::whereDate('inspection_date', today())->count();
        @endphp

        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Pending</p>
                    <p class="text-2xl font-bold text-yellow-400 mt-1">{{ $pendingCount }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Completed</p>
                    <p class="text-2xl font-bold text-emerald-400 mt-1">{{ $completedCount }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Today's Inspections</p>
                    <p class="text-2xl font-bold text-blue-400 mt-1">{{ $todayCount }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table id="leadsTable" class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left">Client</th>
                        <th class="px-4 py-3 text-left">Service</th>
                        <th class="px-4 py-3 text-left">Location</th>
                        <th class="px-4 py-3 text-left">Inspection Date</th>
                        <th class="px-4 py-3 text-left">Charge</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Assigned To</th>
                        <th class="px-4 py-3 text-left">Created</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td class="px-4 py-3">
                            <div>
                                <div class="font-medium text-white">{{ $lead->client_name }}</div>
                                <div class="text-xs text-slate-400">{{ $lead->email }}</div>
                                <div class="text-xs text-slate-400">{{ $lead->phone_number }}</div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-300">{{ $lead->service_requested }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ Str::limit($lead->location, 30) }}</td>
                        <td class="px-4 py-3">
                            @if($lead->inspection_date)
                            <div class="text-white">{{ $lead->inspection_date->format('M d, Y') }}</div>
                            @if($lead->inspection_time)
                            <div class="text-xs text-slate-400">{{ $lead->inspection_time->format('h:i A') }}</div>
                            @endif
                            @else
                            <span class="text-slate-500">Not scheduled</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($lead->inspection_charge)
                            <span class="text-lime-400 font-medium">NPR {{ number_format($lead->inspection_charge, 2) }}</span>
                            @else
                            <span class="text-slate-500">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                            $statusInfo = $statuses->firstWhere('status_key', $lead->status);
                            $colorClass = $statusInfo->color_class ?? 'bg-gray-50 text-gray-700 border-gray-200';
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-md border {{ $colorClass }}">
                                {{ $statusInfo->display_name ?? $lead->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-300">
                            {{ $lead->assignedTo->name ?? 'Unassigned' }}
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-xs">
                            {{ $lead->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.leads.show', $lead) }}"
                                    class="text-blue-400 hover:text-blue-300 transition" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.leads.edit', $lead) }}"
                                    class="text-lime-400 hover:text-lime-300 transition" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.leads.destroy', $lead) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this lead?');"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 transition"
                                        title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                            No leads found. <a href="{{ route('admin.leads.create') }}"
                                class="text-lime-400 hover:text-lime-300">Create your first lead</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($leads->hasPages())
        <div class="px-4 py-3 border-t border-slate-700">
            {{ $leads->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Only initialize DataTables if table has data rows
        if ($('#leadsTable tbody tr').length > 0 && !$('#leadsTable tbody tr td').first().attr('colspan')) {
            $('#leadsTable').DataTable({
                pageLength: 20,
                paging: false,
                searching: false,
                info: false,
                order: [[7, 'desc']], // Sort by created date
                columnDefs: [
                    { orderable: false, targets: [8] } // Disable sorting on actions column
                ]
            });
        }
    });
</script>
@endpush