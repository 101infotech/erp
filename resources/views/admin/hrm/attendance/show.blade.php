@extends('admin.layouts.app')

@section('title', 'Attendance Details')
@section('page-title', 'Attendance Details - ' . \Carbon\Carbon::parse($attendance->date)->format('F d, Y'))

@section('content')
<div class="px-6 md:px-8 py-6">
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header Card -->
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg">
            <div
                class="px-6 py-4 border-b border-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-white">{{ $attendance->employee->full_name }}</h2>
                    <p class="text-sm text-slate-400">{{ $attendance->employee->code }} • {{
                        $attendance->employee->email }}
                    </p>
                </div>
                <a href="{{ route('admin.hrm.attendance.index') }}"
                    class="px-3 py-1.5 text-sm border border-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-700">
                <div class="px-6 py-4">
                    <div class="text-xs font-medium text-slate-400 uppercase tracking-wide">Date</div>
                    <div class="mt-2 text-lg font-semibold text-white">{{
                        \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</div>
                    <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="text-xs font-medium text-slate-400 uppercase tracking-wide">Tracked Hours</div>
                    <div class="mt-2 text-lg font-semibold text-blue-400">{{ number_format($attendance->tracked_hours,
                        2)
                        }}h</div>
                </div>
                <div class="px-6 py-4">
                    <div class="text-xs font-medium text-slate-400 uppercase tracking-wide">Payroll Hours</div>
                    <div class="mt-2 text-lg font-semibold text-green-400">{{ number_format($attendance->payroll_hours,
                        2)
                        }}h</div>
                </div>
                <div class="px-6 py-4">
                    <div class="text-xs font-medium text-slate-400 uppercase tracking-wide">Overtime</div>
                    <div class="mt-2 text-lg font-semibold text-orange-400">{{
                        number_format($attendance->overtime_hours, 2)
                        }}h</div>
                </div>
            </div>
        </div>

        <!-- Time Entries Timeline -->
        @if($attendance->timeEntries->count() > 0)
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg">
            <div class="px-6 py-4 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-white">Time Entries</h3>
                <p class="text-sm text-slate-400 mt-1">Clock in/out timeline for this day</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($attendance->timeEntries as $entry)
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            @if($entry->type === 'In')
                            <div
                                class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center border border-green-500/30">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </div>
                            @else
                            <div
                                class="w-12 h-12 rounded-full bg-red-500/20 flex items-center justify-center border border-red-500/30">
                                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                                </svg>
                            </div>
                            @endif
                        </div>
                        <div class="flex-1 bg-slate-900/50 rounded-lg p-4 border border-slate-700">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $entry->type === 'In' ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                                            Clock {{ $entry->type }}
                                        </span>
                                        <span class="text-lg font-bold text-white">{{ $entry->local_time->format('h:i
                                            A')
                                            }}</span>
                                        <span class="text-sm text-slate-500">{{ $entry->local_time->format('T')
                                            }}</span>
                                    </div>

                                    @if($entry->project_name || $entry->activity_name)
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @if($entry->project_name)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z">
                                                </path>
                                            </svg>
                                            {{ $entry->project_name }}
                                        </span>
                                        @endif
                                        @if($entry->activity_name)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                </path>
                                            </svg>
                                            {{ $entry->activity_name }}
                                        </span>
                                        @endif
                                    </div>
                                    @endif

                                    @if($entry->note)
                                    <div class="mt-2 flex items-start gap-2">
                                        <svg class="w-4 h-4 text-slate-500 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                                            </path>
                                        </svg>
                                        <p class="text-sm text-slate-300">{{ $entry->note }}</p>
                                    </div>
                                    @endif

                                    @if($entry->address || ($entry->latitude && $entry->longitude))
                                    <div class="mt-2 flex items-start gap-2">
                                        <svg class="w-4 h-4 text-slate-500 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <p class="text-sm text-slate-300">
                                            @if($entry->address)
                                            {{ $entry->address }}
                                            @else
                                            {{ $entry->latitude }}, {{ $entry->longitude }}
                                            @endif
                                        </p>
                                    </div>
                                    @endif
                                </div>
                                <div class="text-right text-xs text-slate-500">
                                    <div>{{ $entry->local_time->format('M d, Y') }}</div>
                                    <div class="mt-1">{{ $entry->time->diffForHumans() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Additional Information -->
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg">
            <div class="px-6 py-4 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-white">Additional Information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-xs font-medium text-slate-400 uppercase tracking-wide">Source</label>
                    <div class="mt-1">
                        <span
                            class="px-2.5 py-1 text-sm font-medium rounded-md {{ $attendance->source === 'jibble' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : 'bg-slate-700 text-slate-300 border border-slate-600' }}">
                            {{ ucfirst($attendance->source) }}
                        </span>
                    </div>
                </div>

                @if($attendance->notes)
                <div>
                    <label class="text-xs font-medium text-slate-400 uppercase tracking-wide">Notes</label>
                    <p class="mt-1 text-sm text-slate-300">{{ $attendance->notes }}</p>
                </div>
                @endif

                @if($attendance->timeEntries->count() === 0 && $attendance->jibble_data)
                <div>
                    <label class="text-xs font-medium text-slate-400 uppercase tracking-wide">Raw Jibble Data</label>
                    <pre
                        class="mt-2 bg-slate-900/50 border border-slate-700 rounded-lg p-4 text-xs text-slate-300 overflow-x-auto">{{ json_encode($attendance->jibble_data, JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endsection