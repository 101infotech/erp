<?php

namespace App\Services\Finance;

use App\Models\FinancePurchase;
use App\Models\FinanceTransaction;
use App\Models\FinanceVendor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FinancePurchaseService
{
    protected FinanceTransactionService $transactionService;

    public function __construct(FinanceTransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Create a new purchase with automatic transaction creation
     */
    public function createPurchase(array $data, ?User $user = null): FinancePurchase
    {
        return DB::transaction(function () use ($data, $user) {
            // Auto-generate purchase number if not provided
            if (!isset($data['purchase_number'])) {
                $data['purchase_number'] = FinancePurchase::generatePurchaseNumber($data['company_id']);
            }

            // Calculate amounts (VAT, TDS, net)
            $data = $this->calculatePurchaseAmounts($data);

            // Set fiscal year from purchase date
            if (!isset($data['fiscal_year_bs'])) {
                [$year, $month] = explode('-', $data['purchase_date_bs']);
                $data['fiscal_year_bs'] = $year;
            }

            // Set user
            if ($user && !isset($data['created_by_user_id'])) {
                $data['created_by_user_id'] = $user->id;
            }

            // Handle document upload (bill/receipt)
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

            // Create purchase record
            $purchase = FinancePurchase::create($data);

            // Create corresponding finance transaction if paid
            if ($data['payment_status'] === 'paid' && isset($data['debit_account_id']) && isset($data['credit_account_id'])) {
                $this->createPurchaseTransaction($purchase, $data, $user);
            }

            return $purchase->fresh(['company', 'vendor', 'createdBy', 'transaction']);
        });
    }

    /**
     * Update a purchase
     */
    public function updatePurchase(FinancePurchase $purchase, array $data): FinancePurchase
    {
        return DB::transaction(function () use ($purchase, $data) {
            // Recalculate amounts if base values changed
            if (
                isset($data['total_amount']) || isset($data['vat_amount']) ||
                isset($data['tds_amount']) || isset($data['discount_amount'])
            ) {
                $data = $this->calculatePurchaseAmounts(array_merge($purchase->toArray(), $data));
            }

            // Handle document upload
            if (isset($data['document']) && $data['document'] instanceof UploadedFile) {
                if ($purchase->document_path) {
                    Storage::disk('public')->delete($purchase->document_path);
                }

                $data['document_path'] = $this->transactionService->uploadDocument(
                    $data['document'],
                    $purchase->company_id
                );
                unset($data['document']);
            }

            // Update purchase
            $purchase->update($data);

            // If payment status changed to paid, create transaction
            if (isset($data['payment_status']) && $data['payment_status'] === 'paid' && !$purchase->transaction) {
                if (isset($data['debit_account_id']) && isset($data['credit_account_id'])) {
                    $this->createPurchaseTransaction($purchase, $data);
                }
            }

            return $purchase->fresh(['company', 'vendor', 'transaction']);
        });
    }

    /**
     * Record payment for a purchase
     */
    public function recordPayment(
        FinancePurchase $purchase,
        float $amount,
        string $paymentDate,
        string $paymentMethod,
        int $debitAccountId,
        int $creditAccountId,
        ?User $user = null
    ): FinancePurchase {
        return DB::transaction(function () use ($purchase, $amount, $paymentDate, $paymentMethod, $debitAccountId, $creditAccountId, $user) {
            // Update payment information
            $purchase->update([
                'payment_status' => $amount >= $purchase->net_amount ? 'paid' : 'partial',
                'payment_method' => $paymentMethod,
                'payment_date_bs' => $paymentDate,
            ]);

            // Create transaction
            $transactionData = [
                'company_id' => $purchase->company_id,
                'transaction_date_bs' => $paymentDate,
                'transaction_type' => 'expense',
                'amount' => $amount,
                'debit_account_id' => $debitAccountId, // Expense Account
                'credit_account_id' => $creditAccountId, // Bank/Cash
                'payment_method' => $paymentMethod,
                'reference_type' => FinancePurchase::class,
                'reference_id' => $purchase->id,
                'description' => "Payment for Purchase #{$purchase->purchase_number}" .
                    ($purchase->vendor ? " - {$purchase->vendor_name}" : ''),
                'status' => 'approved',
            ];

            $this->transactionService->createTransaction($transactionData, $user);

            return $purchase->fresh(['transaction']);
        });
    }

    /**
     * Calculate purchase amounts (VAT, TDS, taxable, net)
     */
    protected function calculatePurchaseAmounts(array $data): array
    {
        $totalAmount = $data['total_amount'] ?? 0;
        $vatAmount = $data['vat_amount'] ?? 0;
        $tdsAmount = $data['tds_amount'] ?? 0;
        $tdsPercentage = $data['tds_percentage'] ?? 0;
        $discountAmount = $data['discount_amount'] ?? 0;

        // Calculate VAT (13% in Nepal) if not provided
        if ($vatAmount == 0 && $totalAmount > 0) {
            $data['taxable_amount'] = $totalAmount / 1.13;
            $data['vat_amount'] = $totalAmount - $data['taxable_amount'];
        } else {
            $data['taxable_amount'] = $totalAmount - $vatAmount;
        }

        // Calculate TDS if percentage provided
        if ($tdsPercentage > 0 && $tdsAmount == 0) {
            $data['tds_amount'] = ($data['taxable_amount'] * $tdsPercentage) / 100;
        }

        // Net amount = Total - Discount - TDS (TDS is deducted from payment)
        $data['net_amount'] = $totalAmount - $discountAmount - ($data['tds_amount'] ?? 0);

        return $data;
    }

    /**
     * Create finance transaction for a purchase
     */
    protected function createPurchaseTransaction(FinancePurchase $purchase, array $data, ?User $user = null): FinanceTransaction
    {
        $transactionData = [
            'company_id' => $purchase->company_id,
            'transaction_date_bs' => $data['payment_date_bs'] ?? $purchase->purchase_date_bs,
            'transaction_type' => 'expense',
            'amount' => $purchase->net_amount,
            'debit_account_id' => $data['debit_account_id'], // Expense Account
            'credit_account_id' => $data['credit_account_id'], // Bank/Cash
            'payment_method' => $purchase->payment_method,
            'reference_type' => FinancePurchase::class,
            'reference_id' => $purchase->id,
            'description' => "Purchase #{$purchase->purchase_number}" .
                ($purchase->vendor_name ? " - {$purchase->vendor_name}" : '') .
                ($purchase->bill_number ? " (Bill: {$purchase->bill_number})" : ''),
            'status' => 'approved',
        ];

        return $this->transactionService->createTransaction($transactionData, $user);
    }

    /**
     * Get purchases summary for a period
     */
    public function getPurchasesSummary(int $companyId, string $startDate, string $endDate): array
    {
        $purchases = FinancePurchase::byCompany($companyId)
            ->whereBetween('purchase_date_bs', [$startDate, $endDate])
            ->get();

        return [
            'total_purchases' => $purchases->sum('net_amount'),
            'total_vat' => $purchases->sum('vat_amount'),
            'total_tds' => $purchases->sum('tds_amount'),
            'total_discount' => $purchases->sum('discount_amount'),
            'purchases_count' => $purchases->count(),
            'paid_purchases' => $purchases->where('payment_status', 'paid')->sum('net_amount'),
            'pending_purchases' => $purchases->whereIn('payment_status', ['pending', 'partial'])->sum('net_amount'),
            'paid_count' => $purchases->where('payment_status', 'paid')->count(),
            'pending_count' => $purchases->whereIn('payment_status', ['pending', 'partial'])->count(),
        ];
    }

    /**
     * Get vendor-wise purchases
     */
    public function getVendorPurchases(int $companyId, string $fiscalYear): array
    {
        $purchases = FinancePurchase::byCompany($companyId)
            ->byFiscalYear($fiscalYear)
            ->with('vendor')
            ->get();

        $vendorPurchases = [];
        foreach ($purchases as $purchase) {
            $vendorId = $purchase->vendor_id ?? 'unknown';
            $vendorName = $purchase->vendor_name ?? 'Unknown Vendor';

            if (!isset($vendorPurchases[$vendorId])) {
                $vendorPurchases[$vendorId] = [
                    'vendor_id' => $vendorId,
                    'vendor_name' => $vendorName,
                    'total_purchases' => 0,
                    'total_paid' => 0,
                    'total_pending' => 0,
                    'total_tds' => 0,
                    'purchases_count' => 0,
                ];
            }

            $vendorPurchases[$vendorId]['total_purchases'] += $purchase->net_amount;
            $vendorPurchases[$vendorId]['total_tds'] += $purchase->tds_amount;
            $vendorPurchases[$vendorId]['purchases_count']++;

            if ($purchase->payment_status === 'paid') {
                $vendorPurchases[$vendorId]['total_paid'] += $purchase->net_amount;
            } else {
                $vendorPurchases[$vendorId]['total_pending'] += $purchase->net_amount;
            }
        }

        // Sort by total purchases descending
        usort($vendorPurchases, fn($a, $b) => $b['total_purchases'] <=> $a['total_purchases']);

        return $vendorPurchases;
    }

    /**
     * Get monthly purchase trends
     */
    public function getMonthlyPurchaseTrends(int $companyId, string $fiscalYear): array
    {
        $trends = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthPurchases = FinancePurchase::byCompany($companyId)
                ->byFiscalYear($fiscalYear)
                ->whereRaw('CAST(SUBSTRING(purchase_date_bs, 6, 2) AS UNSIGNED) = ?', [$month])
                ->get();

            $trends[] = [
                'month' => $month,
                'month_name' => $this->getMonthName($month),
                'total_purchases' => $monthPurchases->sum('net_amount'),
                'total_vat' => $monthPurchases->sum('vat_amount'),
                'total_tds' => $monthPurchases->sum('tds_amount'),
                'purchases_count' => $monthPurchases->count(),
                'paid_purchases' => $monthPurchases->where('payment_status', 'paid')->sum('net_amount'),
                'pending_purchases' => $monthPurchases->whereIn('payment_status', ['pending', 'partial'])->sum('net_amount'),
            ];
        }

        return $trends;
    }

    /**
     * Get TDS summary for tax compliance
     */
    public function getTdsSummary(int $companyId, string $fiscalYear): array
    {
        $purchases = FinancePurchase::byCompany($companyId)
            ->byFiscalYear($fiscalYear)
            ->where('tds_amount', '>', 0)
            ->get();

        $tdsByVendor = [];
        foreach ($purchases as $purchase) {
            $vendorPan = $purchase->vendor_pan ?? 'NO_PAN';
            $vendorName = $purchase->vendor_name ?? 'Unknown';

            if (!isset($tdsByVendor[$vendorPan])) {
                $tdsByVendor[$vendorPan] = [
                    'vendor_pan' => $vendorPan,
                    'vendor_name' => $vendorName,
                    'total_taxable_amount' => 0,
                    'total_tds_amount' => 0,
                    'transaction_count' => 0,
                ];
            }

            $tdsByVendor[$vendorPan]['total_taxable_amount'] += $purchase->taxable_amount;
            $tdsByVendor[$vendorPan]['total_tds_amount'] += $purchase->tds_amount;
            $tdsByVendor[$vendorPan]['transaction_count']++;
        }

        return [
            'total_tds' => $purchases->sum('tds_amount'),
            'total_taxable' => $purchases->sum('taxable_amount'),
            'vendor_breakdown' => array_values($tdsByVendor),
        ];
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
