@extends('admin.layouts.app')

@section('title', 'Add Founder')
@section('page-title', 'Add Founder')

@section('content')
<div class="max-w-7xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.finance.founders.index') }}" class="group">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-white">Add Founder</h2>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Add New Founder</h2>
            <p class="text-slate-600 dark:text-slate-400 mt-1">Create a new founder profile with investment tracking</p>
        </div>

        <form action="{{ route('admin.finance.founders.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Company -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Company <span class="text-red-500">*</span>
                    </label>
                    <select name="company_id" required
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 @error('company_id') border-red-500 @enderror">
                        <option value="">Select Company</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id')==$company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('company_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Phone Number
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 @error('phone') border-red-500 @enderror">
                    @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PAN Number -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        PAN Number
                    </label>
                    <input type="text" name="pan_number" value="{{ old('pan_number') }}"
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 @error('pan_number') border-red-500 @enderror">
                    @error('pan_number')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Investment Limit -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Investment Limit (NPR) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="investment_limit" value="{{ old('investment_limit') }}" step="0.01"
                        min="0" required
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 @error('investment_limit') border-red-500 @enderror">
                    @error('investment_limit')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                    Notes
                </label>
                <textarea name="notes" rows="3"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status -->
            <div class="mt-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-slate-700 dark:text-slate-200">Active</span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 mt-8">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i> Create Founder
                </button>
                <a href="{{ route('admin.finance.founders.index') }}"
                    class="bg-slate-500 hover:bg-slate-600 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
    @endsection