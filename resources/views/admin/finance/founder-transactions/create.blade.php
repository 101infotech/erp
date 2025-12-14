@extends('admin.layouts.app')

@section('title', 'New Founder Transaction')
@section('page-title', 'New Founder Transaction')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Record Founder Transaction</h2>
        <p class="text-slate-600 dark:text-slate-400 mt-1">Record investment or withdrawal transaction</p>
    </div>

    <form action="{{ route('admin.finance.founder-transactions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Founder -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                    Founder <span class="text-red-500">*</span>
                </label>
                <select name="finance_founder_id" required
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                    <option value="">Select Founder</option>
                    @foreach($founders as $founder)
                    <option value="{{ $founder->id }}" {{ old('finance_founder_id', request('founder_id'))==$founder->id
                        ? 'selected' : '' }}>
                        {{ $founder->name }} ({{ $founder->company->name }})
                    </option>
                    @endforeach
                </select>
                @error('finance_founder_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Company -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                    Company <span class="text-red-500">*</span>
                </label>
                <select name="company_id" required
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                    <option value="">Select Company</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ old('company_id')==$company->id ? 'selected' : '' }}>{{
                        $company->name }}</option>
                    @endforeach
                </select>
                @error('company_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Transaction Type -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                    Transaction Type <span class="text-red-500">*</span>
                </label>
                <select name="transaction_type" required
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                    <option value="">Select Type</option>
                    <option value="investment" {{ old('transaction_type')=='investment' ? 'selected' : '' }}>Investment
                    </option>
                    <option value="withdrawal" {{ old('transaction_type')=='withdrawal' ? 'selected' : '' }}>Withdrawal
                    </option>
                </select>
                @error('transaction_type')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                    Amount (NPR) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0.01" required
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                @error('amount')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fiscal Year -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                    Fiscal Year (BS) <span class="text-red-500">*</span>
                </label>
                <input type="text" name="fiscal_year_bs" value="{{ old('fiscal_year_bs', '2082') }}" placeholder="2082"
                    required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                @error('fiscal_year_bs')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Transaction Date -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                    Transaction Date (BS) <span class="text-red-500">*</span>
                </label>
                <input type="text" name="transaction_date_bs" value="{{ old('transaction_date_bs', '2082-08-27') }}"
                    placeholder="2082-08-27" required
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                @error('transaction_date_bs')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                    Payment Method <span class="text-red-500">*</span>
                </label>
                <select name="payment_method_id" required
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                    <option value="">Select Method</option>
                    @foreach($paymentMethods as $method)
                    <option value="{{ $method->id }}" {{ old('payment_method_id')==$method->id ? 'selected' : '' }}>{{
                        $method->name }}</option>
                    @endforeach
                </select>
                @error('payment_method_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reference Number -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                    Reference Number
                </label>
                <input type="text" name="reference_number" value="{{ old('reference_number') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                @error('reference_number')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Description -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                Description
            </label>
            <textarea name="description" rows="3"
                class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">{{ old('description') }}</textarea>
            @error('description')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Document Upload -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                Attach Document (PDF, JPG, PNG - Max 5MB)
            </label>
            <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" class="w-full">
            @error('document')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 mt-8">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-save mr-2"></i> Create Transaction
            </button>
            <a href="{{ route('admin.finance.founder-transactions.index') }}"
                class="bg-slate-500 hover:bg-slate-600 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-times mr-2"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection