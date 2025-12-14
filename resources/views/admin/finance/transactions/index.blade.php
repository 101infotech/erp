@extends('admin.layouts.app')

@section('title', 'Transactions')
@section('page-title', 'Finance Transactions')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Transactions</h2>
        <a href="{{ route('admin.finance.transactions.create', ['company_id' => $companyId]) }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i> New Transaction
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Company</label>
            <select id="company_id" onchange="filterTransactions()"
                class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ $companyId==$company->id ? 'selected' : '' }}>
                    {{ $company->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Fiscal Year</label>
            <select id="fiscal_year" onchange="filterTransactions()"
                class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                <option value="2081" {{ $fiscalYear=='2081' ? 'selected' : '' }}>2081</option>
                <option value="2080" {{ $fiscalYear=='2080' ? 'selected' : '' }}>2080</option>
                <option value="2082" {{ $fiscalYear=='2082' ? 'selected' : '' }}>2082</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Type</label>
            <select id="transaction_type" onchange="filterTransactions()"
                class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                <option value="">All</option>
                <option value="income">Income</option>
                <option value="expense">Expense</option>
                <option value="transfer">Transfer</option>
                <option value="investment">Investment</option>
                <option value="loan">Loan</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
            <select id="status" onchange="filterTransactions()"
                class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>

    <div class="w-full">
        <table class="w-full table-auto divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Date (BS)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Transaction #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Payment Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($transactions as $transaction)
                <tr>
                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">
                        {{ $transaction->transaction_date_bs }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">
                        {{ $transaction->transaction_number }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($transaction->transaction_type === 'income') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                            @elseif($transaction->transaction_type === 'expense') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                            @elseif($transaction->transaction_type === 'transfer') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300
                            @else bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-300 @endif">
                            {{ ucfirst($transaction->transaction_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                        {{ $transaction->description }}
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold
                        @if($transaction->transaction_type === 'income') text-green-600 dark:text-green-400
                        @else text-red-600 dark:text-red-400 @endif">
                        रू {{ number_format($transaction->amount, 2) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                        {{ $transaction->payment_method }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($transaction->status === 'approved') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                            @elseif($transaction->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                            @else bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 @endif">
                            {{ ucfirst($transaction->status ?? 'pending') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <a href="{{ route('admin.finance.transactions.edit', $transaction) }}"
                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Edit</a>
                        <form action="{{ route('admin.finance.transactions.destroy', $transaction) }}" method="POST"
                            class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">
                        No transactions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $transactions->appends(request()->query())->links() }}
    </div>
</div>

@push('scripts')
<script>
    function filterTransactions() {
    const companyId = document.getElementById('company_id').value;
    const fiscalYear = document.getElementById('fiscal_year').value;
    const transactionType = document.getElementById('transaction_type').value;
    const status = document.getElementById('status').value;
    
    let url = '{{ route("admin.finance.transactions.index") }}';
    url += `?company_id=${companyId}&fiscal_year=${fiscalYear}`;
    
    if (transactionType) url += `&transaction_type=${transactionType}`;
    if (status) url += `&status=${status}`;
    
    window.location.href = url;
}
</script>
@endpush
@endsection