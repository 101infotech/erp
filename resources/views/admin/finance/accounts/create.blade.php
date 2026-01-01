@extends('admin.layouts.app')

@section('title', 'New Account')
@section('page-title', 'Add New Account')

@section('content')
<div class="max-w-7xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.finance.accounts.index') }}" class="group">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-white">Add New Account</h2>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.finance.accounts.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Company *</label>
                    <select name="company_id" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', request('company_id'))==$company->id ?
                            'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('company_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Account Code *</label>
                    <input type="text" name="account_code" value="{{ old('account_code') }}" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white" placeholder="1000">
                    @error('account_code')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Account Name *</label>
                    <input type="text" name="account_name" value="{{ old('account_name') }}" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white" placeholder="Cash">
                    @error('account_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Account Type *</label>
                    <select name="account_type" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        <option value="asset" {{ old('account_type')=='asset' ? 'selected' : '' }}>Asset</option>
                        <option value="liability" {{ old('account_type')=='liability' ? 'selected' : '' }}>Liability
                        </option>
                        <option value="equity" {{ old('account_type')=='equity' ? 'selected' : '' }}>Equity</option>
                        <option value="revenue" {{ old('account_type')=='revenue' ? 'selected' : '' }}>Revenue</option>
                        <option value="expense" {{ old('account_type')=='expense' ? 'selected' : '' }}>Expense</option>
                    </select>
                    @error('account_type')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Parent Account</label>
                    <select name="parent_account_id"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        <option value="">None (Top Level)</option>
                        @foreach($parentAccounts as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_account_id')==$parent->id ? 'selected' : '' }}>
                            {{ $parent->account_code }} - {{ $parent->account_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('parent_account_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Status</label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="form-checkbox h-5 w-5">
                        <span class="ml-2">Active</span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white"
                        placeholder="Account description...">{{ old('description') }}</textarea>
                    @error('description')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('admin.finance.accounts.index') }}"
                    class="px-6 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 dark:border-slate-600 dark:hover:bg-slate-700">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Create Account
                </button>
            </div>
        </form>
    </div>
    @endsection