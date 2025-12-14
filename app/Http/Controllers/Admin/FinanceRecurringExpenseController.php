<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceRecurringExpense;
use App\Models\FinanceCompany;
use App\Models\FinanceCategory;
use App\Models\FinanceAccount;
use Illuminate\Http\Request;

class FinanceRecurringExpenseController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->get('company_id', 1);

        $query = FinanceRecurringExpense::with(['company', 'category', 'account'])
            ->where('company_id', $companyId);

        if ($request->filled('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $recurringExpenses = $query->latest()->paginate(20);
        $companies = FinanceCompany::where('is_active', true)->get();

        return view('admin.finance.recurring-expenses.index', compact('recurringExpenses', 'companies', 'companyId'));
    }

    public function create(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $companies = FinanceCompany::where('is_active', true)->get();
        $categories = FinanceCategory::where('company_id', $companyId)
            ->where('type', 'expense')
            ->where('is_active', true)
            ->get();
        $accounts = FinanceAccount::where('company_id', $companyId)
            ->where('account_type', 'expense')
            ->where('is_active', true)
            ->get();

        return view('admin.finance.recurring-expenses.create', compact('companies', 'categories', 'accounts', 'companyId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:finance_companies,id',
            'expense_name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:finance_categories,id',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,quarterly,annually',
            'start_date_bs' => 'required|string',
            'end_date_bs' => 'nullable|string',
            'payment_method' => 'required|string',
            'account_id' => 'nullable|exists:finance_accounts,id',
            'auto_create_transaction' => 'boolean',
            'next_due_date_bs' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['created_by_user_id'] = auth()->id();

        FinanceRecurringExpense::create($validated);

        return redirect()->route('admin.finance.recurring-expenses.index', ['company_id' => $validated['company_id']])
            ->with('success', 'Recurring expense created successfully.');
    }

    public function show(FinanceRecurringExpense $recurringExpense)
    {
        $recurringExpense->load(['company', 'category', 'account', 'createdBy']);

        return view('admin.finance.recurring-expenses.show', compact('recurringExpense'));
    }

    public function edit(FinanceRecurringExpense $recurringExpense)
    {
        $companies = FinanceCompany::where('is_active', true)->get();
        $categories = FinanceCategory::where('company_id', $recurringExpense->company_id)
            ->where('type', 'expense')
            ->where('is_active', true)
            ->get();
        $accounts = FinanceAccount::where('company_id', $recurringExpense->company_id)
            ->where('account_type', 'expense')
            ->where('is_active', true)
            ->get();

        return view('admin.finance.recurring-expenses.edit', compact('recurringExpense', 'companies', 'categories', 'accounts'));
    }

    public function update(Request $request, FinanceRecurringExpense $recurringExpense)
    {
        $validated = $request->validate([
            'expense_name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:finance_categories,id',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,quarterly,annually',
            'start_date_bs' => 'required|string',
            'end_date_bs' => 'nullable|string',
            'payment_method' => 'required|string',
            'account_id' => 'nullable|exists:finance_accounts,id',
            'auto_create_transaction' => 'boolean',
            'next_due_date_bs' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $recurringExpense->update($validated);

        return redirect()->route('admin.finance.recurring-expenses.index', ['company_id' => $recurringExpense->company_id])
            ->with('success', 'Recurring expense updated successfully.');
    }

    public function destroy(FinanceRecurringExpense $recurringExpense)
    {
        $companyId = $recurringExpense->company_id;
        $recurringExpense->delete();

        return redirect()->route('admin.finance.recurring-expenses.index', ['company_id' => $companyId])
            ->with('success', 'Recurring expense deleted successfully.');
    }
}
