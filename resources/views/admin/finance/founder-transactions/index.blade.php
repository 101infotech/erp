@extends('admin.layouts.app')

@section('title', 'Founder Transactions')
@section('page-title', 'Founder Transactions')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Founder Transactions</h2>
        <a href="{{ route('admin.finance.founder-transactions.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i> New Transaction
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
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Founder</label>
                <select name="founder_id"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                    <option value="">All Founders</option>
                    @foreach($founders as $founder)
                    <option value="{{ $founder->id }}" {{ request('founder_id')==$founder->id ? 'selected' : '' }}>{{
                        $founder->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Company</label>
                <select name="company_id"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id')==$company->id ? 'selected' : '' }}>{{
                        $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Type</label>
                <select name="type" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                    <option value="">All Types</option>
                    <option value="investment" {{ request('type')=='investment' ? 'selected' : '' }}>Investment</option>
                    <option value="withdrawal" {{ request('type')=='withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Status</label>
                <select name="status"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                    <option value="settled" {{ request('status')=='settled' ? 'selected' : '' }}>Settled</option>
                    <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"><i
                        class="fas fa-search mr-2"></i> Filter</button>
                <a href="{{ route('admin.finance.founder-transactions.index') }}"
                    class="bg-slate-500 hover:bg-slate-600 text-white px-4 py-2 rounded-lg">Reset</a>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Transaction #
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Date (BS)
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Founder</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Company</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Type</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-200">Amount</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-slate-700 dark:text-slate-200">Status</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-slate-700 dark:text-slate-200">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                @forelse($transactions as $transaction)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700">
                    <td class="px-4 py-3 text-slate-900 dark:text-white font-medium">{{ $transaction->transaction_number
                        }}</td>
                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $transaction->transaction_date_bs }}
                    </td>
                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $transaction->founder->name }}</td>
                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $transaction->company->name }}</td>
                    <td class="px-4 py-3">
                        <span
                            class="px-2 py-1 text-xs rounded-full {{ $transaction->transaction_type == 'investment' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($transaction->transaction_type) }}
                        </span>
                    </td>
                    <td
                        class="px-4 py-3 text-right font-medium {{ $transaction->transaction_type == 'investment' ? 'text-green-600' : 'text-red-600' }}">
                        NPR {{ number_format($transaction->amount, 2) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $transaction->status == 'settled' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $transaction->status == 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $transaction->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $transaction->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.finance.founder-transactions.show', $transaction) }}"
                                class="text-blue-600 hover:text-blue-800" title="View"><i class="fas fa-eye"></i></a>

                            @if($transaction->status == 'pending')
                            <a href="{{ route('admin.finance.founder-transactions.edit', $transaction) }}"
                                class="text-yellow-600 hover:text-yellow-800" title="Edit"><i
                                    class="fas fa-edit"></i></a>

                            <button type="button" onclick="openModal('approveFTModal_{{ $transaction->id }}')"
                                class="text-green-600 hover:text-green-800" title="Approve"><i
                                    class="fas fa-check"></i></button>

                            <button type="button" onclick="openModal('cancelFTModal_{{ $transaction->id }}')"
                                class="text-red-600 hover:text-red-800" title="Cancel"><i
                                    class="fas fa-times"></i></button>

                            @endif

                            @if($transaction->status == 'approved')
                            <button type="button" onclick="openModal('settleFTModal_{{ $transaction->id }}')"
                                class="text-purple-600 hover:text-purple-800" title="Settle"><i
                                    class="fas fa-check-double"></i></button>

                            @endif

                            @if($transaction->document_path)
                            <a href="{{ route('admin.finance.founder-transactions.download', $transaction) }}"
                                class="text-indigo-600 hover:text-indigo-800" title="Download"><i
                                    class="fas fa-download"></i></a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-slate-500">No transactions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $transactions->links() }}</div>
</div>
@foreach($transactions as $transaction)
@if($transaction->status == 'pending')
<!-- Approve Founder Transaction Modal -->
<x-professional-modal id="approveFTModal_{{ $transaction->id }}" title="Approve Transaction"
    subtitle="Mark transaction as approved" icon="check" iconColor="green" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Approve this transaction?</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Founder:</span> {{ $transaction->founder->name }}
            </p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Amount:</span> NPR {{
                number_format($transaction->amount, 2) }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('approveFTModal_{{ $transaction->id }}')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Cancel</button>
        <form action="{{ route('admin.finance.founder-transactions.approve', $transaction) }}" method="POST"
            class="inline">
            @csrf
            <button type="submit"
                class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Approve
            </button>
        </form>
    </x-slot>
</x-professional-modal>

<!-- Cancel Founder Transaction Modal -->
<x-professional-modal id="cancelFTModal_{{ $transaction->id }}" title="Cancel Transaction"
    subtitle="Mark transaction as cancelled" icon="warning" iconColor="red" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Cancel this transaction?</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Founder:</span> {{ $transaction->founder->name }}
            </p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Amount:</span> NPR {{
                number_format($transaction->amount, 2) }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('cancelFTModal_{{ $transaction->id }}')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Keep</button>
        <form action="{{ route('admin.finance.founder-transactions.cancel', $transaction) }}" method="POST"
            class="inline">
            @csrf
            <button type="submit"
                class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
            </button>
        </form>
    </x-slot>
</x-professional-modal>
@endif

@if($transaction->status == 'approved')
<!-- Settle Founder Transaction Modal -->
<x-professional-modal id="settleFTModal_{{ $transaction->id }}" title="Settle Transaction"
    subtitle="Mark transaction as settled" icon="check" iconColor="blue" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Mark this transaction as settled?</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Founder:</span> {{ $transaction->founder->name }}
            </p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Amount:</span> NPR {{
                number_format($transaction->amount, 2) }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('settleFTModal_{{ $transaction->id }}')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Keep</button>
        <form action="{{ route('admin.finance.founder-transactions.settle', $transaction) }}" method="POST"
            class="inline">
            @csrf
            <button type="submit"
                class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Settle
            </button>
        </form>
    </x-slot>
</x-professional-modal>
@endif
@endforeach
@endsection