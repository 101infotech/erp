<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmCompany;
use App\Models\HrmDepartment;
use App\Models\FinanceCompany;

class HrmOrganizationController extends Controller
{
    /**
     * Display organization management page (companies and departments)
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'companies');

        $companies = HrmCompany::withCount('employees')
            ->orderBy('name')
            ->get();

        $departments = HrmDepartment::with('company')
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        return view('admin.hrm.organization.index', compact('companies', 'departments', 'tab'));
    }

    public function create()
    {
        $financeCompanies = FinanceCompany::orderBy('name')->get();
        return view('admin.hrm.organization.create', compact('financeCompanies'));
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
            ->route('admin.hrm.organization.index')
            ->with('success', 'Company created successfully.');
    }

    public function show(HrmCompany $company)
    {
        $company->loadCount(['employees', 'departments']);
        $company->load(['departments' => function ($query) {
            $query->withCount('employees');
        }]);
        return view('admin.hrm.organization.show', compact('company'));
    }

    public function edit(HrmCompany $company)
    {
        $financeCompanies = FinanceCompany::orderBy('name')->get();
        return view('admin.hrm.organization.edit', compact('company', 'financeCompanies'));
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
            ->route('admin.hrm.organization.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(HrmCompany $company)
    {
        $company->delete();

        return redirect()
            ->route('admin.hrm.organization.index')
            ->with('success', 'Company deleted successfully.');
    }
}
