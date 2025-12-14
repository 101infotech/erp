<?php

namespace App\Services\Finance;

use App\Models\FinanceCompany;
use App\Models\FinanceTransaction;
use App\Models\FinanceSale;
use App\Models\FinancePurchase;
use App\Services\NepalCalendarService;
use Illuminate\Support\Facades\DB;

class FinanceDashboardService
{
    protected NepalCalendarService $calendar;

    public function __construct(NepalCalendarService $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * Get comprehensive dashboard data for a company
     */
    public function getDashboardData(int $companyId, string $fiscalYear): array
    {
        return [
            'kpis' => $this->getKPIs($companyId, $fiscalYear),
            'revenue_trends' => $this->getRevenueTrends($companyId, $fiscalYear),
            'expense_breakdown' => $this->getExpenseBreakdown($companyId, $fiscalYear),
            'cash_flow_projection' => $this->getCashFlowProjection($companyId, $fiscalYear),
            'top_customers' => $this->getTopCustomers($companyId, $fiscalYear),
            'top_vendors' => $this->getTopVendors($companyId, $fiscalYear),
            'recent_transactions' => $this->getRecentTransactions($companyId, 10),
            'pending_payments' => $this->getPendingPayments($companyId),
        ];
    }

    /**
     * Get Key Performance Indicators
     */
    public function getKPIs(int $companyId, string $fiscalYear): array
    {
        $transactions = FinanceTransaction::where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->where('status', 'completed')
            ->get();

        $totalRevenue = $transactions->where('transaction_type', 'income')->sum('amount');
        $totalExpense = $transactions->where('transaction_type', 'expense')->sum('amount');
        $netProfit = $totalRevenue - $totalExpense;

        // Get previous year for comparison
        $previousYear = (string)((int)$fiscalYear - 1);
        $previousTransactions = FinanceTransaction::where('company_id', $companyId)
            ->where('fiscal_year_bs', $previousYear)
            ->where('status', 'completed')
            ->get();

        $previousRevenue = $previousTransactions->where('transaction_type', 'income')->sum('amount');
        $previousExpense = $previousTransactions->where('transaction_type', 'expense')->sum('amount');

        $revenueGrowth = $previousRevenue > 0
            ? round((($totalRevenue - $previousRevenue) / $previousRevenue) * 100, 2)
            : 0;

        $expenseGrowth = $previousExpense > 0
            ? round((($totalExpense - $previousExpense) / $previousExpense) * 100, 2)
            : 0;

        // Sales metrics
        $sales = FinanceSale::where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->get();

        $totalSales = $sales->sum('net_amount');
        $paidSales = $sales->where('payment_status', 'paid')->sum('net_amount');
        $pendingSales = $sales->whereIn('payment_status', array('pending', 'partial'))->sum('net_amount');

        // Purchase metrics
        $purchases = FinancePurchase::where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->get();

        $totalPurchases = $purchases->sum('net_amount');
        $totalVAT = $sales->sum('vat_amount') - $purchases->sum('vat_amount');
        $totalTDS = $purchases->sum('tds_amount');

        return [
            'revenue' => [
                'total' => number_format($totalRevenue, 2, '.', ''),
                'growth' => $revenueGrowth,
                'previous_year' => number_format($previousRevenue, 2, '.', ''),
            ],
            'expense' => [
                'total' => number_format($totalExpense, 2, '.', ''),
                'growth' => $expenseGrowth,
                'previous_year' => number_format($previousExpense, 2, '.', ''),
            ],
            'profit' => [
                'net' => number_format($netProfit, 2, '.', ''),
                'margin' => $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0,
            ],
            'sales' => [
                'total' => number_format($totalSales, 2, '.', ''),
                'paid' => number_format($paidSales, 2, '.', ''),
                'pending' => number_format($pendingSales, 2, '.', ''),
                'count' => $sales->count(),
            ],
            'purchases' => [
                'total' => number_format($totalPurchases, 2, '.', ''),
                'count' => $purchases->count(),
            ],
            'tax' => [
                'vat_payable' => number_format($totalVAT, 2, '.', ''),
                'tds_deducted' => number_format($totalTDS, 2, '.', ''),
            ],
        ];
    }

    /**
     * Get revenue trends (monthly)
     */
    public function getRevenueTrends(int $companyId, string $fiscalYear): array
    {
        $trends = [];

        for ($month = 1; $month <= 12; $month++) {
            $transactions = FinanceTransaction::where('company_id', $companyId)
                ->where('fiscal_year_bs', $fiscalYear)
                ->where('fiscal_month_bs', $month)
                ->where('status', 'completed')
                ->get();

            $revenue = $transactions->where('transaction_type', 'income')->sum('amount');
            $expense = $transactions->where('transaction_type', 'expense')->sum('amount');

            $trends[] = [
                'month' => $month,
                'month_name' => $this->getMonthName($month),
                'revenue' => number_format($revenue, 2, '.', ''),
                'expense' => number_format($expense, 2, '.', ''),
                'profit' => number_format($revenue - $expense, 2, '.', ''),
            ];
        }

        return $trends;
    }

    /**
     * Get expense breakdown by category
     */
    public function getExpenseBreakdown(int $companyId, string $fiscalYear): array
    {
        $expenses = FinanceTransaction::where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->where('transaction_type', 'expense')
            ->where('status', 'completed')
            ->with('category')
            ->get();

        $totalExpenses = $expenses->sum('amount');

        $breakdown = $expenses
            ->groupBy('category.name')
            ->map(function ($items, $category) use ($totalExpenses) {
                $amount = $items->sum('amount');
                return [
                    'category' => $category ?? 'Uncategorized',
                    'amount' => number_format($amount, 2, '.', ''),
                    'percentage' => $totalExpenses > 0 ? round(($amount / $totalExpenses) * 100, 2) : 0,
                    'count' => $items->count(),
                ];
            })
            ->sortByDesc(function ($item) {
                return (float)$item['amount'];
            })
            ->values()
            ->take(10);

        return $breakdown->toArray();
    }

    /**
     * Get cash flow projection (next 6 months)
     */
    public function getCashFlowProjection(int $companyId, string $fiscalYear): array
    {
        // Get historical average for last 6 months
        $currentDate = $this->calendar->getCurrentBsDate();
        list($currentYear, $currentMonth) = explode('-', $currentDate);

        $projections = [];

        for ($i = 1; $i <= 6; $i++) {
            $month = ((int)$currentMonth + $i - 1) % 12 + 1;
            $year = (int)$currentMonth + $i > 12 ? (string)((int)$currentYear + 1) : $currentYear;

            // Get historical data for same month
            $historicalTransactions = FinanceTransaction::where('company_id', $companyId)
                ->where('fiscal_year_bs', $fiscalYear)
                ->where('fiscal_month_bs', $month)
                ->where('status', 'completed')
                ->get();

            $avgRevenue = $historicalTransactions->where('transaction_type', 'income')->sum('amount');
            $avgExpense = $historicalTransactions->where('transaction_type', 'expense')->sum('amount');

            $projections[] = [
                'month' => $month,
                'month_name' => $this->getMonthName($month),
                'year' => $year,
                'projected_revenue' => number_format($avgRevenue, 2, '.', ''),
                'projected_expense' => number_format($avgExpense, 2, '.', ''),
                'projected_cash_flow' => number_format($avgRevenue - $avgExpense, 2, '.', ''),
            ];
        }

        return $projections;
    }

    /**
     * Get top customers by revenue
     */
    public function getTopCustomers(int $companyId, string $fiscalYear, int $limit = 5): array
    {
        $sales = FinanceSale::where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->with('customer')
            ->get();

        $customerSales = $sales->groupBy('customer_name')
            ->map(function ($items, $customer) {
                return [
                    'customer_name' => $customer,
                    'total_sales' => number_format($items->sum('net_amount'), 2, '.', ''),
                    'order_count' => $items->count(),
                ];
            })
            ->sortByDesc(function ($item) {
                return (float)$item['total_sales'];
            })
            ->take($limit)
            ->values();

        return $customerSales->toArray();
    }

    /**
     * Get top vendors by purchase volume
     */
    public function getTopVendors(int $companyId, string $fiscalYear, int $limit = 5): array
    {
        $purchases = FinancePurchase::where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->with('vendor')
            ->get();

        $vendorPurchases = $purchases->groupBy('vendor_name')
            ->map(function ($items, $vendor) {
                return [
                    'vendor_name' => $vendor,
                    'total_purchases' => number_format($items->sum('net_amount'), 2, '.', ''),
                    'order_count' => $items->count(),
                ];
            })
            ->sortByDesc(function ($item) {
                return (float)$item['total_purchases'];
            })
            ->take($limit)
            ->values();

        return $vendorPurchases->toArray();
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions(int $companyId, int $limit = 10): array
    {
        $transactions = FinanceTransaction::where('company_id', $companyId)
            ->with(array('category', 'debitAccount', 'creditAccount', 'createdBy'))
            ->orderBy('transaction_date_bs', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'date' => $transaction->transaction_date_bs,
                'type' => $transaction->transaction_type,
                'category' => $transaction->category->name ?? 'N/A',
                'amount' => number_format($transaction->amount, 2, '.', ''),
                'description' => $transaction->description,
                'status' => $transaction->status,
            ];
        })->toArray();
    }

