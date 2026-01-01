@extends('admin.layouts.app')

@section('title', 'New Intercompany Loan')
@section('page-title', 'New Intercompany Loan')

@section('content')
<div class="max-w-7xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.finance.intercompany-loans.index') }}" class="group">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-white">Create Intercompany Loan</h2>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Create Intercompany Loan</h2>
            <p class="text-slate-600 dark:text-slate-400 mt-1">Record interest-free loan between sister companies</p>
        </div>

        <form action="{{ route('admin.finance.intercompany-loans.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- From Company -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Lender (From Company) <span class="text-red-500">*</span>
                    </label>
                    <select name="from_company_id" required
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                        <option value="">Select Lender Company</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('from_company_id')==$company->id ? 'selected' : ''
                            }}>{{
                            $company->name }}</option>
                        @endforeach
                    </select>
                    @error('from_company_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- To Company -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Borrower (To Company) <span class="text-red-500">*</span>
                    </label>
                    <select name="to_company_id" required
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                        <option value="">Select Borrower Company</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('to_company_id')==$company->id ? 'selected' : '' }}>{{
                            $company->name }}</option>
                        @endforeach
                    </select>
                    @error('to_company_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Principal Amount -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Principal Amount (NPR) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="principal_amount" value="{{ old('principal_amount') }}" step="0.01"
                        min="0.01" required
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                    @error('principal_amount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Loan Date -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Loan Date (BS) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="loan_date_bs" value="{{ old('loan_date_bs', '2082-08-27') }}"
                        placeholder="2082-08-27" required
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                    @error('loan_date_bs')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Due Date (BS)
                    </label>
                    <input type="text" name="due_date_bs" value="{{ old('due_date_bs') }}" placeholder="2083-07-30"
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                    @error('due_date_bs')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fiscal Year -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Fiscal Year (BS) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="fiscal_year_bs" value="{{ old('fiscal_year_bs', '2082') }}"
                        placeholder="2082" required
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                    @error('fiscal_year_bs')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Purpose -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                    Purpose <span class="text-red-500">*</span>
                </label>
                <textarea name="purpose" rows="3" required
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">{{ old('purpose') }}</textarea>
                @error('purpose')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                    Additional Notes
                </label>
                <textarea name="notes" rows="2"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">{{ old('notes') }}</textarea>
                @error('notes')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-1"></i>
                    <div class="text-sm text-blue-800 dark:text-blue-200">
                        <p class="font-medium mb-1">Intercompany Loan Guidelines:</p>
                        <ul class="list-disc list-inside space-y-1 text-blue-700 dark:text-blue-300">
                            <li>Interest-free loans between sister companies</li>
                            <li>Loan number will be auto-generated (ICL-YYYY-XXXXXX)</li>
                            <li>Outstanding balance will be tracked automatically</li>
                            <li>Loan will be in "Pending" status until approved</li>
                            <li>Record payments after approval to track repayments</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 mt-8">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i> Create Loan
                </button>
                <a href="{{ route('admin.finance.intercompany-loans.index') }}"
                    class="bg-slate-500 hover:bg-slate-600 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
    @endsection