@extends('admin.layouts.app')

@section('title', 'Add Company')
@section('page-title', 'Add Company')

@section('content')
<div class="max-w-3xl mx-auto bg-slate-800 border border-slate-700 rounded-lg p-6">
    <form method="POST" action="{{ route('admin.hrm.companies.store') }}" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Company Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-lime-500" />
            @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Contact Email</label>
            <input type="email" name="contact_email" value="{{ old('contact_email') }}"
                class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-lime-500" />
            @error('contact_email') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Address</label>
            <textarea name="address" rows="3"
                class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-lime-500">{{ old('address') }}</textarea>
            @error('address') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Finance Company *</label>
            <select name="finance_company_id" required
                class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-lime-500">
                <option value="">Select Finance Company</option>
                @foreach($financeCompanies as $f)
                <option value="{{ $f->id }}" {{ old('finance_company_id')==$f->id ? 'selected' : '' }}>
                    {{ $f->name }}
                </option>
                @endforeach
            </select>
            @error('finance_company_id') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.hrm.companies.index') }}"
                class="px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-white font-semibold">Cancel</a>
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold">Create
                Company</button>
        </div>
    </form>
</div>
@endsection