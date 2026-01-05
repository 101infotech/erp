@extends('admin.layouts.app')

@section('title', 'Edit Company')
@section('page-title', 'Edit Company')

@section('content')
<div class="max-w-7xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.finance.companies.index') }}" class="group">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="text-2xl font-bold text-white">Edit Company</h2>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.finance.companies.update', $company) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Company Name *</label>
                    <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                    @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Company Type *</label>
                    <select name="type" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        <option value="holding" {{ old('type', $company->type) == 'holding' ? 'selected'
                            :
                            '' }}>Holding Company</option>
                        <option value="subsidiary" {{ old('type', $company->type) == 'subsidiary' ?
                            'selected' : '' }}>Subsidiary</option>
                        <option value="independent" {{ old('type', $company->type) == 'independent' ?
                            'selected' : '' }}>Independent</option>
                    </select>
                    @error('type')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Parent Company</label>
                    <select name="parent_company_id"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        <option value="">None</option>
                        @foreach($parentCompanies as $comp)
                        <option value="{{ $comp->id }}" {{ old('parent_company_id', $company->parent_company_id) ==
                            $comp->id ? 'selected' : '' }}>
                            {{ $comp->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('parent_company_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">PAN Number</label>
                    <input type="text" name="pan_number" value="{{ old('pan_number', $company->pan_number) }}"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white"
                        placeholder="123456789">
                    @error('pan_number')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $company->contact_email) }}"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white"
                        placeholder="info@company.com">
                    @error('contact_email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $company->contact_phone) }}"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white"
                        placeholder="+977-1-1234567">
                    @error('contact_phone')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Established Date (BS)</label>
                    <input type="text" name="established_date_bs"
                        value="{{ old('established_date_bs', $company->established_date_bs) }}"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white"
                        placeholder="2075-01-01">
                    @error('established_date_bs')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Fiscal Year Start Month *</label>
                    <select name="fiscal_year_start_month" required
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white">
                        @foreach(['Baishakh' => 1, 'Jestha' => 2, 'Ashar' => 3, 'Shrawan' => 4, 'Bhadra' => 5, 'Ashwin'
                        =>
                        6, 'Kartik' => 7, 'Mangsir' => 8, 'Poush' => 9, 'Magh' => 10, 'Falgun' => 11, 'Chaitra' => 12]
                        as
                        $month => $num)
                        <option value="{{ $num }}" {{ old('fiscal_year_start_month', $company->fiscal_year_start_month)
                            ==
                            $num ? 'selected' : '' }}>{{ $month }}</option>
                        @endforeach
                    </select>
                    @error('fiscal_year_start_month')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Address</label>
                    <textarea name="address" rows="3"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-slate-700 dark:text-white"
                        placeholder="Company address...">{{ old('address', $company->address) }}</textarea>
                    @error('address')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $company->is_active) ?
                        'checked' : '' }}
                        class="form-checkbox h-5 w-5">
                        <span class="ml-2">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('admin.finance.companies.index') }}"
                    class="px-6 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 dark:border-slate-600 dark:hover:bg-slate-700">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update Company
                </button>
            </div>
        </form>
    </div>
    @endsection