@extends('admin.layouts.app')

@section('title', 'Create Company')
@section('page-title', 'Create Company')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.finance.companies.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i> Back to Companies
        </a>
    </div>

    <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-6">Create New Company</h2>

    <form action="{{ route('admin.finance.companies.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Company Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Company Type *</label>
                <select name="type" required
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                    <option value="holding" {{ old('type')=='holding' ? 'selected' : '' }}>Holding Company</option>
                    <option value="subsidiary" {{ old('type')=='subsidiary' ? 'selected' : '' }}>Subsidiary</option>
                    <option value="independent" {{ old('type')=='independent' ? 'selected' : '' }}>Independent</option>
                </select>
                @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Parent Company</label>
                <select name="parent_company_id"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                    <option value="">None</option>
                    @foreach($parentCompanies as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_company_id')==$parent->id ? 'selected' : '' }}>
                        {{ $parent->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">PAN Number</label>
                <input type="text" name="pan_number" value="{{ old('pan_number') }}"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Contact Email</label>
                <input type="email" name="contact_email" value="{{ old('contact_email') }}"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Contact Phone</label>
                <input type="text" name="contact_phone" value="{{ old('contact_phone') }}"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Established Date
                    (BS)</label>
                <input type="text" name="established_date_bs" value="{{ old('established_date_bs') }}"
                    placeholder="2081-04-15"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Fiscal Year Start Month
                    *</label>
                <select name="fiscal_year_start_month" required
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                    @for($i = 1; $i <= 12; $i++) <option value="{{ $i }}" {{ old('fiscal_year_start_month', 4)==$i
                        ? 'selected' : '' }}>
                        {{ $i }} ({{ ['', 'Baishakh', 'Jestha', 'Ashadh', 'Shrawan', 'Bhadra', 'Ashwin', 'Kartik',
                        'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra'][$i] }})
                        </option>
                        @endfor
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Address</label>
                <textarea name="address" rows="3"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">{{ old('address') }}</textarea>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="mr-2 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300">Active</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('admin.finance.companies.index') }}"
                class="px-6 py-2 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                Create Company
            </button>
        </div>
    </form>
</div>
@endsection