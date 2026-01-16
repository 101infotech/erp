@extends('admin.layouts.app')

@section('title', 'Add Company')
@section('page-title', 'Add Company')

@section('content')
<div class="px-6 md:px-8 py-6">
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.hrm.companies.index') }}" class="group">
                <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-white">Add Company</h2>
        </div>

        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-6">
            <form method="POST" action="{{ route('admin.hrm.companies.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Company Name
                        *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-lime-500" />
                    @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Contact
                        Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email') }}"
                        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-lime-500" />
                    @error('contact_email') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Address</label>
                    <textarea name="address" rows="3"
                        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-lime-500">{{ old('address') }}</textarea>
                    @error('address') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Finance Company *</label>
                    <select name="finance_company_id" required
                        class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-lime-500">
                        <option value="">Select Finance Company</option>
                        @foreach($financeCompanies as $f)
                        <option value="{{ $f->id }}" {{ old('finance_company_id')==$f->id ? 'selected' : '' }}>
                            {{ $f->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('finance_company_id') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.hrm.companies.index') }}"
                        class="px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-white font-semibold">Cancel</a>
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold">Create
                        Company</button>
                </div>
            </form>
        </div>
        @endsection