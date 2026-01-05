<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceCompany;
use Illuminate\Http\Request;

class FinanceCompanyController extends Controller
{
    public function index()
    {
        $companies = FinanceCompany::with('parentCompany')
            ->withCount(['transactions', 'sales', 'purchases'])
            ->latest()
            ->paginate(20);

        return view('admin.finance.companies.index', compact('companies'));
    }

    public function create()
    {
        $parentCompanies = FinanceCompany::where('is_active', true)->get();
        return view('admin.finance.companies.create', compact('parentCompanies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:holding,subsidiary,independent',
            'parent_company_id' => 'nullable|exists:finance_companies,id',
            'pan_number' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'established_date_bs' => 'nullable|string|max:10',
            'fiscal_year_start_month' => 'required|integer|min:1|max:12',
            'is_active' => 'boolean',
        ]);

        FinanceCompany::create($validated);

        return redirect()->route('admin.finance.companies.index')
            ->with('success', 'Company created successfully.');
    }

    public function show(FinanceCompany $company)
    {
        $company->load(['parentCompany', 'subsidiaries', 'transactions', 'sales', 'purchases']);
        return view('admin.finance.companies.show', compact('company'));
    }

    public function edit(FinanceCompany $company)
    {
        $parentCompanies = FinanceCompany::where('id', '!=', $company->id)
            ->where('is_active', true)
            ->get();
        return view('admin.finance.companies.edit', compact('company', 'parentCompanies'));
    }

    public function update(Request $request, FinanceCompany $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:holding,subsidiary,independent',
            'parent_company_id' => 'nullable|exists:finance_companies,id',
            'pan_number' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'established_date_bs' => 'nullable|string|max:10',
            'fiscal_year_start_month' => 'required|integer|min:1|max:12',
            'is_active' => 'boolean',
        ]);

        $company->update($validated);

        return redirect()->route('admin.finance.companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(FinanceCompany $company)
    {
        // Check for related records before deletion
        if ($company->transactions()->exists()) {
            return back()->with('error', 'Cannot delete company with existing transactions. Please delete all transactions first or deactivate the company instead.');
        }

        if ($company->sales()->exists()) {
            return back()->with('error', 'Cannot delete company with existing sales records. Please delete all sales first or deactivate the company instead.');
        }

        if ($company->purchases()->exists()) {
            return back()->with('error', 'Cannot delete company with existing purchases. Please delete all purchases first or deactivate the company instead.');
        }

        if ($company->subsidiaries()->exists()) {
            return back()->with('error', 'Cannot delete company with subsidiaries. Please reassign or delete subsidiaries first.');
        }

        $company->delete();
        return redirect()->route('admin.finance.companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}
