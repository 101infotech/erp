<?php

namespace App\Services\Finance;

use App\Models\FinanceCompany;
use App\Models\FinanceTransaction;
use App\Models\FinanceAccount;
use App\Models\FinanceSale;
use App\Models\FinancePurchase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinanceReportService
{
    /**
     * Generate Profit & Loss Statement
     */
    public function generateProfitLoss(
        int $companyId,
        string $fiscalYear,
        ?int $month = null
    ): array {
        $company = FinanceCompany::findOrFail($companyId);

        $query = FinanceTransaction::where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->where('status', 'completed');

        if ($month) {
            $query->where('fiscal_month_bs', $month);
        }

        $transactions = $query->with(['category', 'debitAccount', 'creditAccount'])->get();

        // Calculate revenue (income transactions)
        $incomeTransactions = $transactions->where('transaction_type', 'income');
        $totalRevenue = $incomeTransactions->sum('amount');

        // Group revenue by category
        $revenueByCategory = $incomeTransactions
            ->groupBy('category.name')
            ->map(function ($items) {
                return [
                    'amount' => $items->sum('amount'),
                    'count' => $items->count(),
                    'percentage' => 0, // Will calculate later
                ];
            });

        // Calculate expenses
        $expenseTransactions = $transactions->where('transaction_type', 'expense');
        $totalExpenses = $expenseTransactions->sum('amount');

        // Group expenses by category
        $expensesByCategory = $expenseTransactions
            ->groupBy('category.name')
            ->map(function ($items) {
                return [
                    'amount' => $items->sum('amount'),
                    'count' => $items->count(),
                    'percentage' => 0, // Will calculate later
                ];
            })
            ->sortByDesc('amount');

        // Calculate percentages
        foreach ($revenueByCategory as $category => $data) {
            $revenueByCategory[$category]['percentage'] = $totalRevenue > 0
                ? round(($data['amount'] / $totalRevenue) * 100, 2)
                : 0;
        }

        foreach ($expensesByCategory as $category => $data) {
            $expensesByCategory[$category]['percentage'] = $totalExpenses > 0
                ? round(($data['amount'] / $totalExpenses) * 100, 2)
                : 0;
        }

        $netProfit = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0;

        return [
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'type' => $company->type,
            ],
            'period' => [
                'fiscal_year' => $fiscalYear,
                'month' => $month,
                'month_name' => $month ? $this->getMonthName($month) : null,
            ],
            'revenue' => [
                'total' => number_format($totalRevenue, 2, '.', ''),
                'by_category' => $revenueByCategory,
                'transaction_count' => $incomeTransactions->count(),
            ],
            'expenses' => [
                'total' => number_format($totalExpenses, 2, '.', ''),
                'by_category' => $expensesByCategory,
                'transaction_count' => $expenseTransactions->count(),
            ],
            'net_profit' => number_format($netProfit, 2, '.', ''),
            'profit_margin' => $profitMargin,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Generate Balance Sheet
     */
    public function generateBalanceSheet(
        int $companyId,
        string $asOfDate
    ): array {
        $company = FinanceCompany::findOrFail($companyId);

        // Get all accounts with their balances
        $accounts = FinanceAccount::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        $assets = [];
        $liabilities = [];
        $equity = [];

        foreach ($accounts as $account) {
            $balance = $this->calculateAccountBalance($account->id, $asOfDate);

            if ($balance == 0) continue;

            $accountData = [
                'id' => $account->id,
                'name' => $account->name,
                'code' => $account->account_code,
                'balance' => number_format(abs($balance), 2, '.', ''),
            ];

            switch ($account->account_type) {
                case 'asset':
                    $assets[] = $accountData;
                    break;
                case 'liability':
                    $liabilities[] = $accountData;
                    break;
                case 'equity':
                    $equity[] = $accountData;
                    break;
            }
        }

        $totalAssets = collect($assets)->sum(fn($a) => (float)$a['balance']);
        $totalLiabilities = collect($liabilities)->sum(fn($a) => (float)$a['balance']);
        $totalEquity = collect($equity)->sum(fn($a) => (float)$a['balance']);

        // Calculate retained earnings (net profit to date)
        $retainedEarnings = $this->calculateRetainedEarnings($companyId, $asOfDate);

        if ($retainedEarnings != 0) {
            $equity[] = [
                'id' => null,
                'name' => 'Retained Earnings',
                'code' => 'RE-AUTO',
                'balance' => number_format(abs($retainedEarnings), 2, '.', ''),
            ];
            $totalEquity += $retainedEarnings;
        }

        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;
        $isBalanced = abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01;

        return [
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'type' => $company->type,
            ],
            'as_of_date' => $asOfDate,
            'assets' => [
                'accounts' => $assets,
                'total' => number_format($totalAssets, 2, '.', ''),
            ],
            'liabilities' => [
                'accounts' => $liabilities,
                'total' => number_format($totalLiabilities, 2, '.', ''),
            ],
            'equity' => [
                'accounts' => $equity,
                'total' => number_format($totalEquity, 2, '.', ''),
            ],
            'total_liabilities_and_equity' => number_format($totalLiabilitiesAndEquity, 2, '.', ''),
            'is_balanced' => $isBalanced,
            'difference' => number_format($totalAssets - $totalLiabilitiesAndEquity, 2, '.', ''),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Generate Cash Flow Statement
     */
    public function generateCashFlow(
        int $companyId,
        string $fiscalYear,
        ?int $month = null
    ): array {
        $company = FinanceCompany::findOrFail($companyId);

        $query = FinanceTransaction::where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->where('status', 'completed');

        if ($month) {
            $query->where('fiscal_month_bs', $month);
        }

        $transactions = $query->with(array('category', 'debitAccount', 'creditAccount'))->get();

        // Operating Activities (income and expense transactions)
        $operatingInflows = $transactions->where('transaction_type', 'income')->sum('amount');
        $operatingOutflows = $transactions->where('transaction_type', 'expense')->sum('amount');
        $netOperatingCashFlow = $operatingInflows - $operatingOutflows;

        // Investing Activities (investment transactions)
        $investingOutflows = $transactions->where('transaction_type', 'investment')->sum('amount');
        $netInvestingCashFlow = -$investingOutflows;

        // Financing Activities (withdrawals)
        $financingOutflows = $transactions->where('transaction_type', 'withdrawal')->sum('amount');
        $netFinancingCashFlow = -$financingOutflows;

        // Transfers (internal movements)
        $transfers = $transactions->where('transaction_type', 'transfer')->sum('amount');

        $netCashFlow = $netOperatingCashFlow + $netInvestingCashFlow + $netFinancingCashFlow;

        // Get opening and closing cash balances
        $cashAccounts = FinanceAccount::where('company_id', $companyId)
            ->whereIn('account_type', ['asset'])
            ->where('name', 'LIKE', '%cash%')
            ->orWhere('name', 'LIKE', '%bank%')
            ->pluck('id');

        $startDate = $fiscalYear . '-04-01'; // Shrawan 1
        $endDate = $month
            ? $fiscalYear . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-32'
            : $fiscalYear . '-03-32'; // Ashadh 32

        $openingCash = $this->calculateCashBalance($companyId, $startDate, $cashAccounts);
        $closingCash = $this->calculateCashBalance($companyId, $endDate, $cashAccounts);

        return [
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'type' => $company->type,
            ],
            'period' => [
                'fiscal_year' => $fiscalYear,
                'month' => $month,
                'month_name' => $month ? $this->getMonthName($month) : null,
            ],
            'operating_activities' => [
                'inflows' => number_format($operatingInflows, 2, '.', ''),
                'outflows' => number_format($operatingOutflows, 2, '.', ''),
                'net' => number_format($netOperatingCashFlow, 2, '.', ''),
            ],
            'investing_activities' => [
                'outflows' => number_format($investingOutflows, 2, '.', ''),
                'net' => number_format($netInvestingCashFlow, 2, '.', ''),
            ],
            'financing_activities' => [
                'outflows' => number_format($financingOutflows, 2, '.', ''),
                'net' => number_format($netFinancingCashFlow, 2, '.', ''),
            ],
            'transfers' => number_format($transfers, 2, '.', ''),
            'net_cash_flow' => number_format($netCashFlow, 2, '.', ''),
            'opening_cash' => number_format($openingCash, 2, '.', ''),
            'closing_cash' => number_format($closingCash, 2, '.', ''),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Generate Trial Balance
     */
    public function generateTrialBalance(
        int $companyId,
        string $asOfDate
    ): array {
        $company = FinanceCompany::findOrFail($companyId);

        $accounts = FinanceAccount::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $accountBalances = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $debits = $this->calculateAccountDebits($account->id, $asOfDate);
            $credits = $this->calculateAccountCredits($account->id, $asOfDate);
            $balance = $debits - $credits;

            if ($debits == 0 && $credits == 0) continue;

            $accountBalances[] = [
                'account_id' => $account->id,
                'account_code' => $account->account_code,
                'account_name' => $account->name,
                'account_type' => $account->account_type,
                'debits' => number_format($debits, 2, '.', ''),
                'credits' => number_format($credits, 2, '.', ''),
                'balance' => number_format(abs($balance), 2, '.', ''),
                'balance_type' => $balance >= 0 ? 'debit' : 'credit',
            ];

            $totalDebit += $debits;
            $totalCredit += $credits;
        }

        $isBalanced = abs($totalDebit - $totalCredit) < 0.01;

        return [
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'type' => $company->type,
            ],
            'as_of_date' => $asOfDate,
            'accounts' => $accountBalances,
            'totals' => [
                'debits' => number_format($totalDebit, 2, '.', ''),
                'credits' => number_format($totalCredit, 2, '.', ''),
                'difference' => number_format($totalDebit - $totalCredit, 2, '.', ''),
            ],
            'is_balanced' => $isBalanced,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Generate Expense Summary Report
     */
    public function generateExpenseSummary(
        int $companyId,
        string $fiscalYear
    ): array {
        $company = FinanceCompany::findOrFail($companyId);

        $expenses = FinanceTransaction::where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->where('transaction_type', 'expense')
            ->where('status', 'completed')
            ->with('category')
            ->get();

        $totalExpenses = $expenses->sum('amount');

        // Monthly breakdown
        $monthlyBreakdown = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthExpenses = $expenses->where('fiscal_month_bs', $month);
            $monthlyBreakdown[] = [
                'month' => $month,
                'month_name' => $this->getMonthName($month),
                'amount' => number_format($monthExpenses->sum('amount'), 2, '.', ''),
                'count' => $monthExpenses->count(),
            ];
        }

        // Category breakdown
        $categoryBreakdown = $expenses
            ->groupBy('category.name')
            ->map(function ($items, $category) use ($totalExpenses) {
                $amount = $items->sum('amount');
                return [
                    'category' => $category ?? 'Uncategorized',
                    'amount' => number_format($amount, 2, '.', ''),
                    'count' => $items->count(),
                    'percentage' => $totalExpenses > 0 ? round(($amount / $totalExpenses) * 100, 2) : 0,
                ];
            })
            ->sortByDesc(fn($item) => (float)$item['amount'])
            ->values();

        // Payment method breakdown
        $paymentMethodBreakdown = $expenses
            ->groupBy('payment_method')
            ->map(function ($items, $method) {
                return [
                    'payment_method' => $method,
                    'amount' => number_format($items->sum('amount'), 2, '.', ''),
                    'count' => $items->count(),
                ];
            })
            ->sortByDesc(fn($item) => (float)$item['amount'])
            ->values();

        return [
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'type' => $company->type,
            ],
            'fiscal_year' => $fiscalYear,
            'total_expenses' => number_format($totalExpenses, 2, '.', ''),
            'transaction_count' => $expenses->count(),
            'average_expense' => number_format($expenses->count() > 0 ? $totalExpenses / $expenses->count() : 0, 2, '.', ''),
            'monthly_breakdown' => $monthlyBreakdown,
            'category_breakdown' => $categoryBreakdown,
            'payment_method_breakdown' => $paymentMethodBreakdown,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Generate Consolidated Report (all companies)
     */
    public function generateConsolidatedReport(string $fiscalYear): array
    {
        $companies = FinanceCompany::where('is_active', true)->get();

        $consolidatedData = [];
        $totalRevenue = 0;
        $totalExpense = 0;
        $totalProfit = 0;

        foreach ($companies as $company) {
            $transactions = FinanceTransaction::where('company_id', $company->id)
                ->where('fiscal_year_bs', $fiscalYear)
                ->where('status', 'completed')
                ->get();

            $revenue = $transactions->where('transaction_type', 'income')->sum('amount');
            $expense = $transactions->where('transaction_type', 'expense')->sum('amount');
            $netProfit = $revenue - $expense;

            $consolidatedData[] = [
                'company_id' => $company->id,
                'company_name' => $company->name,
                'company_type' => $company->type,
                'revenue' => number_format($revenue, 2, '.', ''),
                'expense' => number_format($expense, 2, '.', ''),
                'net_profit' => number_format($netProfit, 2, '.', ''),
                'profit_margin' => $revenue > 0 ? round(($netProfit / $revenue) * 100, 2) : 0,
            ];

            $totalRevenue += $revenue;
            $totalExpense += $expense;
            $totalProfit += $netProfit;
        }

        // Sort by revenue descending
        usort($consolidatedData, fn($a, $b) => (float)$b['revenue'] <=> (float)$a['revenue']);

        return [
            'fiscal_year' => $fiscalYear,
            'companies' => $consolidatedData,
            'group_totals' => [
                'revenue' => number_format($totalRevenue, 2, '.', ''),
                'expense' => number_format($totalExpense, 2, '.', ''),
                'net_profit' => number_format($totalProfit, 2, '.', ''),
                'profit_margin' => $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 2) : 0,
            ],
            'company_count' => $companies->count(),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Helper: Calculate account balance as of date
     */
    protected function calculateAccountBalance(int $accountId, string $asOfDate): float
    {
        $debits = FinanceTransaction::where(function ($q) use ($accountId) {
            $q->where('debit_account_id', $accountId);
        })
            ->where('transaction_date_bs', '<=', $asOfDate)
            ->where('status', 'completed')
            ->sum('amount');

        $credits = FinanceTransaction::where(function ($q) use ($accountId) {
            $q->where('credit_account_id', $accountId);
        })
            ->where('transaction_date_bs', '<=', $asOfDate)
            ->where('status', 'completed')
            ->sum('amount');

        return $debits - $credits;
    }

    /**
     * Helper: Calculate account debits as of date
     */
    protected function calculateAccountDebits(int $accountId, string $asOfDate): float
    {
        return FinanceTransaction::where('debit_account_id', $accountId)
            ->where('transaction_date_bs', '<=', $asOfDate)
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * Helper: Calculate account credits as of date
     */
    protected function calculateAccountCredits(int $accountId, string $asOfDate): float
    {
        return FinanceTransaction::where('credit_account_id', $accountId)
            ->where('transaction_date_bs', '<=', $asOfDate)
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * Helper: Calculate retained earnings
     */
    protected function calculateRetainedEarnings(int $companyId, string $asOfDate): float
    {
        $income = FinanceTransaction::where('company_id', $companyId)
            ->where('transaction_type', 'income')
            ->where('transaction_date_bs', '<=', $asOfDate)
            ->where('status', 'completed')
            ->sum('amount');

        $expense = FinanceTransaction::where('company_id', $companyId)
            ->where('transaction_type', 'expense')
            ->where('transaction_date_bs', '<=', $asOfDate)
            ->where('status', 'completed')
            ->sum('amount');

        return $income - $expense;
    }

    /**
     * Helper: Calculate cash balance
     */
    protected function calculateCashBalance(int $companyId, string $asOfDate, $cashAccountIds): float
    {
        $balance = 0;

        foreach ($cashAccountIds as $accountId) {
            $balance += $this->calculateAccountBalance($accountId, $asOfDate);
        }

        return $balance;
    }

    /**
     * Helper: Get Nepali month name
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
}
