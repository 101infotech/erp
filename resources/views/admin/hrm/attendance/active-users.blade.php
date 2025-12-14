@extends('admin.layouts.app')

@section('title', 'Who is Logged In')
@section('page-title', 'Currently Active Employees')

@section('content')
<div class="mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
        <div>
            <h2 class="text-2xl font-bold text-white">Who is Logged In (Jibble)</h2>
            <p class="text-slate-400 mt-1">Employees currently clocked in on Jibble</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button id="refresh-btn" onclick="refreshActiveUsers()"
                class="px-3 py-1.5 text-sm bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 transition flex items-center gap-1.5">
                <svg id="refresh-icon" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                <span id="refresh-text">Refresh</span>
            </button>
            <a href="{{ route('admin.hrm.attendance.index') }}"
                class="px-3 py-1.5 text-sm bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition">
                Back to Attendance
            </a>
        </div>
    </div>
</div>

<div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
    <!-- Loading State -->
    <div id="loading-state" class="hidden p-12 text-center">
        <svg class="animate-spin h-12 w-12 mx-auto mb-4 text-lime-400" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <p class="text-slate-400 text-lg">Fetching active employees...</p>
    </div>

    <!-- Error State -->
    @if(!empty($error))
    <div id="error-state" class="p-12 text-center">
        <svg class="w-16 h-16 mx-auto mb-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-red-400 text-lg mb-2">{{ $error }}</p>
        <p class="text-slate-500 text-sm mb-4">There was an error connecting to Jibble API</p>
        <button onclick="refreshActiveUsers()"
            class="px-4 py-2 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400">
            Try Again
        </button>
    </div>
    @endif

    <!-- Content State -->
    <div id="content-state">
        @if(count($activeEmployees) > 0)
        <div class="p-4 bg-lime-500/10 border-b border-lime-500/30">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-lime-500 rounded-full animate-pulse"></div>
                <span class="text-lime-400 font-medium">
                    {{ count($activeEmployees) }} {{ Str::plural('employee', count($activeEmployees)) }} currently
                    active
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-700">
                <thead class="bg-slate-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Employee
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Department/Group
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Clocked In At
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-slate-800/50 divide-y divide-slate-700">
                    @foreach($activeEmployees as $active)
                    <tr class="hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-lime-500/20 flex items-center justify-center">
                                        <span class="text-lime-400 font-semibold text-sm">
                                            {{ strtoupper(substr($active['full_name'], 0, 2)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-white">{{ $active['full_name'] }}</div>
                                    @if($active['employee_code'])
                                    <div class="text-xs text-slate-400">{{ $active['employee_code'] }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-300">{{ $active['group'] ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-300">
                                {{ $active['lastClockIn'] ?
                                \Carbon\Carbon::parse($active['lastClockIn'])->setTimezone('Asia/Kathmandu')->format('h:i
                                A') : '-' }}
                            </div>
                            <div class="text-xs text-slate-500">
                                {{ $active['lastClockIn'] ?
                                \Carbon\Carbon::parse($active['lastClockIn'])->setTimezone('Asia/Kathmandu')->diffForHumans()
                                : '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-lime-500/20 text-lime-400">
                                <span class="w-2 h-2 bg-lime-500 rounded-full mr-1.5 mt-0.5 animate-pulse"></span>
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($active['employee'])
                            <a href="{{ route('admin.hrm.attendance.employee', $active['employee']) }}"
                                class="text-lime-400 hover:text-lime-300">
                                View Timesheet
                            </a>
                            @else
                            <span class="text-slate-500">Not Linked</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <p class="text-slate-400 text-lg font-medium">No employees currently active</p>
            <p class="text-slate-500 text-sm mt-2">Employees who are clocked in on Jibble will appear here</p>
            <p class="text-slate-600 text-xs mt-4">
                <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                        clip-rule="evenodd"></path>
                </svg>
                Last checked: <span id="last-checked">{{ now()->format('h:i A') }}</span>
            </p>
        </div>
        @endif
    </div>
</div>

<div class="mt-6 bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd"></path>
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-400">Information</h3>
            <div class="mt-2 text-sm text-blue-300">
                <ul class="list-disc list-inside space-y-1">
                    <li>This data is cached for 2 minutes to improve performance</li>
                    <li>Click "Refresh" to get the latest status from Jibble</li>
                    <li>Only employees currently clocked in will appear in this list</li>
                    <li>Group and last clock-in time are shown for each active employee</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function refreshActiveUsers() {
        const refreshBtn = document.getElementById('refresh-btn');
        const refreshIcon = document.getElementById('refresh-icon');
        const refreshText = document.getElementById('refresh-text');
        const loadingState = document.getElementById('loading-state');
        const errorState = document.getElementById('error-state');
        const contentState = document.getElementById('content-state');
        
        // Show loading state
        refreshBtn.disabled = true;
        refreshIcon.classList.add('animate-spin');
        refreshText.textContent = 'Loading...';
        
        if (loadingState && contentState) {
            loadingState.classList.remove('hidden');
            contentState.classList.add('hidden');
            errorState.classList.add('hidden');
        } else {
            // Fallback to full page reload
            location.reload();
            return;
        }
        
        // Reload the page to get fresh data
        setTimeout(() => {
            location.reload();
        }, 500);
    }
    
    // Update last checked time
    function updateLastChecked() {
        const lastCheckedEl = document.getElementById('last-checked');
        if (lastCheckedEl) {
            const now = new Date();
            lastCheckedEl.textContent = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: true 
            });
        }
    }
    
    // Update on page load
    updateLastChecked();
</script>
@endpush
@endsection