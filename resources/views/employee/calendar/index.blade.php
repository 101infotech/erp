@extends('employee.layouts.app')

@section('title', 'Calendar')
@section('page-title', 'Calendar')

@section('content')
    <div class="p-6">
        <div class="max-w-4xl mx-auto bg-slate-800/60 rounded-lg p-6">
            <h1 class="text-2xl font-semibold text-white mb-4">Calendar — Holidays & Events</h1>

            <p class="text-sm text-slate-300 mb-4">This view shows company holidays and other calendar events. Attendance or personal events will be merged here.</p>

            <div class="grid grid-cols-1 gap-4">
                @forelse($holidays->groupBy(fn($h) => $h->date->format('Y-m')) as $month => $group)
                    <div class="bg-slate-700/40 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-lime-300">{{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}</h3>
                        <ul class="mt-2 space-y-2">
                            @foreach($group as $holiday)
                                <li class="flex items-start gap-3">
                                    <div class="w-10 text-sm text-slate-200">{{ $holiday->date->format('d M') }}</div>
                                    <div>
                                        <div class="text-white font-semibold">{{ $holiday->name }}</div>
                                        @if($holiday->description)
                                            <div class="text-sm text-slate-300">{{ $holiday->description }}</div>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <div class="bg-slate-700/40 rounded-lg p-4 text-slate-300">No holidays found.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
