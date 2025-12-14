@extends('admin.layouts.app')

@section('title', 'Intercompany Loans')
@section('page-title', 'Intercompany Loans')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Intercompany Loans</h2>
        <a href="{{ route('admin.finance.intercompany-loans.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i> New Loan
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <!-- Filters -->
    <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">From Company</label>
                <select name="from_company"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('from_company')==$company->id ? 'selected' : '' }}>{{
                        $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">To Company</label>
                <select name="to_company"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('to_company')==$company->id ? 'selected' : '' }}>{{
                        $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Status</label>
                <select name="status"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>Completed</option>
                    <option value="written_off" {{ request('status')=='written_off' ? 'selected' : '' }}>Written Off
                    </option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"><i
                        class="fas fa-search mr-2"></i> Filter</button>
                <a href="{{ route('admin.finance.intercompany-loans.index') }}"
                    class="bg-slate-500 hover:bg-slate-600 text-white px-4 py-2 rounded-lg">Reset</a>
            </div>
        </form>
    </div>

    <!-- Loans Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Loan #</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">From → To
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Loan Date
                    </th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-200">Principal
                    </th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-200">Outstanding
                    </th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-slate-700 dark:text-slate-200">Status</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-slate-700 dark:text-slate-200">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                @forelse($loans as $loan)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700">
                    <td class="px-4 py-3 text-slate-900 dark:text-white font-medium">{{ $loan->loan_number }}</td>
                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                        <div class="flex items-center gap-2">
                            <span class="font-medium">{{ $loan->fromCompany->name }}</span>
                            <i class="fas fa-arrow-right text-slate-400"></i>
                            <span class="font-medium">{{ $loan->toCompany->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $loan->loan_date_bs }}</td>
                    <td class="px-4 py-3 text-right font-medium text-blue-600">NPR {{
                        number_format($loan->principal_amount, 2) }}</td>
                    <td
                        class="px-4 py-3 text-right font-bold {{ $loan->outstanding_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                        NPR {{ number_format($loan->outstanding_balance, 2) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $loan->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $loan->status == 'active' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $loan->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $loan->status == 'written_off' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $loan->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.finance.intercompany-loans.show', $loan) }}"
                                class="text-blue-600 hover:text-blue-800" title="View"><i class="fas fa-eye"></i></a>

                            @if($loan->status == 'pending')
                            <a href="{{ route('admin.finance.intercompany-loans.edit', $loan) }}"
                                class="text-yellow-600 hover:text-yellow-800" title="Edit"><i
                                    class="fas fa-edit"></i></a>

                            <form action="{{ route('admin.finance.intercompany-loans.approve', $loan) }}" method="POST"
                                class="inline" onsubmit="return confirm('Approve this loan?')">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800" title="Approve"><i
                                        class="fas fa-check"></i></button>
                            </form>
                            @endif

                            @if($loan->status == 'active' && $loan->outstanding_balance > 0)
                            <button onclick="openPaymentModal({{ $loan->id }})"
                                class="text-purple-600 hover:text-purple-800" title="Record Payment">
                                <i class="fas fa-dollar-sign"></i>
                            </button>

                            <form action="{{ route('admin.finance.intercompany-loans.write-off', $loan) }}"
                                method="POST" class="inline" onsubmit="return confirm('Write off this loan?')">
                                @csrf
                                <input type="text" name="reason" hidden value="Bad debt">
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Write Off"><i
                                        class="fas fa-ban"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">No loans found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $loans->links() }}</div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-4">Record Loan Payment</h3>
        <form id="paymentForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Payment Date
                        (BS)</label>
                    <input type="text" name="payment_date_bs" required
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Amount
                        (NPR)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Payment
                        Method</label>
                    <select name="payment_method_id" required
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                        @foreach($paymentMethods as $method)
                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Reference
                        Number</label>
                    <input type="text" name="reference_number"
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Notes</label>
                    <textarea name="notes" rows="2"
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700"></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Record
                    Payment</button>
                <button type="button" onclick="closePaymentModal()"
                    class="bg-slate-500 hover:bg-slate-600 text-white px-4 py-2 rounded-lg">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openPaymentModal(loanId) {
    document.getElementById('paymentForm').action = `/admin/finance/intercompany-loans/${loanId}/record-payment`;
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}
</script>
@endpush
@endsection