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
                    <form method="POST" action="{{ route('admin.hrm.companies.destroy', $company) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300"
                            onclick="return confirm('Are you sure? This will delete all departments and employees in this company.')">Delete</button>
                    </form>
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
@endsection