    /**
     * Get pending payments (sales + purchases)
     */
    public function getPendingPayments(int $companyId): array
    {
        $pendingSales = FinanceSale::where('company_id', $companyId)
            ->whereIn('payment_status', array('pending', 'partial'))
            ->get();

        $pendingPurchases = FinancePurchase::where('company_id', $companyId)
            ->whereIn('payment_status', array('pending', 'partial'))
            ->get();

        return [
            'sales' => [
                'total' => number_format($pendingSales->sum('net_amount'), 2, '.', ''),
                'count' => $pendingSales->count(),
                'items' => $pendingSales->take(5)->map(function ($sale) {
                    return [
                        'id' => $sale->id,
                        'number' => $sale->sale_number,
                        'customer' => $sale->customer_name,
                        'amount' => number_format($sale->net_amount, 2, '.', ''),
                        'date' => $sale->sale_date_bs,
                    ];
                })->toArray(),
            ],
            'purchases' => [
                'total' => number_format($pendingPurchases->sum('net_amount'), 2, '.', ''),
                'count' => $pendingPurchases->count(),
                'items' => $pendingPurchases->take(5)->map(function ($purchase) {
                    return [
                        'id' => $purchase->id,
                        'number' => $purchase->purchase_number,
                        'vendor' => $purchase->vendor_name,
                        'amount' => number_format($purchase->net_amount, 2, '.', ''),
                        'date' => $purchase->purchase_date_bs,
                    ];
                })->toArray(),
            ],
        ];
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
