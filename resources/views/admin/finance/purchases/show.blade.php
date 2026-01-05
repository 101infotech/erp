@extends('admin.layouts.app')

@section('title', 'Purchase Details')
@section('page-title', 'Purchase Bill Details')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $purchase->purchase_number }}</h2>
        <div class="space-x-2">
            <a href="{{ route('admin.finance.purchases.edit', $purchase) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
            <a href="{{ route('admin.finance.purchases.index') }}"
                class="px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 dark:border-slate-600">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Company</h3>
            <p class="mt-1 text-lg">{{ $purchase->company->name }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Bill Number</h3>
            <p class="mt-1 text-lg">{{ $purchase->bill_number ?? '-' }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Purchase Date (BS)</h3>
            <p class="mt-1 text-lg">{{ $purchase->purchase_date_bs }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Vendor</h3>
            <p class="mt-1 text-lg">{{ $purchase->vendor_name }}</p>
        </div>
    </div>

    <div class="border-t pt-6 mb-6">
        <h3 class="text-xl font-semibold mb-4">Amount Breakdown</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-slate-600 dark:text-slate-400">Taxable Amount:</span>
                <span class="text-lg font-medium">NPR {{ number_format($purchase->taxable_amount, 2) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-600 dark:text-slate-400">VAT (13%):</span>
                <span class="text-lg font-medium">NPR {{ number_format($purchase->vat_amount, 2) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-600 dark:text-slate-400">Total Amount:</span>
                <span class="text-lg font-medium">NPR {{ number_format($purchase->total_amount, 2) }}</span>
            </div>
            @if($purchase->tds_amount)
            <div class="flex justify-between items-center text-red-600">
                <span>TDS ({{ $purchase->tds_percentage }}%):</span>
                <span class="text-lg font-medium">- NPR {{ number_format($purchase->tds_amount, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between items-center border-t pt-3">
                <span class="text-lg font-semibold">Net Amount:</span>
                <span class="text-xl font-bold">NPR {{ number_format($purchase->net_amount, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="border-t pt-6">
        <h3 class="text-xl font-semibold mb-4">Payment Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Payment Status</h3>
                <p class="mt-1">
                    <span
                        class="px-2 py-1 text-sm rounded-full {{ $purchase->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($purchase->payment_status) }}
                    </span>
                </p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Payment Method</h3>
                <p class="mt-1 text-lg">{{ $purchase->payment_method ? ucfirst(str_replace('_', ' ',
                    $purchase->payment_method)) : '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Notes</h3>
                <p class="mt-1 text-lg">{{ $purchase->notes ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection