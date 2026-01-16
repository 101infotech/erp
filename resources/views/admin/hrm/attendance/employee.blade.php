@extends('admin.layouts.app')

@section('title', 'Employee Timesheet')
@section('page-title', 'Timesheet: ' . $employee->full_name)

@section('content')
<div class="px-6 md:px-8 py-6 space-y-6">
    <!-- Header -->
    <div>
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
            <div>
                <h2 class="text-2xl font-bold text-white">{{ $employee->full_name }} - Timesheet</h2>
                <p class="text-slate-400 mt-1">{{ \Carbon\Carbon::parse($startDate ?? now())->format('F Y') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="GET" class="inline">
                    @php
                    $currentMonth = isset($startDate) ? \Carbon\Carbon::parse($startDate)->format('Y-m') :
                    now()->format('Y-m');
                    @endphp
                    <select name="month"
                        class="px-3 py-2 text-sm bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500"
                        onchange="this.form.submit()">
                        @for($i = 0; $i < 24; $i++) @php $date=now()->subMonths($i);
                            $value = $date->format('Y-m');
                            $label = $date->format('F Y');
                            @endphp
                            <option value="{{ $value }}" {{ $currentMonth==$value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endfor
                    </select>
                </form>
                <form method="POST" action="{{ route('admin.hrm.attendance.sync') }}">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                    <input type="hidden" name="start_date" value="{{ $startDate ?? now()->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate ?? now()->format('Y-m-d') }}">
                    <button type="submit"
                        class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        Sync from Jibble
                    </button>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Tracked</p>
                <p class="text-3xl font-bold text-blue-400">{{ number_format($totalTracked, 1) }}<span
                        class="text-lg text-slate-500">h</span></p>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Payroll</p>
                <p class="text-3xl font-bold text-green-400">{{ number_format($totalPayroll, 1) }}<span
                        class="text-lg text-slate-500">h</span></p>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4">
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Overtime</p>
                <p class="text-3xl font-bold text-orange-400">{{ number_format($totalOvertime, 1) }}<span
                        class="text-lg text-slate-500">h</span></p>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-700">
                <thead class="bg-slate-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Tracked
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Payroll
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Overtime
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-slate-800/50 divide-y divide-slate-700">
                    @php
                    $startCarbon = \Carbon\Carbon::parse($startDate ?? now());
                    $endCarbon = \Carbon\Carbon::parse($endDate ?? now());
                    $byDate = collect($attendances)->keyBy(fn($a) => $a->date->format('Y-m-d'));
                    $today = now()->format('Y-m-d');
                    @endphp
                    @for($date = $startCarbon->copy(); $date->lte($endCarbon); $date->addDay())
                    @php
                    $currentDate = $date->format('Y-m-d');
                    $dayRecord = $byDate[$currentDate] ?? null;
                    $hasData = $dayRecord && ($dayRecord->tracked_hours > 0);
                    $isToday = $currentDate === $today;
                    $isWeekend = $date->isSaturday();
                    @endphp
                    <tr class="hover:bg-slate-700/50 transition {{ $isToday ? 'bg-blue-500/10' : '' }}">
                        <td class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="text-sm {{ $isToday ? 'font-bold text-blue-400' : 'text-slate-300' }}">
                                    {{ $date->format('D, M d') }}
                                </span>
                                @if($isToday)
                                <span class="px-2 py-0.5 text-xs bg-blue-500 text-white rounded-full">Today</span>
                                @endif
                                @if($isWeekend)
                                <span
                                    class="px-2 py-0.5 text-xs bg-slate-700 text-slate-400 rounded-full">Weekend</span>
                                @endif
                            </div>
                        </td>
                        <td
                            class="px-6 py-3 whitespace-nowrap text-sm {{ $hasData ? 'font-semibold text-blue-400' : 'text-slate-500' }}">
                            {{ number_format((float)($dayRecord->tracked_hours ?? 0), 2) }}h
                        </td>
                        <td
                            class="px-6 py-3 whitespace-nowrap text-sm {{ $hasData ? 'font-semibold text-green-400' : 'text-slate-500' }}">
                            {{ number_format((float)($dayRecord->payroll_hours ?? 0), 2) }}h
                        </td>
                        <td
                            class="px-6 py-3 whitespace-nowrap text-sm {{ $hasData && ($dayRecord->overtime_hours ?? 0) > 0 ? 'font-semibold text-orange-400' : 'text-slate-500' }}">
                            {{ number_format((float)($dayRecord->overtime_hours ?? 0), 2) }}h
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-right text-sm">
                            @if($dayRecord)
                            <a href="{{ route('admin.hrm.attendance.show', $dayRecord->id) }}"
                                class="text-lime-400 hover:text-lime-300">View</a>
                            @else
                            <span class="text-slate-600">—</span>
                            @endif
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
    @endsection