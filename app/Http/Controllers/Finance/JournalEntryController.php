<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceJournalEntry;
use App\Models\FinanceJournalEntryLine;
use App\Models\FinanceChartOfAccount;
use App\Models\FinanceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = FinanceJournalEntry::with(['company', 'lines.account'])
            ->when($request->company_id, fn($q) => $q->forCompany($request->company_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->fiscal_year, fn($q) => $q->byFiscalYear($request->fiscal_year))
            ->when($request->entry_type, fn($q) => $q->byType($request->entry_type))
            ->orderByDesc('entry_date_bs');

        $entries = $query->paginate(20);
        $companies = FinanceCompany::active()->get();

        return view('finance.journal-entries.index', compact('entries', 'companies'));
    }

    public function create()
    {
        $companies = FinanceCompany::active()->get();
        $accounts = FinanceChartOfAccount::active()->manualEntry()->get();

        $entryTypes = [
            'manual',
            'asset_purchase',
            'depreciation',
            'sales',
            'purchase',
            'payroll',
            'payment',
            'receipt',
            'adjustment',
            'closing',
            'opening',
            'reversal'
        ];

        return view('finance.journal-entries.create', compact('companies', 'accounts', 'entryTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:finance_companies,id',
            'reference_number' => 'nullable|string|max:100',
            'entry_date_bs' => 'required|string',
            'fiscal_year_bs' => 'required|string',
            'fiscal_month_bs' => 'required|integer|min:1|max:12',
            'entry_type' => 'required|string',
            'description' => 'required|string',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:finance_chart_of_accounts,id',
            'lines.*.description' => 'nullable|string',
            'lines.*.debit_amount' => 'nullable|numeric|min:0',
            'lines.*.credit_amount' => 'nullable|numeric|min:0',
            'lines.*.department' => 'nullable|string',
        ]);

        // Validate balanced entry
        $totalDebit = collect($request->lines)->sum('debit_amount');
        $totalCredit = collect($request->lines)->sum('credit_amount');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withErrors(['lines' => 'Journal entry must be balanced (Debits = Credits)'])->withInput();
        }

        return DB::transaction(function () use ($validated, $totalDebit, $totalCredit) {
            // Generate entry number
            $entryNumber = $this->generateEntryNumber(
                $validated['company_id'],
                $validated['fiscal_year_bs'],
                $validated['fiscal_month_bs']
            );

            // Create journal entry
            $entry = FinanceJournalEntry::create([
                'company_id' => $validated['company_id'],
                'entry_number' => $entryNumber,
                'reference_number' => $validated['reference_number'],
                'entry_date_bs' => $validated['entry_date_bs'],
                'fiscal_year_bs' => $validated['fiscal_year_bs'],
                'fiscal_month_bs' => $validated['fiscal_month_bs'],
                'entry_type' => $validated['entry_type'],
                'description' => $validated['description'],
                'notes' => $validated['notes'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            // Create lines
            foreach ($validated['lines'] as $index => $line) {
                FinanceJournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'line_number' => $index + 1,
                    'description' => $line['description'] ?? $validated['description'],
                    'debit_amount' => $line['debit_amount'] ?? 0,
                    'credit_amount' => $line['credit_amount'] ?? 0,
                    'department' => $line['department'] ?? null,
                ]);
            }

            return redirect()->route('finance.journal-entries.show', $entry->id)
                ->with('success', 'Journal entry created successfully');
        });
    }

    public function show($id)
    {
        $entry = FinanceJournalEntry::with(['company', 'lines.account', 'postedBy', 'reversedBy', 'approvedBy'])
            ->findOrFail($id);

        return view('finance.journal-entries.show', compact('entry'));
    }

    public function edit($id)
    {
        $entry = FinanceJournalEntry::with('lines')->findOrFail($id);

        if ($entry->status !== 'draft') {
            return back()->withErrors(['error' => 'Only draft entries can be edited']);
        }

        $companies = FinanceCompany::active()->get();
        $accounts = FinanceChartOfAccount::active()->manualEntry()->get();

        return view('finance.journal-entries.edit', compact('entry', 'companies', 'accounts'));
    }

    public function update(Request $request, $id)
    {
        $entry = FinanceJournalEntry::findOrFail($id);

        if ($entry->status !== 'draft') {
            return back()->withErrors(['error' => 'Only draft entries can be updated']);
        }

        $validated = $request->validate([
            'reference_number' => 'nullable|string|max:100',
            'entry_date_bs' => 'required|string',
            'description' => 'required|string',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:finance_chart_of_accounts,id',
            'lines.*.description' => 'nullable|string',
            'lines.*.debit_amount' => 'nullable|numeric|min:0',
            'lines.*.credit_amount' => 'nullable|numeric|min:0',
        ]);

        // Validate balanced
        $totalDebit = collect($request->lines)->sum('debit_amount');
        $totalCredit = collect($request->lines)->sum('credit_amount');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withErrors(['lines' => 'Journal entry must be balanced'])->withInput();
        }

        return DB::transaction(function () use ($entry, $validated, $totalDebit, $totalCredit) {
            $entry->update([
                'reference_number' => $validated['reference_number'],
                'entry_date_bs' => $validated['entry_date_bs'],
                'description' => $validated['description'],
                'notes' => $validated['notes'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'updated_by' => Auth::id(),
            ]);

            // Delete old lines and create new
            $entry->lines()->delete();

            foreach ($validated['lines'] as $index => $line) {
                FinanceJournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'line_number' => $index + 1,
                    'description' => $line['description'] ?? $validated['description'],
                    'debit_amount' => $line['debit_amount'] ?? 0,
                    'credit_amount' => $line['credit_amount'] ?? 0,
                ]);
            }

            return redirect()->route('finance.journal-entries.show', $entry->id)
                ->with('success', 'Journal entry updated successfully');
        });
    }

    public function destroy($id)
    {
        $entry = FinanceJournalEntry::findOrFail($id);

        if ($entry->status !== 'draft') {
            return back()->withErrors(['error' => 'Only draft entries can be deleted']);
        }

        DB::transaction(function () use ($entry) {
            $entry->lines()->delete();
            $entry->delete();
        });

        return redirect()->route('finance.journal-entries.index')
            ->with('success', 'Journal entry deleted successfully');
    }

    public function post($id)
    {
        $entry = FinanceJournalEntry::with('lines')->findOrFail($id);

        if ($entry->status !== 'draft') {
            return back()->withErrors(['error' => 'Only draft entries can be posted']);
        }

        try {
            $entry->post(Auth::id());
            return back()->with('success', 'Journal entry posted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function reverse(Request $request, $id)
    {
        $entry = FinanceJournalEntry::findOrFail($id);

        $validated = $request->validate([
            'reversal_reason' => 'required|string',
        ]);

        try {
            $reversalEntry = $entry->reverse(Auth::id(), $validated['reversal_reason']);

            return redirect()->route('finance.journal-entries.show', $reversalEntry->id)
                ->with('success', 'Reversal entry created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    protected function generateEntryNumber($companyId, $fiscalYear, $fiscalMonth)
    {
        $count = FinanceJournalEntry::where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->where('fiscal_month_bs', $fiscalMonth)
            ->count();

        return 'JE-' . $fiscalYear . '-' . str_pad($fiscalMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }
}
