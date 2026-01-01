@extends('admin.layouts.app')

@section('title', 'Edit Transaction')
@section('page-title', 'Edit Transaction')

@section('content')
<div class="max-w-7xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.finance.transactions.index') }}" class="group">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-white">Edit Transaction</h2>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.finance.transactions.update', $transaction) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Company *</label>
                    <select name="company_id" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $transaction->company_id) ==
                            $company->id ?
                            'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('company_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Transaction Number *</label>
                    <input type="text" name="transaction_number"
                        value="{{ old('transaction_number', $transaction->transaction_number) }}" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white" readonly>
                    @error('transaction_number')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Transaction Date (BS) *</label>
                    <input type="text" name="transaction_date_bs"
                        value="{{ old('transaction_date_bs', $transaction->transaction_date_bs) }}" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white"
                        placeholder="2081-09-15">
                    @error('transaction_date_bs')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Transaction Type *</label>
                    <select name="transaction_type" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        <option value="income" {{ old('transaction_type', $transaction->transaction_type) == 'income' ?
                            'selected' : '' }}>Income</option>
                        <option value="expense" {{ old('transaction_type', $transaction->transaction_type) == 'expense'
                            ?
                            'selected' : '' }}>Expense</option>
                        <option value="transfer" {{ old('transaction_type', $transaction->transaction_type) ==
                            'transfer' ?
                            'selected' : '' }}>Transfer</option>
                        <option value="investment" {{ old('transaction_type', $transaction->transaction_type) ==
                            'investment' ? 'selected' : '' }}>Investment</option>
                        <option value="loan" {{ old('transaction_type', $transaction->transaction_type) == 'loan' ?
                            'selected' : '' }}>Loan</option>
                    </select>
                    @error('transaction_type')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Description *</label>
                    <textarea name="description" rows="2" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">{{ old('description', $transaction->description) }}</textarea>
                    @error('description')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Amount (रू) *</label>
                    <input type="number" name="amount" value="{{ old('amount', $transaction->amount) }}" step="0.01"
                        min="0" required class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                    @error('amount')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Payment Method *</label>
                    <select name="payment_method" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        <option value="cash" {{ old('payment_method', $transaction->payment_method) == 'cash' ?
                            'selected' :
                            '' }}>Cash</option>
                        <option value="bank_transfer" {{ old('payment_method', $transaction->payment_method) ==
                            'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cheque" {{ old('payment_method', $transaction->payment_method) == 'cheque' ?
                            'selected' : '' }}>Cheque</option>
                        <option value="card" {{ old('payment_method', $transaction->payment_method) == 'card' ?
                            'selected' :
                            '' }}>Card</option>
                        <option value="online" {{ old('payment_method', $transaction->payment_method) == 'online' ?
                            'selected' : '' }}>Online</option>
                    </select>
                    @error('payment_method')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Category</label>
                    <select name="category_id"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $transaction->category_id) ==
                            $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Payment Reference</label>
                    <input type="text" name="payment_reference"
                        value="{{ old('payment_reference', $transaction->payment_reference) }}"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white"
                        placeholder="Reference number">
                    @error('payment_reference')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Fiscal Year (BS) *</label>
                    <input type="text" name="fiscal_year_bs"
                        value="{{ old('fiscal_year_bs', $transaction->fiscal_year_bs) }}" maxlength="4" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white" placeholder="2081">
                    @error('fiscal_year_bs')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Fiscal Month *</label>
                    <select name="fiscal_month_bs" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        @for($i = 1; $i <= 12; $i++) <option value="{{ $i }}" {{ old('fiscal_month_bs', $transaction->
                            fiscal_month_bs) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                    </select>
                    @error('fiscal_month_bs')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">{{ old('notes', $transaction->notes) }}</textarea>
                    @error('notes')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('admin.finance.transactions.index') }}"
                    class="px-6 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 dark:border-slate-600 dark:hover:bg-slate-700">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update Transaction
                </button>
            </div>
        </form>
    </div>
    @endsection