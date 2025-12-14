@extends('admin.layouts.app')

@section('title', 'New Purchase')
@section('page-title', 'Create Purchase Bill')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <form action="{{ route('admin.finance.purchases.store') }}" method="POST">
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
                <label class="block text-sm font-medium mb-2">Purchase Number *</label>
                <input type="text" name="purchase_number"
                    value="{{ old('purchase_number', 'PUR-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT)) }}"
                    required class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white" readonly>
                @error('purchase_number')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Bill Number</label>
                <input type="text" name="bill_number" value="{{ old('bill_number') }}"
                    class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white" placeholder="BILL-001">
                @error('bill_number')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Purchase Date (BS) *</label>
                <input type="text" name="purchase_date_bs" value="{{ old('purchase_date_bs', date('Y-m-d')) }}" required
                    class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white"
                    placeholder="2081-09-15">
                @error('purchase_date_bs')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Vendor</label>
                <select name="vendor_id" class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                    <option value="">-- Select Vendor --</option>
                    @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}" {{ old('vendor_id')==$vendor->id ? 'selected' : '' }}>
                        {{ $vendor->name }}
                    </option>
                    @endforeach
                </select>
                @error('vendor_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Vendor Name *</label>
                <input type="text" name="vendor_name" value="{{ old('vendor_name') }}" required
                    class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white"
                    placeholder="Supplier XYZ">
                @error('vendor_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Taxable Amount (रू) *</label>
                <input type="number" name="taxable_amount" value="{{ old('taxable_amount') }}" step="0.01" min="0"
                    required class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white"
                    placeholder="10000.00">
                @error('taxable_amount')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">VAT Amount (13%) *</label>
                <input type="number" name="vat_amount" value="{{ old('vat_amount') }}" step="0.01" min="0" required
                    class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white" placeholder="1300.00">
                @error('vat_amount')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">TDS Percentage (%)</label>
                <input type="number" name="tds_percentage" value="{{ old('tds_percentage', 1.5) }}" step="0.01" min="0"
                    class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white" placeholder="1.5">
                @error('tds_percentage')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">TDS Amount (रू)</label>
                <input type="number" name="tds_amount" value="{{ old('tds_amount') }}" step="0.01" min="0"
                    class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white" placeholder="150.00">
                @error('tds_amount')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Total Amount (रू) *</label>
                <input type="number" name="total_amount" value="{{ old('total_amount') }}" step="0.01" min="0" required
                    class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white" placeholder="11300.00">
                @error('total_amount')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Net Amount (रू) *</label>
                <input type="number" name="net_amount" value="{{ old('net_amount') }}" step="0.01" min="0" required
                    class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white" placeholder="11150.00">
                @error('net_amount')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Payment Status *</label>
                <select name="payment_status" required
                    class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                    <option value="pending" {{ old('payment_status')=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ old('payment_status')=='partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ old('payment_status')=='paid' ? 'selected' : '' }}>Paid</option>
                </select>
                @error('payment_status')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Payment Method</label>
                <select name="payment_method"
                    class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                    <option value="cash" {{ old('payment_method')=='cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank_transfer" {{ old('payment_method')=='bank_transfer' ? 'selected' : '' }}>Bank
                        Transfer</option>
                    <option value="cheque" {{ old('payment_method')=='cheque' ? 'selected' : '' }}>Cheque</option>
                    <option value="card" {{ old('payment_method')=='card' ? 'selected' : '' }}>Card</option>
                    <option value="online" {{ old('payment_method')=='online' ? 'selected' : '' }}>Online</option>
                </select>
                @error('payment_method')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Notes</label>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white"
                    placeholder="Additional notes...">{{ old('notes') }}</textarea>
                @error('notes')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="flex justify-end gap-4 mt-6">
            <a href="{{ route('admin.finance.purchases.index') }}"
                class="px-6 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 dark:border-slate-600 dark:hover:bg-slate-700">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Create Purchase
            </button>
        </div>
    </form>
</div>
@endsection