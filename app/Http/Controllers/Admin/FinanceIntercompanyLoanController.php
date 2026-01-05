<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceIntercompanyLoan;
use App\Models\FinanceIntercompanyLoanPayment;
use App\Models\FinanceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FinanceIntercompanyLoanController extends Controller
{
    public function index(Request $request)
    {
        $query = FinanceIntercompanyLoan::query()
            ->with(['lenderCompany', 'borrowerCompany', 'createdBy', 'approvedBy']);

        if ($request->filled('lender_id')) {
            $query->where('lender_company_id', $request->lender_id);
        }

        if ($request->filled('borrower_id')) {
            $query->where('borrower_company_id', $request->borrower_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('fiscal_year')) {
            $query->where('fiscal_year_bs', $request->fiscal_year);
        }

        $loans = $query->latest('loan_date_bs')->paginate(20);
        $companies = FinanceCompany::active()->orderBy('name')->get();

        return view('admin.finance.intercompany-loans.index', compact('loans', 'companies'));
    }

    public function create()
    {
        $companies = FinanceCompany::active()->orderBy('name')->get();
        return view('admin.finance.intercompany-loans.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lender_company_id' => 'required|exists:finance_companies,id',
            'borrower_company_id' => 'required|exists:finance_companies,id|different:lender_company_id',
            'loan_date_bs' => 'required|string|max:10',
            'loan_amount' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'due_date_bs' => 'nullable|string|max:10',
            'purpose' => 'nullable|string',
            'notes' => 'nullable|string',
            'fiscal_year_bs' => 'required|string|size:4',
        ]);

        DB::beginTransaction();
        try {
            $lastLoan = FinanceIntercompanyLoan::whereYear('created_at', date('Y'))
                ->orderBy('id', 'desc')->first();

            $nextNumber = $lastLoan ? (int)substr($lastLoan->loan_number, -6) + 1 : 1;
            $validated['loan_number'] = 'ICL-' . date('Y') . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $validated['interest_rate'] = $validated['interest_rate'] ?? 0.00;
            $validated['repaid_amount'] = 0;
            $validated['outstanding_balance'] = $validated['loan_amount'];
            $validated['status'] = 'active';
            $validated['created_by_user_id'] = Auth::id();

            $loan = FinanceIntercompanyLoan::create($validated);
            DB::commit();

            return redirect()->route('admin.finance.intercompany-loans.show', $loan)
                ->with('success', 'Intercompany loan created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create loan: ' . $e->getMessage());
        }
    }

    public function show(FinanceIntercompanyLoan $intercompanyLoan)
    {
        $intercompanyLoan->load(['lenderCompany', 'borrowerCompany', 'payments', 'createdBy', 'approvedBy']);

        $stats = [
            'total_payments' => $intercompanyLoan->payments->count(),
            'total_repaid' => $intercompanyLoan->repaid_amount,
            'outstanding' => $intercompanyLoan->outstanding_balance,
            'repayment_percentage' => $intercompanyLoan->loan_amount > 0
                ? ($intercompanyLoan->repaid_amount / $intercompanyLoan->loan_amount) * 100
                : 0,
        ];

        return view('admin.finance.intercompany-loans.show', compact('intercompanyLoan', 'stats'));
    }

    public function edit(FinanceIntercompanyLoan $intercompanyLoan)
    {
        if ($intercompanyLoan->payments()->count() > 0) {
            return back()->with('error', 'Cannot edit loan with existing payments.');
        }

        $companies = FinanceCompany::active()->orderBy('name')->get();
        return view('admin.finance.intercompany-loans.edit', compact('intercompanyLoan', 'companies'));
    }

    public function update(Request $request, FinanceIntercompanyLoan $intercompanyLoan)
    {
        if ($intercompanyLoan->payments()->count() > 0) {
            return back()->with('error', 'Cannot update loan with existing payments.');
        }

        $validated = $request->validate([
            'lender_company_id' => 'required|exists:finance_companies,id',
            'borrower_company_id' => 'required|exists:finance_companies,id|different:lender_company_id',
            'loan_date_bs' => 'required|string|max:10',
            'loan_amount' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'due_date_bs' => 'nullable|string|max:10',
            'purpose' => 'nullable|string',
            'notes' => 'nullable|string',
            'fiscal_year_bs' => 'required|string|size:4',
        ]);

        $validated['interest_rate'] = $validated['interest_rate'] ?? 0.00;
        $validated['outstanding_balance'] = $validated['loan_amount'];

        $intercompanyLoan->update($validated);

        return redirect()->route('admin.finance.intercompany-loans.show', $intercompanyLoan)
            ->with('success', 'Loan updated successfully.');
    }

    public function destroy(FinanceIntercompanyLoan $intercompanyLoan)
    {
        $paymentsCount = $intercompanyLoan->payments()->count();

        if ($paymentsCount > 0) {
            return back()->with('error', "Cannot delete loan with {$paymentsCount} existing payment(s).");
        }

        // Check if loan has outstanding balance
        if ($intercompanyLoan->outstanding_balance > 0) {
            return back()->with('error', 'Cannot delete loan with outstanding balance of NPR ' . number_format($intercompanyLoan->outstanding_balance, 2) . '. Please settle all payments first.');
        }

        // Only allow deletion of draft or cancelled loans
        if (!in_array($intercompanyLoan->status, ['draft', 'cancelled'])) {
            return back()->with('error', 'Only draft or cancelled loans can be deleted. Current status: ' . $intercompanyLoan->status);
        }

        $intercompanyLoan->delete();

        return redirect()->route('admin.finance.intercompany-loans.index')
            ->with('success', 'Loan deleted successfully.');
    }

    public function approve(FinanceIntercompanyLoan $intercompanyLoan)
    {
        $intercompanyLoan->update(['approved_by_user_id' => Auth::id()]);
        return back()->with('success', 'Loan approved successfully.');
    }

    public function recordPayment(Request $request, FinanceIntercompanyLoan $intercompanyLoan)
    {
        $validated = $request->validate([
            'payment_date_bs' => 'required|string|max:10',
            'amount' => 'required|numeric|min:0|max:' . $intercompanyLoan->outstanding_balance,
            'payment_method' => 'required|string|max:50',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $payment = $intercompanyLoan->payments()->create([
                'payment_date_bs' => $validated['payment_date_bs'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'recorded_by_user_id' => Auth::id(),
            ]);

            $newRepaidAmount = $intercompanyLoan->repaid_amount + $validated['amount'];
            $newOutstanding = $intercompanyLoan->loan_amount - $newRepaidAmount;

            $status = 'active';
            if ($newOutstanding == 0) {
                $status = 'fully_repaid';
            } elseif ($newRepaidAmount > 0) {
                $status = 'partially_repaid';
            }

            $intercompanyLoan->update([
                'repaid_amount' => $newRepaidAmount,
                'outstanding_balance' => $newOutstanding,
                'status' => $status,
            ]);

            DB::commit();

            return back()->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    public function writeOff(FinanceIntercompanyLoan $intercompanyLoan)
    {
        $intercompanyLoan->update(['status' => 'written_off']);
        return back()->with('success', 'Loan written off successfully.');
    }
}
