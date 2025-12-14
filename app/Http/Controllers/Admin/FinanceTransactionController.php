<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use App\Models\FinanceCompany;
use App\Models\FinanceCategory;
use App\Models\FinanceAccount;
use Illuminate\Http\Request;

class FinanceTransactionController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $fiscalYear = $request->get('fiscal_year', '2081');

        $query = FinanceTransaction::with(['company', 'category', 'debitAccount', 'creditAccount'])
            ->where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear);

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest('transaction_date_bs')->paginate(20);

        $companies = FinanceCompany::where('is_active', true)->get();

        return view('admin.finance.transactions.index', compact('transactions', 'companies', 'companyId', 'fiscalYear'));
    }

    public function create(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $companies = FinanceCompany::where('is_active', true)->get();
        $categories = FinanceCategory::where('company_id', $companyId)->get();
        $accounts = FinanceAccount::where('company_id', $companyId)->where('is_active', true)->get();

        return view('admin.finance.transactions.create', compact('companies', 'categories', 'accounts', 'companyId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:finance_companies,id',
            'transaction_number' => 'required|string|unique:finance_transactions,transaction_number',
            'transaction_date_bs' => 'required|string|max:10',
            'transaction_type' => 'required|in:income,expense,transfer,investment,loan',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'fiscal_year_bs' => 'required|string|size:4',
            'fiscal_month_bs' => 'required|integer|min:1|max:12',
            'category_id' => 'nullable|exists:finance_categories,id',
            'debit_account_id' => 'nullable|exists:finance_accounts,id',
            'credit_account_id' => 'nullable|exists:finance_accounts,id',
            'payment_reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        FinanceTransaction::create($validated);

        return redirect()->route('admin.finance.transactions.index', ['company_id' => $validated['company_id']])
            ->with('success', 'Transaction created successfully.');
    }

    public function show(FinanceTransaction $transaction)
    {
        $transaction->load(['company', 'category', 'debitAccount', 'creditAccount']);
        return view('admin.finance.transactions.show', compact('transaction'));
    }

    public function edit(FinanceTransaction $transaction)
    {
        $companies = FinanceCompany::where('is_active', true)->get();
        $categories = FinanceCategory::where('company_id', $transaction->company_id)->get();
        $accounts = FinanceAccount::where('company_id', $transaction->company_id)->where('is_active', true)->get();

        return view('admin.finance.transactions.edit', compact('transaction', 'companies', 'categories', 'accounts'));
    }

    public function update(Request $request, FinanceTransaction $transaction)
    {
        $validated = $request->validate([
            'transaction_date_bs' => 'required|string|max:10',
            'transaction_type' => 'required|in:income,expense,transfer,investment,loan',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'category_id' => 'nullable|exists:finance_categories,id',
            'debit_account_id' => 'nullable|exists:finance_accounts,id',
            'credit_account_id' => 'nullable|exists:finance_accounts,id',
            'payment_reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('admin.finance.transactions.index', ['company_id' => $transaction->company_id])
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(FinanceTransaction $transaction)
    {
        $companyId = $transaction->company_id;
        $transaction->delete();

        return redirect()->route('admin.finance.transactions.index', ['company_id' => $companyId])
            ->with('success', 'Transaction deleted successfully.');
    }
}
