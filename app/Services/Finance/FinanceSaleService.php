<?php

namespace App\Services\Finance;

use App\Models\FinanceSale;
use App\Models\FinanceTransaction;
use App\Models\FinanceVendor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FinanceSaleService
{
    protected FinanceTransactionService $transactionService;

    public function __construct(FinanceTransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Create a new sale with automatic transaction creation
     */
    public function createSale(array $data, ?User $user = null): FinanceSale
    {
        return DB::transaction(function () use ($data, $user) {
            // Auto-generate sale number if not provided
            if (!isset($data['sale_number'])) {
                $data['sale_number'] = FinanceSale::generateSaleNumber($data['company_id']);
            }

            // Calculate amounts
            $data = $this->calculateSaleAmounts($data);

            // Set fiscal year from sale date
            if (!isset($data['fiscal_year_bs'])) {
                [$year, $month] = explode('-', $data['sale_date_bs']);
                $data['fiscal_year_bs'] = $year;
            }

            // Set user
            if ($user && !isset($data['created_by_user_id'])) {
                $data['created_by_user_id'] = $user->id;
            }

            // Handle document upload
            if (isset($data['document']) && $data['document'] instanceof UploadedFile) {
                $data['document_path'] = $this->transactionService->uploadDocument(
                    $data['document'],
                    $data['company_id']
                );
                unset($data['document']);
            }

            // Set initial payment status
            if (!isset($data['payment_status'])) {
                $data['payment_status'] = 'pending';
            }

            // Create sale record
            $sale = FinanceSale::create($data);

            // Create corresponding finance transaction if paid
            if ($data['payment_status'] === 'paid' && isset($data['debit_account_id']) && isset($data['credit_account_id'])) {
                $this->createSaleTransaction($sale, $data, $user);
            }

            return $sale->fresh(['company', 'customer', 'createdBy', 'transaction']);
        });
    }

    /**
     * Update a sale
     */
    public function updateSale(FinanceSale $sale, array $data): FinanceSale
    {
        return DB::transaction(function () use ($sale, $data) {
            // Recalculate amounts if base values changed
            if (isset($data['total_amount']) || isset($data['vat_amount']) || isset($data['discount_amount'])) {
                $data = $this->calculateSaleAmounts(array_merge($sale->toArray(), $data));
            }

            // Handle document upload
            if (isset($data['document']) && $data['document'] instanceof UploadedFile) {
                if ($sale->document_path) {
                    Storage::disk('public')->delete($sale->document_path);
                }

                $data['document_path'] = $this->transactionService->uploadDocument(
                    $data['document'],
                    $sale->company_id
                );
                unset($data['document']);
            }

            // Update sale
            $sale->update($data);

            // If payment status changed to paid, create transaction
            if (isset($data['payment_status']) && $data['payment_status'] === 'paid' && !$sale->transaction) {
                if (isset($data['debit_account_id']) && isset($data['credit_account_id'])) {
                    $this->createSaleTransaction($sale, $data);
                }
            }

            return $sale->fresh(['company', 'customer', 'transaction']);
        });
    }

    /**
     * Record payment for a sale
     */
    public function recordPayment(
        FinanceSale $sale,
        float $amount,
        string $paymentDate,
        string $paymentMethod,
        int $debitAccountId,
        int $creditAccountId,
        ?User $user = null
    ): FinanceSale {
        return DB::transaction(function () use ($sale, $amount, $paymentDate, $paymentMethod, $debitAccountId, $creditAccountId, $user) {
            // Update payment information
            $sale->update([
                'payment_status' => $amount >= $sale->net_amount ? 'paid' : 'partial',
                'payment_method' => $paymentMethod,
                'payment_date_bs' => $paymentDate,
            ]);

            // Create transaction
            $transactionData = [
                'company_id' => $sale->company_id,
                'transaction_date_bs' => $paymentDate,
                'transaction_type' => 'income',
                'category_id' => null, // Sales revenue
                'amount' => $amount,
                'debit_account_id' => $debitAccountId, // Bank/Cash
                'credit_account_id' => $creditAccountId, // Sales Revenue
                'payment_method' => $paymentMethod,
                'reference_type' => FinanceSale::class,
                'reference_id' => $sale->id,
                'description' => "Payment for Sale #{$sale->sale_number}" .
                    ($sale->customer ? " - {$sale->customer_name}" : ''),
                'status' => 'approved',
            ];

            $this->transactionService->createTransaction($transactionData, $user);

            return $sale->fresh(['transaction']);
        });
    }

    /**
     * Calculate sale amounts (VAT, taxable, net)
     */
    protected function calculateSaleAmounts(array $data): array
    {
        $totalAmount = $data['total_amount'] ?? 0;
        $vatAmount = $data['vat_amount'] ?? 0;
        $discountAmount = $data['discount_amount'] ?? 0;

        // If VAT amount is not provided but we have total, calculate 13% VAT
        if ($vatAmount == 0 && $totalAmount > 0) {
            // Assuming 13% VAT in Nepal
            $data['taxable_amount'] = $totalAmount / 1.13;
            $data['vat_amount'] = $totalAmount - $data['taxable_amount'];
        } else {
            $data['taxable_amount'] = $totalAmount - $vatAmount;
        }

        // Net amount after discount
        $data['net_amount'] = $totalAmount - $discountAmount;

        return $data;
    }

    /**
     * Create finance transaction for a sale
     */
    protected function createSaleTransaction(FinanceSale $sale, array $data, ?User $user = null): FinanceTransaction
    {
        $transactionData = [
            'company_id' => $sale->company_id,
            'transaction_date_bs' => $data['payment_date_bs'] ?? $sale->sale_date_bs,
            'transaction_type' => 'income',
            'amount' => $sale->net_amount,
            'debit_account_id' => $data['debit_account_id'], // Bank/Cash
            'credit_account_id' => $data['credit_account_id'], // Sales Revenue Account
            'payment_method' => $sale->payment_method,
            'reference_type' => FinanceSale::class,
            'reference_id' => $sale->id,
            'description' => "Sale #{$sale->sale_number}" .
                ($sale->customer_name ? " - {$sale->customer_name}" : '') .
                ($sale->invoice_number ? " (Invoice: {$sale->invoice_number})" : ''),
            'invoice_number' => $sale->invoice_number,
            'status' => 'approved',
        ];

        return $this->transactionService->createTransaction($transactionData, $user);
    }

    /**
     * Get sales summary for a period
     */
    public function getSalesSummary(int $companyId, string $startDate, string $endDate): array
    {
        $sales = FinanceSale::byCompany($companyId)
            ->whereBetween('sale_date_bs', [$startDate, $endDate])
            ->get();

        return [
            'total_sales' => $sales->sum('net_amount'),
            'total_vat' => $sales->sum('vat_amount'),
            'total_discount' => $sales->sum('discount_amount'),
            'sales_count' => $sales->count(),
            'paid_sales' => $sales->where('payment_status', 'paid')->sum('net_amount'),
            'pending_sales' => $sales->whereIn('payment_status', ['pending', 'partial'])->sum('net_amount'),
            'paid_count' => $sales->where('payment_status', 'paid')->count(),
            'pending_count' => $sales->whereIn('payment_status', ['pending', 'partial'])->count(),
        ];
    }

    /**
     * Get customer-wise sales
     */
    public function getCustomerSales(int $companyId, string $fiscalYear): array
    {
        $sales = FinanceSale::byCompany($companyId)
            ->byFiscalYear($fiscalYear)
            ->with('customer')
            ->get();

        $customerSales = [];
        foreach ($sales as $sale) {
            $customerId = $sale->customer_id ?? 'walk-in';
            $customerName = $sale->customer_name ?? 'Walk-in Customer';

            if (!isset($customerSales[$customerId])) {
                $customerSales[$customerId] = [
                    'customer_id' => $customerId,
                    'customer_name' => $customerName,
                    'total_sales' => 0,
                    'total_paid' => 0,
                    'total_pending' => 0,
                    'sales_count' => 0,
                ];
            }

            $customerSales[$customerId]['total_sales'] += $sale->net_amount;
            $customerSales[$customerId]['sales_count']++;

            if ($sale->payment_status === 'paid') {
                $customerSales[$customerId]['total_paid'] += $sale->net_amount;
            } else {
                $customerSales[$customerId]['total_pending'] += $sale->net_amount;
            }
        }

        // Sort by total sales descending
        usort($customerSales, fn($a, $b) => $b['total_sales'] <=> $a['total_sales']);

        return $customerSales;
    }

    /**
     * Get monthly sales trends
     */
    public function getMonthlySalesTrends(int $companyId, string $fiscalYear): array
    {
        $trends = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthSales = FinanceSale::byCompany($companyId)
                ->byFiscalYear($fiscalYear)
                ->whereRaw('CAST(SUBSTRING(sale_date_bs, 6, 2) AS UNSIGNED) = ?', [$month])
                ->get();

            $trends[] = [
                'month' => $month,
                'month_name' => $this->getMonthName($month),
                'total_sales' => $monthSales->sum('net_amount'),
                'total_vat' => $monthSales->sum('vat_amount'),
                'sales_count' => $monthSales->count(),
                'paid_sales' => $monthSales->where('payment_status', 'paid')->sum('net_amount'),
                'pending_sales' => $monthSales->whereIn('payment_status', ['pending', 'partial'])->sum('net_amount'),
            ];
        }

        return $trends;
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
}
