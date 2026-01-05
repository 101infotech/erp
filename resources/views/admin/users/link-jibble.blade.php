@extends('admin.layouts.app')

@section('title', 'Link Employee to User Account')
@section('page-title', 'Link to User Account')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="group">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-white">Link Employee to User Account</h2>
    </div>

    <!-- Employee Info Card -->
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6 mb-6">
        <div class="flex items-center gap-4">
            <div
                class="w-16 h-16 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center">
                <span class="text-white font-bold text-2xl">{{ strtoupper(substr($employee->name, 0, 1)) }}</span>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white">{{ $employee->name }}</h3>
                @if($employee->email)
                <p class="text-slate-400 text-sm">{{ $employee->email }}</p>
                @else
                <p class="text-orange-400 text-sm">⚠️ No email address</p>
                @endif
                @if($employee->jibble_person_id)
                <p class="text-slate-500 text-xs mt-1">Jibble ID: {{ $employee->jibble_person_id }}</p>
                @endif
            </div>
        </div>

        @if($employee->company)
        <div class="mt-4 pt-4 border-t border-slate-700">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-400">Company</p>
                    <p class="text-white font-medium">{{ $employee->company->name }}</p>
                </div>
                @if($employee->department)
                <div>
                    <p class="text-slate-400">Department</p>
                    <p class="text-white font-medium">{{ $employee->department->name }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Link Form -->
    <div class="bg-slate-800 rounded-lg shadow border border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Select User Account to Link</h3>

        <form action="{{ route('admin.employees.link-jibble', $employee) }}" method="POST">
            @csrf

            <div class="mb-6">
                <label for="user_id" class="block text-sm font-medium text-slate-300 mb-2">
                    User Account <span class="text-red-400">*</span>
                </label>

                <select name="user_id" id="user_id" required
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    <option value="">-- Select a user account --</option>
                    @foreach(\App\Models\User::whereDoesntHave('hrmEmployee')->orWhereHas('hrmEmployee', function($q)
                    use ($employee) { $q->where('id', $employee->id); })->orderBy('name')->get() as $user)
                    <option value="{{ $user->id }}" {{ old('user_id')==$user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }}) - {{ ucfirst($user->role) }}
                    </option>
                    @endforeach
                </select>

                @error('user_id')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror

                <p class="text-slate-400 text-xs mt-2">
                    Only users without an existing employee link are shown. This will allow the user to log in and
                    access their employee profile and attendance data.
                </p>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4 mb-6">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-blue-300">
                        <p class="font-semibold mb-1">What happens when you link?</p>
                        <ul class="list-disc list-inside space-y-1 text-blue-400">
                            <li>The user will be able to access their employee profile</li>
                            <li>Attendance data will be linked to the user account</li>
                            <li>The user can view their own attendance and leave records</li>
                            <li>You can unlink them later if needed</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-lime-500 hover:bg-lime-600 text-slate-950 font-medium px-6 py-3 rounded-lg transition">
                    Link User Account
                </button>
                <a href="{{ route('admin.users.index') }}"
                    class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium px-6 py-3 rounded-lg text-center transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection