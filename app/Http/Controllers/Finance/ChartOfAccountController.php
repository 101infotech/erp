<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceChartOfAccount;
use App\Models\FinanceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChartOfAccountController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->input('company_id');

        $query = FinanceChartOfAccount::with(['company', 'parentAccount'])
            ->when($companyId, fn($q) => $q->forCompany($companyId))
            ->when($request->input('type'), fn($q) => $q->byType($request->input('type')))
            ->when($request->input('active_only'), fn($q) => $q->active())
            ->orderBy('account_code');

        $accounts = $query->get();

        // Build hierarchy
        $hierarchical = $this->buildAccountHierarchy($accounts);

        $companies = FinanceCompany::active()->get();

        return view('finance.chart-of-accounts.index', compact('accounts', 'hierarchical', 'companies'));
    }

    public function create()
    {
        $companies = FinanceCompany::active()->get();
        $parentAccounts = FinanceChartOfAccount::active()->parentAccounts()->get();

        $accountTypes = ['asset', 'liability', 'equity', 'revenue', 'expense'];
        $accountSubtypes = [
            'current_asset',
            'fixed_asset',
            'current_liability',
            'long_term_liability',
            'equity',
            'revenue',
            'cost_of_goods_sold',
            'operating_expense',
            'other_income',
            'other_expense',
            'accumulated_depreciation',
            'depreciation_expense'
        ];

        return view('finance.chart-of-accounts.create', compact('companies', 'parentAccounts', 'accountTypes', 'accountSubtypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:finance_companies,id',
            'account_code' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'account_subtype' => 'required|string',
            'normal_balance' => 'required|in:debit,credit',
            'parent_account_id' => 'nullable|exists:finance_chart_of_accounts,id',
            'opening_balance' => 'nullable|numeric',
            'fiscal_year_bs' => 'required|string',
            'is_control_account' => 'boolean',
            'is_contra_account' => 'boolean',
            'allow_manual_entry' => 'boolean',
            'is_taxable' => 'boolean',
            'show_in_bs' => 'boolean',
            'show_in_pl' => 'boolean',
        ]);

        // Check for unique code within company
        $exists = FinanceChartOfAccount::where('company_id', $validated['company_id'])
            ->where('account_code', $validated['account_code'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['account_code' => 'Account code already exists for this company'])->withInput();
        }

        // Calculate level
        $level = 0;
        if ($request->parent_account_id) {
            $parent = FinanceChartOfAccount::find($request->parent_account_id);
            $level = $parent->level + 1;
        }

        $account = FinanceChartOfAccount::create([
            ...$validated,
            'level' => $level,
            'current_balance' => $validated['opening_balance'] ?? 0,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('finance.chart-of-accounts.index')
            ->with('success', 'Chart of Account created successfully');
    }

    public function show($id)
    {
        $account = FinanceChartOfAccount::with(['company', 'parentAccount', 'childAccounts', 'journalEntryLines.journalEntry'])
            ->findOrFail($id);

        return view('finance.chart-of-accounts.show', compact('account'));
    }

    public function edit($id)
    {
        $account = FinanceChartOfAccount::findOrFail($id);
        $companies = FinanceCompany::active()->get();
        $parentAccounts = FinanceChartOfAccount::active()
            ->where('id', '!=', $id)
            ->parentAccounts()
            ->get();

        $accountTypes = ['asset', 'liability', 'equity', 'revenue', 'expense'];
        $accountSubtypes = [
            'current_asset',
            'fixed_asset',
            'current_liability',
            'long_term_liability',
            'equity',
            'revenue',
            'cost_of_goods_sold',
            'operating_expense',
            'other_income',
            'other_expense',
            'accumulated_depreciation',
            'depreciation_expense'
        ];

        return view('finance.chart-of-accounts.edit', compact('account', 'companies', 'parentAccounts', 'accountTypes', 'accountSubtypes'));
    }

    public function update(Request $request, $id)
    {
        $account = FinanceChartOfAccount::findOrFail($id);

        if ($account->is_system_account) {
            return back()->withErrors(['error' => 'Cannot edit system accounts'])->withInput();
        }

        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_account_id' => 'nullable|exists:finance_chart_of_accounts,id',
            'allow_manual_entry' => 'boolean',
            'is_active' => 'boolean',
            'is_taxable' => 'boolean',
            'show_in_bs' => 'boolean',
            'show_in_pl' => 'boolean',
        ]);

        // Recalculate level if parent changed
        if ($request->parent_account_id != $account->parent_account_id) {
            $level = 0;
            if ($request->parent_account_id) {
                $parent = FinanceChartOfAccount::find($request->parent_account_id);
                $level = $parent->level + 1;
            }
            $validated['level'] = $level;
        }

        $account->update([
            ...$validated,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('finance.chart-of-accounts.index')
            ->with('success', 'Chart of Account updated successfully');
    }

    public function destroy($id)
    {
        $account = FinanceChartOfAccount::findOrFail($id);

        if ($account->is_system_account) {
            return back()->withErrors(['error' => 'Cannot delete system accounts']);
        }

        if ($account->journalEntryLines()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete account with journal entries']);
        }

        if ($account->childAccounts()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete account with child accounts']);
        }

        $account->delete();

        return redirect()->route('finance.chart-of-accounts.index')
            ->with('success', 'Chart of Account deleted successfully');
    }

    protected function buildAccountHierarchy($accounts, $parentId = null)
    {
        $hierarchy = [];

        foreach ($accounts->where('parent_account_id', $parentId) as $account) {
            $account->children = $this->buildAccountHierarchy($accounts, $account->id);
            $hierarchy[] = $account;
        }

        return $hierarchy;
    }
}
