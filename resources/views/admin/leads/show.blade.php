@extends('admin.layouts.app')

@section('title', 'View Service Lead')
@section('page-title', $lead->client_name)

@section('content')
<div class="px-6 md:px-8 py-6">
    <div class="max-w-5xl">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-slate-400 mb-2">
                <a href="{{ route('admin.leads.index') }}" class="hover:text-lime-400 transition">Service Leads</a>
                <span>/</span>
                <span class="text-white">{{ $lead->client_name }}</span>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $lead->client_name }}</h2>
                    <p class="text-slate-400 mt-1">{{ $lead->service_requested }} - {{ $lead->location }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @php
                    $statusInfo = \App\Models\LeadStatus::where('status_key', $lead->status)->first();
                    $colorClass = $statusInfo->color_class ?? 'bg-gray-50 text-gray-700 border-gray-200';
                    @endphp
                    <span class="px-3 py-2 rounded-lg border {{ $colorClass }} font-medium">
                        {{ $statusInfo->display_name ?? $lead->status }}
                    </span>
                    <a href="{{ route('admin.leads.edit', $lead) }}"
                        class="px-4 py-2 bg-lime-500 text-slate-950 rounded-lg font-medium hover:bg-lime-400 transition flex items-center space-x-2">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                        <span>Edit Lead</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Client Information -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-lime-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        Client Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Full Name</label>
                            <p class="text-white">{{ $lead->client_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Email Address</label>
                            <p class="text-white">
                                <a href="mailto:{{ $lead->email }}" class="hover:text-lime-400 transition">
                                    {{ $lead->email }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Phone Number</label>
                            <p class="text-white">
                                <a href="tel:{{ $lead->phone_number }}" class="hover:text-lime-400 transition">
                                    {{ $lead->phone_number }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Location</label>
                            <p class="text-white">{{ $lead->location }}</p>
                        </div>
                    </div>
                </div>

                <!-- Service Details -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-lime-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M3 12h18" />
                            <path d="M3 6h18" />
                            <path d="M3 18h18" />
                        </svg>
                        Service Details
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Service Requested</label>
                            <p class="text-white">{{ $lead->service_requested }}</p>
                        </div>

                        @if($lead->message)
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Client Message</label>
                            <p class="text-white">{{ $lead->message }}</p>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($lead->project_size)
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-1">Project Size</label>
                                <p class="text-white">{{ $lead->project_size }}</p>
                            </div>
                            @endif

                            @if($lead->timeline)
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-1">Timeline</label>
                                <p class="text-white">{{ $lead->timeline }}</p>
                            </div>
                            @endif

                            @if($lead->budget)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-400 mb-1">Budget</label>
                                <p class="text-white">{{ $lead->budget }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Inspection Details -->
                @if($lead->inspection_date || $lead->inspection_time || $lead->inspection_charge)
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Inspection Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($lead->inspection_date)
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Date</label>
                            <p class="text-white">{{ $lead->inspection_date->format('F j, Y') }}</p>
                        </div>
                        @endif

                        @if($lead->inspection_time)
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Time</label>
                            <p class="text-white">{{ $lead->inspection_time->format('h:i A') }}</p>
                        </div>
                        @endif

                        @if($lead->inspection_charge)
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Charge</label>
                            <p class="text-lime-400 font-semibold">NPR {{ number_format($lead->inspection_charge, 2) }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Activity Timeline -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Activity Timeline
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-10 h-10 bg-lime-500/20 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-white font-medium">Lead Created</p>
                                <p class="text-sm text-slate-400">
                                    Created by {{ $lead->createdBy->name ?? 'System' }} on {{
                                    $lead->created_at->format('M
                                    d, Y h:i A') }}
                                </p>
                            </div>
                        </div>

                        @if($lead->updated_at != $lead->created_at)
                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-white font-medium">Lead Updated</p>
                                <p class="text-sm text-slate-400">
                                    Last updated {{ $lead->updated_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        @endif

                        @if($lead->inspection_assigned_to)
                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-10 h-10 bg-purple-500/20 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-white font-medium">Assigned</p>
                                <p class="text-sm text-slate-400">
                                    Assigned to {{ $lead->assignedTo->name }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>

                    <div class="space-y-2">
                        <!-- Change Status -->
                        <form action="{{ route('admin.leads.update-status', $lead) }}" method="POST" class="space-y-2">
                            @csrf
                            @method('PATCH')
                            <label class="block text-sm font-medium text-slate-300">Change Status</label>
                            <select name="status"
                                class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                                @foreach(\App\Models\LeadStatus::getAllActive() as $status)
                                <option value="{{ $status->status_key }}" {{ $lead->status == $status->status_key ?
                                    'selected' : '' }}>
                                    {{ $status->display_name }}
                                </option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="w-full px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Update Status
                            </button>
                        </form>

                        <!-- Assign -->
                        <form action="{{ route('admin.leads.assign', $lead) }}" method="POST" class="space-y-2 pt-4">
                            @csrf
                            @method('PATCH')
                            <label class="block text-sm font-medium text-slate-300">Assign To</label>
                            <select name="assigned_to"
                                class="w-full px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                                <option value="">Unassigned</option>
                                @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}" {{ $lead->inspection_assigned_to == $user->id ?
                                    'selected' :
                                    '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="w-full px-3 py-2 text-sm bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                Assign Lead
                            </button>
                        </form>

                        <!-- Delete -->
                        <form action="{{ route('admin.leads.destroy', $lead) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this lead? This action cannot be undone.');"
                            class="pt-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Delete Lead</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Lead Info -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Lead Information</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Status</label>
                            <span class="px-2 py-1 text-xs rounded-md border {{ $colorClass }}">
                                {{ $statusInfo->display_name ?? $lead->status }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Assigned To</label>
                            <p class="text-white">{{ $lead->assignedTo->name ?? 'Unassigned' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Created By</label>
                            <p class="text-white">{{ $lead->createdBy->name ?? 'System' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Created At</label>
                            <p class="text-white text-sm">{{ $lead->created_at->format('M d, Y h:i A') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Last Updated</label>
                            <p class="text-white text-sm">{{ $lead->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection