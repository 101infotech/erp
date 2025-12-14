<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceFounderTransaction;
use App\Models\FinanceFounder;
use App\Models\FinanceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FinanceFounderTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = FinanceFounderTransaction::query()
            ->with(['founder', 'company', 'createdBy', 'approvedBy']);

        if ($request->filled('founder_id')) {
            $query->where('founder_id', $request->founder_id);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest('transaction_date_bs')->paginate(20);
        $founders = FinanceFounder::active()->orderBy('name')->get();
        $companies = FinanceCompany::active()->orderBy('name')->get();

        return view('admin.finance.founder-transactions.index', compact('transactions', 'founders', 'companies'));
    }

    public function create(Request $request)
    {
        $founders = FinanceFounder::active()->orderBy('name')->get();
        $companies = FinanceCompany::active()->orderBy('name')->get();
        $founderId = $request->get('founder_id');
        $companyId = $request->get('company_id');

        return view('admin.finance.founder-transactions.create', compact('founders', 'companies', 'founderId', 'companyId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'founder_id' => 'required|exists:finance_founders,id',
            'company_id' => 'required|exists:finance_companies,id',
            'transaction_date_bs' => 'required|string|max:10',
            'transaction_type' => 'required|in:investment,withdrawal',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:50',
            'payment_reference' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'fiscal_year_bs' => 'required|string|size:4',
        ]);

        DB::beginTransaction();
        try {
            $lastTransaction = FinanceFounderTransaction::whereYear('created_at', date('Y'))
                ->orderBy('id', 'desc')->first();

            $nextNumber = $lastTransaction ? (int)substr($lastTransaction->transaction_number, -6) + 1 : 1;
            $validated['transaction_number'] = 'FT-' . date('Y') . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $previousBalance = FinanceFounderTransaction::where('founder_id', $validated['founder_id'])
                ->where('company_id', $validated['company_id'])
                ->where('status', 'approved')
                ->orderBy('id', 'desc')
                ->value('running_balance') ?? 0;

            $validated['running_balance'] = $validated['transaction_type'] === 'investment'
                ? $previousBalance + $validated['amount']
                : $previousBalance - $validated['amount'];

            if ($request->hasFile('document')) {
                $validated['document_path'] = $request->file('document')->store('finance/founder-transactions', 'public');
            }

            $validated['created_by_user_id'] = Auth::id();
            $validated['status'] = 'pending';

            $transaction = FinanceFounderTransaction::create($validated);
            DB::commit();

            return redirect()->route('admin.finance.founder-transactions.show', $transaction)
                ->with('success', 'Transaction created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create transaction: ' . $e->getMessage());
        }
    }

    public function show(FinanceFounderTransaction $founderTransaction)
    {
        $founderTransaction->load(['founder', 'company', 'createdBy', 'approvedBy']);
        return view('admin.finance.founder-transactions.show', compact('founderTransaction'));
    }

    public function edit(FinanceFounderTransaction $founderTransaction)
    {
        if ($founderTransaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be edited.');
        }

        $founders = FinanceFounder::active()->orderBy('name')->get();
        $companies = FinanceCompany::active()->orderBy('name')->get();

        return view('admin.finance.founder-transactions.edit', compact('founderTransaction', 'founders', 'companies'));
    }

    public function update(Request $request, FinanceFounderTransaction $founderTransaction)
    {
        if ($founderTransaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be updated.');
        }

        $validated = $request->validate([
            'founder_id' => 'required|exists:finance_founders,id',
            'company_id' => 'required|exists:finance_companies,id',
            'transaction_date_bs' => 'required|string|max:10',
            'transaction_type' => 'required|in:investment,withdrawal',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:50',
            'payment_reference' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'fiscal_year_bs' => 'required|string|size:4',
        ]);

        DB::beginTransaction();
        try {
            $previousBalance = FinanceFounderTransaction::where('founder_id', $validated['founder_id'])
                ->where('company_id', $validated['company_id'])
                ->where('status', 'approved')
                ->where('id', '<', $founderTransaction->id)
                ->orderBy('id', 'desc')
                ->value('running_balance') ?? 0;

            $validated['running_balance'] = $validated['transaction_type'] === 'investment'
                ? $previousBalance + $validated['amount']
                : $previousBalance - $validated['amount'];

            if ($request->hasFile('document')) {
                if ($founderTransaction->document_path) {
                    Storage::disk('public')->delete($founderTransaction->document_path);
                }
                $validated['document_path'] = $request->file('document')->store('finance/founder-transactions', 'public');
            }

            $founderTransaction->update($validated);
            DB::commit();

            return redirect()->route('admin.finance.founder-transactions.show', $founderTransaction)
                ->with('success', 'Transaction updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update transaction: ' . $e->getMessage());
        }
    }

    public function destroy(FinanceFounderTransaction $founderTransaction)
    {
        if ($founderTransaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be deleted.');
        }

        if ($founderTransaction->document_path) {
            Storage::disk('public')->delete($founderTransaction->document_path);
        }

        $founderTransaction->delete();

        return redirect()->route('admin.finance.founder-transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    public function approve(FinanceFounderTransaction $founderTransaction)
    {
        if ($founderTransaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be approved.');
        }

        $founderTransaction->update([
            'status' => 'approved',
            'approved_by_user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Transaction approved successfully.');
    }

    public function cancel(FinanceFounderTransaction $founderTransaction)
    {
        if ($founderTransaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be cancelled.');
        }

        $founderTransaction->update(['status' => 'cancelled']);
        return back()->with('success', 'Transaction cancelled successfully.');
    }

    public function settle(Request $request, FinanceFounderTransaction $founderTransaction)
    {
        $request->validate(['settled_date_bs' => 'required|string|max:10']);

        $founderTransaction->update([
            'is_settled' => true,
            'settled_date_bs' => $request->settled_date_bs,
        ]);

        return back()->with('success', 'Transaction marked as settled.');
    }

    public function download(FinanceFounderTransaction $founderTransaction)
    {
        if (!$founderTransaction->document_path) {
            return back()->with('error', 'No document available.');
        }

        $path = storage_path('app/public/' . $founderTransaction->document_path);

        if (!file_exists($path)) {
            return back()->with('error', 'Document file not found.');
        }

        return response()->download($path);
    }
}
