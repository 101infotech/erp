@extends('admin.layouts.app')

@section('title', 'Edit Sale')
@section('page-title', 'Edit Sale Invoice')

@section('content')
<div class="max-w-7xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.finance.sales.index') }}" class="group">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-white">Edit Sale Invoice</h2>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.finance.sales.update', $sale) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Company *</label>
                    <select name="company_id" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $sale->company_id) == $company->id ?
                            'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('company_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Sale Number *</label>
                    <input type="text" name="sale_number" value="{{ old('sale_number', $sale->sale_number) }}" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white" readonly>
                    @error('sale_number')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Invoice Number</label>
                    <input type="text" name="invoice_number" value="{{ old('invoice_number', $sale->invoice_number) }}"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                    @error('invoice_number')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Sale Date (BS) *</label>
                    <input type="text" name="sale_date_bs" value="{{ old('sale_date_bs', $sale->sale_date_bs) }}"
                        required class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                    @error('sale_date_bs')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Customer</label>
                    <select name="customer_id"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id', $sale->customer_id) == $customer->id ?
                            'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('customer_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Customer Name *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $sale->customer_name) }}"
                        required class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                    @error('customer_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Taxable Amount (रू) *</label>
                    <input type="number" name="taxable_amount"
                        value="{{ old('taxable_amount', $sale->taxable_amount) }}" step="0.01" min="0" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                    @error('taxable_amount')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">VAT Amount (13%) *</label>
                    <input type="number" name="vat_amount" value="{{ old('vat_amount', $sale->vat_amount) }}"
                        step="0.01" min="0" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                    @error('vat_amount')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Total Amount (रू) *</label>
                    <input type="number" name="total_amount" value="{{ old('total_amount', $sale->total_amount) }}"
                        step="0.01" min="0" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                    @error('total_amount')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Net Amount (रू) *</label>
                    <input type="number" name="net_amount" value="{{ old('net_amount', $sale->net_amount) }}"
                        step="0.01" min="0" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                    @error('net_amount')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Payment Status *</label>
                    <select name="payment_status" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        <option value="pending" {{ old('payment_status', $sale->payment_status) == 'pending' ?
                            'selected' :
                            '' }}>Pending</option>
                        <option value="partial" {{ old('payment_status', $sale->payment_status) == 'partial' ?
                            'selected' :
                            '' }}>Partial</option>
                        <option value="paid" {{ old('payment_status', $sale->payment_status) == 'paid' ? 'selected' : ''
                            }}>Paid</option>
                    </select>
                    @error('payment_status')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Payment Method</label>
                    <select name="payment_method"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        <option value="cash" {{ old('payment_method', $sale->payment_method) == 'cash' ? 'selected' : ''
                            }}>Cash</option>
                        <option value="bank_transfer" {{ old('payment_method', $sale->payment_method) == 'bank_transfer'
                            ?
                            'selected' : '' }}>Bank Transfer</option>
                        <option value="cheque" {{ old('payment_method', $sale->payment_method) == 'cheque' ? 'selected'
                            : ''
                            }}>Cheque</option>
                        <option value="card" {{ old('payment_method', $sale->payment_method) == 'card' ? 'selected' : ''
                            }}>Card</option>
                        <option value="online" {{ old('payment_method', $sale->payment_method) == 'online' ? 'selected'
                            : ''
                            }}>Online</option>
                    </select>
                    @error('payment_method')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">{{ old('notes', $sale->notes) }}</textarea>
                    @error('notes')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('admin.finance.sales.index') }}"
                    class="px-6 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 dark:border-slate-600 dark:hover:bg-slate-700">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update Sale
                </button>
            </div>
        </form>
    </div>
    @endsection