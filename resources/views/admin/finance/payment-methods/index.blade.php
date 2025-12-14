@extends('admin.layouts.app')

@section('title', 'Payment Methods')
@section('page-title', 'Payment Methods')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Payment Methods</h2>
        <a href="{{ route('admin.finance.payment-methods.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i> Add Payment Method
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Name</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Code</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700 dark:text-slate-200">Description
                    </th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-slate-700 dark:text-slate-200">Requires
                        Reference</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-slate-700 dark:text-slate-200">Status</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-slate-700 dark:text-slate-200">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                @forelse($paymentMethods as $method)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700">
                    <td class="px-4 py-3 text-slate-900 dark:text-white font-medium">{{ $method->name }}</td>
                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $method->code ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ Str::limit($method->description, 50) ??
                        '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span
                            class="px-2 py-1 text-xs rounded-full {{ $method->requires_reference ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-800' }}">
                            {{ $method->requires_reference ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span
                            class="px-2 py-1 text-xs rounded-full {{ $method->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $method->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.finance.payment-methods.edit', $method) }}"
                                class="text-yellow-600 hover:text-yellow-800"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.finance.payment-methods.destroy', $method) }}" method="POST"
                                class="inline" onsubmit="return confirm('Delete this payment method?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800"><i
                                        class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">No payment methods found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $paymentMethods->links() }}</div>
</div>
@endsection