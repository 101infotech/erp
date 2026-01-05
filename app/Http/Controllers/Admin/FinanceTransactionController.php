<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use App\Models\FinanceCompany;
use App\Models\FinanceCategory;
use App\Models\FinanceAccount;
use App\Services\Finance\AI\FinanceAiCategorizationService;
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

        $transaction = FinanceTransaction::create($validated);

        // AI Auto-categorization if category not provided
        if (!$transaction->category_id && config('finance_ai.enabled')) {
            try {
                $aiService = app(FinanceAiCategorizationService::class);
                $autoCategorized = $aiService->autoCategorize($transaction);

                if ($autoCategorized) {
                    $category = FinanceCategory::find($transaction->category_id);
                    $message = 'Transaction created and automatically categorized as "' . $category->name . '" by AI.';
                } else {
                    // Get suggestion for user review
                    $suggestion = $aiService->suggestCategory($transaction);
                    if ($suggestion && $suggestion['confidence'] >= config('finance_ai.categorization.suggestion_threshold', 0.70)) {
                        $category = FinanceCategory::find($suggestion['category_id']);
                        $message = 'Transaction created. AI suggests category: "' . $category->name . '" (Confidence: ' . round($suggestion['confidence'] * 100) . '%)';
                    } else {
                        $message = 'Transaction created successfully.';
                    }
                }
            } catch (\Exception $e) {
                \Log::error('AI categorization error', ['error' => $e->getMessage()]);
                $message = 'Transaction created successfully.';
            }
        } else {
            $message = 'Transaction created successfully.';
        }

        return redirect()->route('admin.finance.transactions.index', ['company_id' => $validated['company_id']])
            ->with('success', $message);
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
        // Prevent editing completed or approved transactions
        if (in_array($transaction->status, ['completed', 'approved'])) {
            return back()->with(
                'error',
                'Cannot edit completed or approved transactions. Create a reversal transaction instead.'
            );
        }

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
        // Only allow deletion of draft or pending transactions
        if (!in_array($transaction->status, ['draft', 'pending'])) {
            return back()->with('error', 'Only draft or pending transactions can be deleted. Current status: ' . $transaction->status . '. Please use reversal for completed transactions.');
        }

        $companyId = $transaction->company_id;
        $transaction->delete();

        return redirect()->route('admin.finance.transactions.index', ['company_id' => $companyId])
            ->with('success', 'Transaction deleted successfully.');
    }
}
