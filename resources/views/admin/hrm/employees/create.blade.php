@extends('admin.layouts.app')

@section('title', 'Add Employee')
@section('page-title', 'Add Employee')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-white">Add New Employee</h1>
        <a href="{{ route('admin.hrm.employees.index') }}"
            class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
            Back to Employees
        </a>
    </div>

    <form method="POST" action="{{ route('admin.hrm.employees.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-slate-700 dark:text-slate-400 mb-2">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                    @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-400 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                    @error('email') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-400 mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-400 mb-2">Status *</label>
                    <select name="status" required
                        class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        <option value="active" {{ old('status')=='active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 mb-2">Company *</label>
                    <select name="company_id" required
                        class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        <option value="">Select Company</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id')==$company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 mb-2">Department</label>
                    <select name="department_id"
                        class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id')==$department->id ? 'selected' : ''
                            }}>
                            {{ $department->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 mb-2">Designation/Position</label>
                    <input type="text" name="position" value="{{ old('position') }}"
                        class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-400 mb-2">Hire Date</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date') }}"
                        class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-slate-400 mb-2">Avatar</label>
                    <input type="file" name="avatar"
                        class="w-full rounded-lg bg-slate-900 text-white border border-slate-700 px-4 py-2 focus:border-lime-500 focus:outline-none">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('admin.hrm.employees.index') }}"
                class="px-6 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Cancel</a>
            <button type="submit"
                class="px-6 py-2.5 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Employee
            </button>
        </div>
    </form>
</div>
@endsection