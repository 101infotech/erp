@extends('admin.layouts.app')

@section('title', 'Sync Attendance from Jibble')
@section('page-title', 'Sync Attendance from Jibble')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-slate-800 rounded-lg shadow border border-slate-700 p-6">
        <form method="POST" action="{{ route('admin.hrm.attendance.sync') }}">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Start Date
                </label>
                <input type="date" name="start_date" required
                    value="{{ old('start_date', now()->subDays(7)->format('Y-m-d')) }}"
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-transparent">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    End Date
                </label>
                <input type="date" name="end_date" required value="{{ old('end_date', now()->format('Y-m-d')) }}"
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-transparent">
            </div>

            <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-400">
                            Sync Information
                        </h3>
                        <div class="mt-2 text-sm text-blue-300">
                            <ul class="list-disc list-inside space-y-1">
                                <li>This will sync attendance data from Jibble for all employees</li>
                                <li>Existing records for the same date will be updated</li>
                                <li>Syncing may take a few moments depending on the date range</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex space-x-4">
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-lime-500 text-slate-950 font-medium rounded-lg hover:bg-lime-600">
                    Sync Attendance
                </button>
                <a href="{{ route('admin.hrm.attendance.index') }}"
                    class="flex-1 px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection