<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrmCompany;
use App\Models\FinanceCompany;
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
        $financeCompanies = FinanceCompany::orderBy('name')->get();
        return view('admin.hrm.companies.create', compact('financeCompanies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'finance_company_id' => 'required|exists:finance_companies,id|unique:hrm_companies,finance_company_id',
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
        $financeCompanies = FinanceCompany::orderBy('name')->get();
        return view('admin.hrm.companies.edit', compact('company', 'financeCompanies'));
    }

    public function update(Request $request, HrmCompany $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'finance_company_id' => 'required|exists:finance_companies,id|unique:hrm_companies,finance_company_id,' . $company->id,
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
