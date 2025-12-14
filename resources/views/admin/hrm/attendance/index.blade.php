@extends('admin.layouts.app')

@section('title', 'Attendance')
@section('page-title', 'Attendance Records')

@section('content')
<!-- Header -->
<div class="mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
        <div>
            <h2 class="text-2xl font-bold text-white">Attendance Records</h2>
            <p class="text-slate-400 mt-1">Track and manage employee attendance</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.hrm.attendance.active-users') }}"
                class="px-3 py-1.5 text-sm bg-lime-600 text-white rounded-lg hover:bg-lime-500 transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                Who's Logged In
            </a>
            <a href="{{ route('admin.hrm.attendance.calendar') }}"
                class="px-3 py-1.5 text-sm bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition">
                Calendar
            </a>
            <form method="POST" action="{{ route('admin.hrm.attendance.sync-all') }}" class="inline">
                @csrf
                <input type="hidden" name="days" value="30">
                <button type="submit"
                    class="px-3 py-1.5 text-sm bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Sync All (30d)
                </button>
            </form>
            <a href="{{ route('admin.hrm.attendance.sync-form') }}"
                class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Custom Sync
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <select name="employee_id"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500">
                <option value="">All Employees</option>
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ request('employee_id')==$emp->id ? 'selected' : '' }}>
                    {{ $emp->full_name }}
                </option>
                @endforeach
            </select>

            <input type="date" name="start_date" value="{{ request('start_date') }}"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500">

            <input type="date" name="end_date" value="{{ request('end_date') }}"
                class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500">

            <button type=\"submit\"
                class="px-4 py-2 text-sm bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-medium transition">
                Apply Filters
            </button>
        </div>
    </form>
</div>

<div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-700">
            <thead class="bg-slate-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Employee
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Tracked
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider hidden sm:table-cell">
                        Payroll</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider hidden md:table-cell">
                        Overtime</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider hidden md:table-cell">
                        Source</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-slate-800/50 divide-y divide-slate-700">
                @forelse($attendances as $attendance)
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.hrm.attendance.employee', $attendance->employee) }}"
                            class="hover:text-lime-400">
                            <div class="text-sm font-medium text-white">{{ $attendance->employee->full_name }}</div>
                            <div class="text-xs text-slate-400">{{ $attendance->employee->code }}</div>
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                        {{ $attendance->date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-medium">
                        {{ number_format($attendance->tracked_hours, 2) }}h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                        {{ number_format($attendance->payroll_hours, 2) }}h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($attendance->overtime_hours > 0)
                        <span class="text-orange-400 font-medium">{{ number_format($attendance->overtime_hours, 2)
                            }}h</span>
                        @else
                        <span class="text-slate-500">0.00h</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $attendance->source === 'jibble' ? 'bg-blue-500/20 text-blue-400' : 'bg-slate-700 text-slate-400' }}">
                            {{ ucfirst($attendance->source) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.hrm.attendance.show', $attendance) }}"
                            class="text-lime-400 hover:text-lime-300">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p>No attendance records found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $attendances->appends(request()->query())->links() }}
</div>
@endsection