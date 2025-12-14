@extends('admin.layouts.app')

@section('title', 'Attendance Calendar')
@section('page-title', 'Attendance Calendar')

@section('content')
<div class="space-y-6">
    <form method="GET"
        class="bg-white p-4 rounded-lg shadow flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-600">Month</label>
            <input type="month" name="month" value="{{ $startDate->format('Y-m') }}"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500" />
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-600">Employee</label>
            <select name="employee_id"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                <option value="">All Employees</option>
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ request('employee_id')==$emp->id ? 'selected' : '' }}>{{
                    $emp->full_name }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800">Apply</button>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-7 gap-2 text-center text-xs font-medium text-gray-500 mb-2">
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
        @endphp
        <div class="grid grid-cols-7 gap-2">
            @while($cursor->lte($endCell))
            @php
            $inMonth = $cursor->month === $first->month;
            $list = $byDate[$cursor->toDateString()] ?? collect();
            $tracked = $list->sum('tracked_hours');
            @endphp
            <div class="border rounded p-2 h-28 {{ $inMonth ? 'bg-white' : 'bg-gray-50 text-gray-400' }}">
                <div class="text-xs font-semibold">{{ $cursor->format('j') }}</div>
                <div class="mt-1 text-xs {{ $tracked>0 ? 'text-green-700 font-semibold' : 'text-gray-400' }}">
                    {{ $tracked>0 ? number_format($tracked,2).'h' : '—' }}
                </div>
            </div>
            @php $cursor->addDay(); @endphp
            @endwhile
        </div>
    </div>
</div>
@endsection