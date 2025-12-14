<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrmCompany;
use Illuminate\Http\Request;

class HrmCompanyController extends Controller
{
    public function index()
    {
        $companies = HrmCompany::withCount(['employees', 'departments'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.hrm.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.hrm.companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        HrmCompany::create($validated);

        return redirect()
            ->route('admin.hrm.companies.index')
            ->with('success', 'Company created successfully.');
    }

    public function show(HrmCompany $company)
    {
        $company->loadCount(['employees', 'departments']);
        return view('admin.hrm.companies.show', compact('company'));
    }

    public function edit(HrmCompany $company)
    {
        return view('admin.hrm.companies.edit', compact('company'));
    }

    public function update(Request $request, HrmCompany $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $company->update($validated);

        return redirect()
            ->route('admin.hrm.companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(HrmCompany $company)
    {
        $company->delete();

        return redirect()
            ->route('admin.hrm.companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}
