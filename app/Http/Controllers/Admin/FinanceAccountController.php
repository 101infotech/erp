<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceAccount;
use App\Models\FinanceCompany;
use Illuminate\Http\Request;

class FinanceAccountController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $accounts = FinanceAccount::with(['company', 'parentAccount'])
            ->where('company_id', $companyId)
            ->orderBy('account_code')
            ->paginate(50);

        $companies = FinanceCompany::where('is_active', true)->get();

        return view('admin.finance.accounts.index', compact('accounts', 'companies', 'companyId'));
    }

    public function create(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $companies = FinanceCompany::where('is_active', true)->get();
        $parentAccounts = FinanceAccount::where('company_id', $companyId)
            ->whereNull('parent_account_id')
            ->get();

        return view('admin.finance.accounts.create', compact('companies', 'parentAccounts', 'companyId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:finance_companies,id',
            'account_name' => 'required|string|max:255',
            'account_code' => 'required|string|max:50',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_account_id' => 'nullable|exists:finance_accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        FinanceAccount::create($validated);

        return redirect()->route('admin.finance.accounts.index', ['company_id' => $validated['company_id']])
            ->with('success', 'Account created successfully.');
    }

    public function show(FinanceAccount $account)
    {
        $account->load(['company', 'parentAccount', 'subAccounts']);
        return view('admin.finance.accounts.show', compact('account'));
    }

    public function edit(FinanceAccount $account)
    {
        $companies = FinanceCompany::where('is_active', true)->get();
        $parentAccounts = FinanceAccount::where('company_id', $account->company_id)
            ->where('id', '!=', $account->id)
            ->whereNull('parent_account_id')
            ->get();

        return view('admin.finance.accounts.edit', compact('account', 'companies', 'parentAccounts'));
    }

    public function update(Request $request, FinanceAccount $account)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:finance_companies,id',
            'account_name' => 'required|string|max:255',
            'account_code' => 'required|string|max:50',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_account_id' => 'nullable|exists:finance_accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $account->update($validated);

        return redirect()->route('admin.finance.accounts.index', ['company_id' => $validated['company_id']])
            ->with('success', 'Account updated successfully.');
    }

    public function destroy(FinanceAccount $account)
    {
        $companyId = $account->company_id;
        $account->delete();

        return redirect()->route('admin.finance.accounts.index', ['company_id' => $companyId])
            ->with('success', 'Account deleted successfully.');
    }
}
