@extends('admin.layouts.app')

@section('title', 'Sales')
@section('page-title', 'Sales')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Sales</h2>
        <a href="{{ route('admin.finance.sales.create', ['company_id' => $companyId]) }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i> New Sale
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}
    </div>
    @endif

    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <select id="company_id" onchange="location.href='?company_id='+this.value+'&fiscal_year={{ $fiscalYear }}'"
            class="px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
            @foreach($companies as $company)
            <option value="{{ $company->id }}" {{ $companyId==$company->id ? 'selected' : '' }}>{{ $company->name }}
            </option>
            @endforeach
        </select>
        <select id="fiscal_year" onchange="location.href='?company_id={{ $companyId }}&fiscal_year='+this.value"
            class="px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
            <option value="2081" {{ $fiscalYear=='2081' ? 'selected' : '' }}>2081</option>
            <option value="2080" {{ $fiscalYear=='2080' ? 'selected' : '' }}>2080</option>
        </select>
        <select
            onchange="location.href='?company_id={{ $companyId }}&fiscal_year={{ $fiscalYear }}&payment_status='+this.value"
            class="px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
        </select>
    </div>

    <div class="w-full">
        <table class="w-full table-auto divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Sale #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Invoice #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">VAT
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($sales as $sale)
                <tr>
                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">{{ $sale->sale_date_bs }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ $sale->sale_number }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">{{ $sale->customer_name }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">{{ $sale->invoice_number }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">NPR {{
                        number_format($sale->taxable_amount, 2) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">NPR {{ number_format($sale->vat_amount,
                        2) }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">NPR {{
                        number_format($sale->total_amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <span
                            class="px-2 py-1 text-xs rounded-full {{ $sale->payment_status === 'paid' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' }}">
                            {{ ucfirst($sale->payment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('admin.finance.sales.edit', $sale) }}"
                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Edit</a>
                        <form action="{{ route('admin.finance.sales.destroy', $sale) }}" method="POST" class="inline"
                            onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">No sales found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $sales->appends(request()->query())->links() }}</div>
</div>
@endsection