@extends('admin.layouts.app')

@section('title', 'Create Department')
@section('page-title', 'Create Department')

@section('content')
<div class="px-6 md:px-8 py-6">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">Departments</h1>
                <p class="text-slate-400">Create a new department</p>
            </div>
            <a href="{{ route('admin.hrm.departments.index') }}"
                class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Departments
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700 p-8">
        <form action="{{ route('admin.hrm.departments.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                <!-- Company -->
                <div>
                    <label class="block text-slate-400 mb-2 font-medium">Company <span
                            class="text-red-400">*</span></label>
                    <select name="company_id" required
                        class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-3 focus:border-lime-500 focus:outline-none focus:ring-2 focus:ring-lime-500/20">
                        <option value="">Select Company</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id')==$company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('company_id')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department Name -->
                <div>
                    <label class="block text-slate-400 mb-2 font-medium">Department Name <span
                            class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="e.g., Engineering, Sales, Marketing"
                        class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-3 focus:border-lime-500 focus:outline-none focus:ring-2 focus:ring-lime-500/20">
                    @error('name')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-slate-400 mb-2 font-medium">Description</label>
                    <textarea name="description" rows="4" placeholder="Brief description of the department..."
                        class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-3 focus:border-lime-500 focus:outline-none focus:ring-2 focus:ring-lime-500/20">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-slate-700">
                <a href="{{ route('admin.hrm.departments.index') }}"
                    class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-lime-500 hover:bg-lime-600 text-slate-950 font-semibold rounded-lg transition">
                    Create Department
                </button>
            </div>
        </form>
    </div>

    <!-- Note -->
    <div class="mt-6 bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
        <p class="text-blue-400 text-sm">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Note: Employees can be assigned to departments later. This department will not be synced with Jibble
            automatically.
        </p>
    </div>
</div>
</div>
@endsection