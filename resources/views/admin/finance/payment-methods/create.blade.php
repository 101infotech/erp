@extends('admin.layouts.app')

@section('title', 'Add Payment Method')
@section('page-title', 'Add Payment Method')

@section('content')
<div class="max-w-7xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.finance.payment-methods.index') }}" class="group">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-white">Add Payment Method</h2>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Add Payment Method</h2>
        </div>

        <form action="{{ route('admin.finance.payment-methods.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Method Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Code</label>
                    <input type="text" name="code" value="{{ old('code') }}"
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Description</label>
                    <textarea name="description" rows="2"
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="requires_reference" value="1" {{ old('requires_reference')
                            ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-slate-700 dark:text-slate-200">Requires Reference Number</span>
                    </label>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-slate-700 dark:text-slate-200">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 mt-8">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"><i
                        class="fas fa-save mr-2"></i> Create Payment Method</button>
                <a href="{{ route('admin.finance.payment-methods.index') }}"
                    class="bg-slate-500 hover:bg-slate-600 text-white px-6 py-2 rounded-lg"><i
                        class="fas fa-times mr-2"></i> Cancel</a>
            </div>
        </form>
    </div>
    @endsection