<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceBudget;
use App\Models\FinanceCompany;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use Illuminate\Http\Request;

class FinanceBudgetController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $fiscalYear = $request->get('fiscal_year_bs', '2081');

        $query = FinanceBudget::with(['company', 'category'])
            ->where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear);

        if ($request->filled('budget_type')) {
            $query->where('budget_type', $request->budget_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $budgets = $query->latest()->paginate(20);
        $companies = FinanceCompany::where('is_active', true)->get();

        $totalBudgeted = $budgets->sum('budgeted_amount');
        $totalActual = $budgets->sum('actual_amount');
        $totalVariance = $totalActual - $totalBudgeted;

        return view('admin.finance.budgets.index', compact(
            'budgets',
            'companies',
            'companyId',
            'fiscalYear',
            'totalBudgeted',
            'totalActual',
            'totalVariance'
        ));
    }

    public function create(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $companies = FinanceCompany::where('is_active', true)->get();
        $categories = FinanceCategory::where('company_id', $companyId)
            ->where('type', 'expense')
            ->where('is_active', true)
            ->get();

        return view('admin.finance.budgets.create', compact('companies', 'categories', 'companyId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:finance_companies,id',
            'fiscal_year_bs' => 'required|string|size:4',
            'category_id' => 'nullable|exists:finance_categories,id',
            'budget_type' => 'required|in:monthly,quarterly,annual',
            'period' => 'required|integer|min:1',
            'budgeted_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,approved,active',
        ]);

        $validated['created_by_user_id'] = auth()->id();
        $validated['actual_amount'] = 0;
        $validated['variance'] = 0;
        $validated['variance_percentage'] = 0;

        FinanceBudget::create($validated);

        return redirect()->route('admin.finance.budgets.index', ['company_id' => $validated['company_id']])
            ->with('success', 'Budget created successfully.');
    }

    public function show(FinanceBudget $budget)
    {
        $budget->load(['company', 'category', 'createdBy', 'approvedBy']);

        $query = FinanceTransaction::where('company_id', $budget->company_id)
            ->where('fiscal_year_bs', $budget->fiscal_year_bs)
            ->where('transaction_type', 'expense');

        if ($budget->category_id) {
            $query->where('category_id', $budget->category_id);
        }

        if ($budget->budget_type === 'monthly') {
            $query->where('fiscal_month_bs', $budget->period);
        }

        $actualExpenses = $query->sum('amount');

        $budget->update([
            'actual_amount' => $actualExpenses,
            'variance' => $actualExpenses - $budget->budgeted_amount,
            'variance_percentage' => $budget->budgeted_amount > 0
                ? (($actualExpenses - $budget->budgeted_amount) / $budget->budgeted_amount) * 100
                : 0,
        ]);

        $budget->refresh();

        $recentTransactions = $query->with(['category', 'debitAccount'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.finance.budgets.show', compact('budget', 'recentTransactions'));
    }

    public function edit(FinanceBudget $budget)
    {
        $companies = FinanceCompany::where('is_active', true)->get();
        $categories = FinanceCategory::where('company_id', $budget->company_id)
            ->where('type', 'expense')
            ->where('is_active', true)
            ->get();

        return view('admin.finance.budgets.edit', compact('budget', 'companies', 'categories'));
    }

    public function update(Request $request, FinanceBudget $budget)
    {
        $validated = $request->validate([
            'fiscal_year_bs' => 'required|string|size:4',
            'category_id' => 'nullable|exists:finance_categories,id',
            'budget_type' => 'required|in:monthly,quarterly,annual',
            'period' => 'required|integer|min:1',
            'budgeted_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,approved,active',
        ]);

        $budget->update($validated);

        return redirect()->route('admin.finance.budgets.index', ['company_id' => $budget->company_id])
            ->with('success', 'Budget updated successfully.');
    }

    public function destroy(FinanceBudget $budget)
    {
        $companyId = $budget->company_id;
        $budget->delete();

        return redirect()->route('admin.finance.budgets.index', ['company_id' => $companyId])
            ->with('success', 'Budget deleted successfully.');
    }
}
