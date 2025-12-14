@extends('admin.layouts.app')

@section('title', 'Create Transaction')
@section('page-title', 'Create Transaction')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.finance.transactions.index', ['company_id' => $companyId]) }}"
            class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i> Back to Transactions
        </a>
    </div>

    <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-6">Create New Transaction</h2>

    <form action="{{ route('admin.finance.transactions.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Company *</label>
                <select name="company_id" required
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ $companyId==$company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Transaction Number
                    *</label>
                <input type="text" name="transaction_number"
                    value="{{ old('transaction_number', 'TXN-' . str_pad(rand(1,999999), 6, '0', STR_PAD_LEFT)) }}"
                    required
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Date (BS) *</label>
                <input type="text" name="transaction_date_bs"
                    value="{{ old('transaction_date_bs', '2081-09-' . date('d')) }}" required placeholder="2081-09-15"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Transaction Type
                    *</label>
                <select name="transaction_type" required
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                    <option value="transfer">Transfer</option>
                    <option value="investment">Investment</option>
                    <option value="loan">Loan</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Description *</label>
                <textarea name="description" rows="2" required
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Amount *</label>
                <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" required
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Payment Method
                    *</label>
                <select name="payment_method" required
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cheque">Cheque</option>
                    <option value="card">Card</option>
                    <option value="online">Online Payment</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Category</label>
                <select name="category_id"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                    <option value="">None</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Payment
                    Reference</label>
                <input type="text" name="payment_reference" value="{{ old('payment_reference') }}"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Fiscal Year *</label>
                <input type="text" name="fiscal_year_bs" value="{{ old('fiscal_year_bs', '2081') }}" required
                    maxlength="4"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Fiscal Month *</label>
                <select name="fiscal_month_bs" required
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                    @for($i = 1; $i <= 12; $i++) <option value="{{ $i }}" {{ old('fiscal_month_bs', 9)==$i ? 'selected'
                        : '' }}>{{ $i }}</option>
                        @endfor
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Notes</label>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('admin.finance.transactions.index', ['company_id' => $companyId]) }}"
                class="px-6 py-2 border border-slate-300 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                Create Transaction
            </button>
        </div>
    </form>
</div>
@endsection