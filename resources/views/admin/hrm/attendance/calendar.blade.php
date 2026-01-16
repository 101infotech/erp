@extends('admin.layouts.app')

@section('title', 'Attendance Calendar')
@section('page-title', 'Attendance Calendar')

@section('content')
<div class="px-6 md:px-8 py-6 space-y-6">
    <form method="GET"
        class="bg-slate-800/50 border border-slate-700 rounded-lg p-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex items-center gap-2">
            <label class="text-sm text-slate-300">Month</label>
            <input type="month" name="month" value="{{ $startDate->format('Y-m') }}"
                class="px-3 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500" />
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm text-slate-300">Employee</label>
            <select name="employee_id"
                class="px-3 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500">
                <option value="">All Employees</option>
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ request('employee_id')==$emp->id ? 'selected' : '' }}>{{
                    $emp->full_name }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400">Apply</button>
        </div>
    </form>

    <div class="bg-slate-800/50 border border-slate-700 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3 text-xs text-slate-300">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-2"><span
                        class="w-3 h-3 rounded-full bg-lime-500/40 border border-lime-500/50"></span>Tracked
                    hours</span>
                <span class="inline-flex items-center gap-2"><span
                        class="w-3 h-3 rounded-full bg-amber-500/40 border border-amber-500/60"></span>Holiday</span>
            </div>
        </div>
        <div class="grid grid-cols-7 gap-2 text-center text-xs font-medium text-slate-300 mb-2">
            <div>Sun</div>
            <div>Mon</div>
            <div>Tue</div>
            <div>Wed</div>
            <div>Thu</div>
            <div>Fri</div>
            <div>Sat</div>
        </div>
        @php
        $first = $startDate->copy()->startOfMonth();
        $startCell = $first->copy()->startOfWeek();
        $endCell = $endDate->copy()->endOfWeek();
        $cursor = $startCell->copy();
        $byDate = collect($attendances)->mapWithKeys(function($items, $date){ return [$date => $items]; });
        $holidaysByDate = isset($holidays) ? $holidays : collect();
        @endphp
        <div class="grid grid-cols-7 gap-2">
            @while($cursor->lte($endCell))
            @php
            $inMonth = $cursor->month === $first->month;
            $list = $byDate[$cursor->toDateString()] ?? collect();
            $tracked = $list->sum('tracked_hours');
            $dayHolidays = $holidaysByDate[$cursor->toDateString()] ?? collect();
            @endphp
            <div
                class="border border-slate-700 rounded p-2 h-32 flex flex-col gap-1 {{ $inMonth ? 'bg-slate-900 text-white' : 'bg-slate-800/40 text-slate-500' }}">
                <div class="text-xs font-semibold">{{ $cursor->format('j') }}</div>
                <div class="text-xs {{ $tracked>0 ? 'text-lime-300 font-semibold' : 'text-slate-500' }}">
                    {{ $tracked>0 ? number_format($tracked,2).'h' : '—' }}
                </div>
                @if($dayHolidays->isNotEmpty())
                <div class="space-y-1 mt-1">
                    @foreach($dayHolidays as $holiday)
                    <div
                        class="text-[11px] leading-tight bg-amber-500/10 border border-amber-500/40 text-amber-200 rounded px-2 py-1 text-left">
                        {{ $holiday->name }}
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @php $cursor->addDay(); @endphp
            @endwhile
        </div>
    </div>
</div>
@endsection