<?php

namespace App\Services\Finance;

use App\Models\FinanceTransaction;
use App\Models\FinanceCompany;
use App\Models\FinanceAccount;
use App\Models\User;
use App\Services\NepalCalendarService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class FinanceTransactionService
{
    protected NepalCalendarService $calendar;

    public function __construct(NepalCalendarService $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * Create a new financial transaction with double-entry accounting
     */
    public function createTransaction(array $data, ?User $user = null): FinanceTransaction
    {
        return DB::transaction(function () use ($data, $user) {
            // Validate double-entry: must have both debit and credit accounts
            if (empty($data['debit_account_id']) || empty($data['credit_account_id'])) {
                throw new \Exception('Both debit and credit accounts are required for double-entry accounting');
            }

            // Auto-set fiscal year and month from transaction date
            if (!isset($data['fiscal_year_bs']) || !isset($data['fiscal_month_bs'])) {
                [$year, $month, $day] = explode('-', $data['transaction_date_bs']);
                $data['fiscal_year_bs'] = $year;
                $data['fiscal_month_bs'] = (int)$month;
            }

            // Set handling user
            if ($user && !isset($data['created_by_user_id'])) {
                $data['created_by_user_id'] = $user->id;
            }

            // Set initial status
            if (!isset($data['status'])) {
                $data['status'] = 'draft';
            }

            // Handle document upload if provided
            if (isset($data['document']) && $data['document'] instanceof UploadedFile) {
                $data['document_path'] = $this->uploadDocument($data['document'], $data['company_id']);
                unset($data['document']);
            }

            // Create transaction
            $transaction = FinanceTransaction::create($data);

            // If auto-approve is enabled (for small amounts or specific types)
            if ($this->shouldAutoApprove($transaction)) {
                $transaction->update([
                    'status' => 'approved',
                    'approved_by_user_id' => $user?->id,
                ]);
            }

            return $transaction->fresh(['company', 'category', 'debitAccount', 'creditAccount', 'createdBy']);
        });
    }

    /**
     * Update an existing transaction
     */
    public function updateTransaction(FinanceTransaction $transaction, array $data): FinanceTransaction
    {
        // Prevent editing if already completed
        if ($transaction->status === 'completed') {
            throw new \Exception('Cannot edit completed transactions');
        }

        return DB::transaction(function () use ($transaction, $data) {
            // Handle document upload if provided
            if (isset($data['document']) && $data['document'] instanceof UploadedFile) {
                // Delete old document
                if ($transaction->document_path) {
                    Storage::disk('public')->delete($transaction->document_path);
                }

                $data['document_path'] = $this->uploadDocument($data['document'], $transaction->company_id);
                unset($data['document']);
            }

            // Update fiscal year/month if transaction date changed
            if (isset($data['transaction_date_bs']) && $data['transaction_date_bs'] !== $transaction->transaction_date_bs) {
                [$year, $month, $day] = explode('-', $data['transaction_date_bs']);
                $data['fiscal_year_bs'] = $year;
                $data['fiscal_month_bs'] = (int)$month;
            }

            $transaction->update($data);

            return $transaction->fresh(['company', 'category', 'debitAccount', 'creditAccount']);
        });
    }

    /**
     * Approve a transaction
     */
    public function approveTransaction(FinanceTransaction $transaction, User $user): FinanceTransaction
    {
        if ($transaction->status !== 'pending' && $transaction->status !== 'draft') {
            throw new \Exception('Only pending or draft transactions can be approved');
        }

        $transaction->update([
            'status' => 'approved',
            'approved_by_user_id' => $user->id,
        ]);

        return $transaction->fresh();
    }

    /**
     * Complete a transaction and update account balances
     */
    public function completeTransaction(FinanceTransaction $transaction): FinanceTransaction
    {
        if ($transaction->status !== 'approved') {
            throw new \Exception('Only approved transactions can be completed');
        }

        return DB::transaction(function () use ($transaction) {
            $transaction->update(['status' => 'completed']);

            // Note: Account balance updates are informational only in double-entry
            // Actual balances are calculated from transaction queries
            // But we could cache them for performance if needed

            return $transaction->fresh();
        });
    }

    /**
     * Cancel a transaction
     */
    public function cancelTransaction(FinanceTransaction $transaction, string $reason): FinanceTransaction
    {
        if ($transaction->status === 'completed') {
            throw new \Exception('Cannot cancel completed transactions. Create a reversal transaction instead.');
        }

        $transaction->update([
            'status' => 'cancelled',
            'notes' => ($transaction->notes ? $transaction->notes . "\n\n" : '') . "Cancelled: " . $reason,
        ]);

        return $transaction->fresh();
    }

    /**
     * Create a reversal transaction for a completed transaction
     */
    public function reverseTransaction(FinanceTransaction $transaction, User $user, string $reason): FinanceTransaction
    {
        if ($transaction->status !== 'completed') {
            throw new \Exception('Only completed transactions can be reversed');
        }

        return DB::transaction(function () use ($transaction, $user, $reason) {
            // Create opposite transaction
            $reversalData = [
                'company_id' => $transaction->company_id,
                'transaction_date_bs' => $this->calendar->getCurrentBsDate(),
                'transaction_type' => $transaction->transaction_type,
                'category_id' => $transaction->category_id,
                'amount' => $transaction->amount,
                // Swap debit and credit
                'debit_account_id' => $transaction->credit_account_id,
                'credit_account_id' => $transaction->debit_account_id,
                'description' => "REVERSAL: " . $transaction->description,
                'reference_type' => FinanceTransaction::class,
                'reference_id' => $transaction->id,
                'payment_method' => $transaction->payment_method,
                'status' => 'approved',
                'approved_by_user_id' => $user->id,
                'created_by_user_id' => $user->id,
                'notes' => "Reversal of transaction #{$transaction->transaction_number}. Reason: {$reason}",
            ];

            $reversal = FinanceTransaction::create($reversalData);

            // Auto-complete the reversal
            $reversal->update(['status' => 'completed']);

            // Mark original as reversed
            $transaction->update([
                'notes' => ($transaction->notes ? $transaction->notes . "\n\n" : '') .
                    "Reversed by transaction #{$reversal->transaction_number}",
            ]);

            return $reversal->fresh(['company', 'category', 'debitAccount', 'creditAccount']);
        });
    }

    /**
     * Get transactions for a specific period
     */
    public function getTransactionsByPeriod(
        int $companyId,
        string $startDate,
        string $endDate,
        array $filters = []
    ) {
        $query = FinanceTransaction::byCompany($companyId)
            ->whereBetween('transaction_date_bs', [$startDate, $endDate])
            ->with(['category', 'debitAccount', 'creditAccount', 'createdBy', 'approvedBy']);

        // Apply filters
        if (isset($filters['type'])) {
            $query->where('transaction_type', $filters['type']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (isset($filters['from_holding'])) {
            $query->where('is_from_holding_company', $filters['from_holding']);
        }

        return $query->orderBy('transaction_date_bs', 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Calculate totals for a period
     */
    public function calculatePeriodTotals(int $companyId, string $startDate, string $endDate): array
    {
        $transactions = $this->getTransactionsByPeriod($companyId, $startDate, $endDate)
            ->where('status', 'completed')
            ->get();

        return [
            'total_income' => $transactions->where('transaction_type', 'income')->sum('amount'),
            'total_expense' => $transactions->where('transaction_type', 'expense')->sum('amount'),
            'total_transfer' => $transactions->where('transaction_type', 'transfer')->sum('amount'),
            'total_investment' => $transactions->where('transaction_type', 'investment')->sum('amount'),
            'total_withdrawal' => $transactions->where('transaction_type', 'withdrawal')->sum('amount'),
            'net_income' => $transactions->where('transaction_type', 'income')->sum('amount') -
                $transactions->where('transaction_type', 'expense')->sum('amount'),
            'transaction_count' => $transactions->count(),
        ];
    }

    /**
     * Get category-wise breakdown
     */
    public function getCategoryBreakdown(int $companyId, string $fiscalYear, string $type = 'expense'): array
    {
        $transactions = FinanceTransaction::byCompany($companyId)
            ->byFiscalYear($fiscalYear)
            ->where('transaction_type', $type)
            ->where('status', 'completed')
            ->with('category')
            ->get();

        $breakdown = [];
        foreach ($transactions as $transaction) {
            $categoryName = $transaction->category?->name ?? 'Uncategorized';

            if (!isset($breakdown[$categoryName])) {
                $breakdown[$categoryName] = [
                    'category' => $categoryName,
                    'amount' => 0,
                    'count' => 0,
                ];
            }

            $breakdown[$categoryName]['amount'] += $transaction->amount;
            $breakdown[$categoryName]['count']++;
        }

        // Sort by amount descending
        usort($breakdown, fn($a, $b) => $b['amount'] <=> $a['amount']);

        return $breakdown;
    }

    /**
     * Get monthly trends
     */
    public function getMonthlyTrends(int $companyId, string $fiscalYear): array
    {
        $trends = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthData = FinanceTransaction::byCompany($companyId)
                ->byFiscalYear($fiscalYear)
                ->byMonth($month)
                ->where('status', 'completed')
                ->selectRaw('
                    SUM(CASE WHEN transaction_type = "income" THEN amount ELSE 0 END) as income,
                    SUM(CASE WHEN transaction_type = "expense" THEN amount ELSE 0 END) as expense,
                    COUNT(*) as transaction_count
                ')
                ->first();

            $trends[] = [
                'month' => $month,
                'month_name' => $this->getMonthName($month),
                'income' => (float)($monthData->income ?? 0),
                'expense' => (float)($monthData->expense ?? 0),
                'net' => (float)(($monthData->income ?? 0) - ($monthData->expense ?? 0)),
                'transaction_count' => $monthData->transaction_count ?? 0,
            ];
        }

        return $trends;
    }

    /**
     * Upload document to storage
     */
    public function uploadDocument(UploadedFile $file, int $companyId): string
    {
        $company = FinanceCompany::find($companyId);
        $companySlug = Str::slug($company->name);

        $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            "finance/{$companySlug}/documents/" . date('Y/m'),
            $fileName,
            'public'
        );

        return $path;
    }

    /**
     * Determine if transaction should be auto-approved
     */
    protected function shouldAutoApprove(FinanceTransaction $transaction): bool
    {
        // Auto-approve if amount is less than 5000 and it's an expense
        // This is configurable - could be moved to settings
        $autoApproveThreshold = 5000;

        return $transaction->transaction_type === 'expense'
            && $transaction->amount < $autoApproveThreshold;
    }

    /**
     * Get Nepali month name
     */
    protected function getMonthName(int $month): string
    {
        $months = [
            1 => 'Baisakh',
            2 => 'Jestha',
            3 => 'Ashadh',
            4 => 'Shrawan',
            5 => 'Bhadra',
            6 => 'Ashwin',
            7 => 'Kartik',
            8 => 'Mangsir',
            9 => 'Poush',
            10 => 'Magh',
            11 => 'Falgun',
            12 => 'Chaitra'
        ];

        return $months[$month] ?? 'Unknown';
    }

    /**
     * Validate transaction balance (debit = credit)
     */
    public function validateDoubleEntry(array $data): bool
    {
        // In double-entry accounting, every transaction must have
        // equal debits and credits
        return isset($data['debit_account_id'])
            && isset($data['credit_account_id'])
            && $data['amount'] > 0;
    }

    /**
     * Get account suggestions based on transaction type
     */
    public function suggestAccounts(string $transactionType, int $companyId): array
    {
        $suggestions = [];

        switch ($transactionType) {
            case 'income':
                // Debit: Bank/Cash, Credit: Revenue Account
                $suggestions = [
                    'debit' => FinanceAccount::byCompany($companyId)
                        ->byType('asset')
                        ->whereIn('account_code', ['1000', '1100'])
                        ->get(),
                    'credit' => FinanceAccount::byCompany($companyId)
                        ->byType('revenue')
                        ->get(),
                ];
                break;

            case 'expense':
                // Debit: Expense Account, Credit: Bank/Cash
                $suggestions = [
                    'debit' => FinanceAccount::byCompany($companyId)
                        ->byType('expense')
                        ->get(),
                    'credit' => FinanceAccount::byCompany($companyId)
                        ->byType('asset')
                        ->whereIn('account_code', ['1000', '1100'])
                        ->get(),
                ];
                break;

            case 'transfer':
                // Debit: Destination Account, Credit: Source Account
                $suggestions = [
                    'debit' => FinanceAccount::byCompany($companyId)
                        ->byType('asset')
                        ->get(),
                    'credit' => FinanceAccount::byCompany($companyId)
                        ->byType('asset')
                        ->get(),
                ];
                break;
        }

        return $suggestions;
    }
}
