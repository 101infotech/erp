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
                            <button type="button" onclick="openModal('deleteMethodModal_{{ $method->id }}')"
                                class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
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
@foreach($paymentMethods as $method)
<!-- Delete Payment Method Modal -->
<x-professional-modal id="deleteMethodModal_{{ $method->id }}" title="Delete Payment Method"
    subtitle="This action cannot be undone" icon="trash" iconColor="red" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Are you sure you want to delete this payment method?</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Method:</span> {{ $method->name }}</p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Status:</span> {{ $method->is_active ?
                'Active' : 'Inactive' }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('deleteMethodModal_{{ $method->id }}')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Keep</button>
        <form action="{{ route('admin.finance.payment-methods.destroy', $method) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete
            </button>
        </form>
    </x-slot>
</x-professional-modal>
@endforeach
@endsection