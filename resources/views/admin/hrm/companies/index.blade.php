@extends('admin.layouts.app')

@section('title', 'Companies')
@section('page-title', 'Companies')

@section('content')
<div class="mb-6 flex justify-end">
    <a href="{{ route('admin.hrm.companies.create') }}"
        class="px-4 py-2 bg-lime-500 text-slate-950 font-semibold rounded-lg hover:bg-lime-600">
        Add Company
    </a>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-lg shadow-lg overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-700">
        <thead class="bg-slate-900">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Company Name
                </th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider hidden md:table-cell">
                    Contact Email</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider hidden sm:table-cell">
                    Employees</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider hidden lg:table-cell">
                    Departments</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-400 uppercase tracking-wider">Actions
                </th>
            </tr>
        </thead>
        <tbody class="bg-slate-800 divide-y divide-slate-700">
            @forelse($companies as $company)
            <tr class="hover:bg-slate-700">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-white">{{ $company->name }}</div>
                    <div class="text-sm text-slate-400">{{ $company->address }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300 hidden md:table-cell">
                    {{ $company->contact_email ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-white hidden sm:table-cell">
                    {{ $company->employees_count }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-white hidden lg:table-cell">
                    {{ $company->departments_count }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('admin.hrm.companies.show', $company) }}"
                        class="text-blue-400 hover:text-blue-300 mr-3">View</a>
                    <a href="{{ route('admin.hrm.companies.edit', $company) }}"
                        class="text-lime-400 hover:text-lime-300 mr-3">Edit</a>
                    <button type="button" onclick="openModal('deleteCompanyModal_{{ $company->id }}')"
                        class="text-red-400 hover:text-red-300">Delete</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-slate-400">
                    No companies found. <a href="{{ route('admin.hrm.companies.create') }}"
                        class="text-lime-400 hover:text-lime-300">Add your first company</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $companies->links() }}
</div>

@foreach($companies as $company)
<!-- Delete Company Modal -->
<x-professional-modal id="deleteCompanyModal_{{ $company->id }}" title="Delete Company"
    subtitle="This action cannot be undone" icon="trash" iconColor="red" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Are you sure? This will delete all departments and employees in this company.</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Company:</span> {{ $company->name }}</p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Departments:</span> {{
                $company->departments_count }}</p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Employees:</span> {{
                $company->employees_count ?? 0 }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('deleteCompanyModal_{{ $company->id }}')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Keep</button>
        <form method="POST" action="{{ route('admin.hrm.companies.destroy', $company) }}" class="inline">
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