<?php

namespace App\Services\Finance;

use App\Models\FinanceAsset;
use App\Models\FinanceAssetDepreciation;
use App\Models\FinanceChartOfAccount;
use App\Models\FinanceJournalEntry;
use App\Models\FinanceJournalEntryLine;
use Illuminate\Support\Facades\DB;

class AssetDepreciationService
{
    /**
     * Calculate depreciation for a specific asset and month
     */
    public function calculateMonthlyDepreciation($assetId, $fiscalYear, $fiscalMonth)
    {
        $asset = FinanceAsset::findOrFail($assetId);

        if ($asset->depreciation_method === 'none') {
            return 0;
        }

        // Check if already calculated
        $existing = FinanceAssetDepreciation::where('asset_id', $assetId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->where('fiscal_month_bs', $fiscalMonth)
            ->first();

        if ($existing) {
            return $existing->depreciation_amount;
        }

        // Calculate based on method
        $depreciationAmount = 0;

        switch ($asset->depreciation_method) {
            case 'straight_line':
                $depreciationAmount = $this->calculateStraightLine($asset);
                break;
            case 'declining_balance':
                $depreciationAmount = $this->calculateDecliningBalance($asset);
                break;
            case 'double_declining':
                $depreciationAmount = $this->calculateDoubleDeclining($asset);
                break;
            case 'sum_of_years':
                $depreciationAmount = $this->calculateSumOfYears($asset);
                break;
        }

        return $depreciationAmount;
    }

    /**
     * Post depreciation entry and create journal entry
     */
    public function postDepreciationEntry(FinanceAsset $asset, $fiscalYear, $fiscalMonth, $userId)
    {
        return DB::transaction(function () use ($asset, $fiscalYear, $fiscalMonth, $userId) {
            // Calculate depreciation
            $depreciationAmount = $this->calculateMonthlyDepreciation($asset->id, $fiscalYear, $fiscalMonth);

            if ($depreciationAmount <= 0) {
                throw new \Exception('No depreciation to post for this period');
            }

            // Create depreciation record
            $depreciation = FinanceAssetDepreciation::create([
                'asset_id' => $asset->id,
                'company_id' => $asset->company_id,
                'depreciation_date_bs' => $fiscalYear . '-' . str_pad($fiscalMonth, 2, '0', STR_PAD_LEFT) . '-01',
                'fiscal_year_bs' => $fiscalYear,
                'fiscal_month_bs' => $fiscalMonth,
                'depreciation_method' => $asset->depreciation_method,
                'depreciation_rate' => $asset->depreciation_rate,
                'depreciation_amount' => $depreciationAmount,
                'accumulated_depreciation' => $asset->accumulated_depreciation + $depreciationAmount,
                'net_book_value' => $asset->purchase_value - ($asset->accumulated_depreciation + $depreciationAmount),
                'created_by' => $userId,
            ]);

            // Get depreciation accounts
            $accounts = $this->getDepreciationAccounts($asset->company_id, $asset->category_id);

            if (!$accounts['expense'] || !$accounts['accumulated']) {
                throw new \Exception('Depreciation accounts not configured for this asset category');
            }

            // Create journal entry
            $journalEntry = FinanceJournalEntry::create([
                'company_id' => $asset->company_id,
                'entry_number' => $this->generateEntryNumber($asset->company_id, $fiscalYear, $fiscalMonth),
                'reference_number' => $asset->asset_code,
                'entry_date_bs' => $fiscalYear . '-' . str_pad($fiscalMonth, 2, '0', STR_PAD_LEFT) . '-01',
                'fiscal_year_bs' => $fiscalYear,
                'fiscal_month_bs' => $fiscalMonth,
                'entry_type' => 'depreciation',
                'source_type' => FinanceAssetDepreciation::class,
                'source_id' => $depreciation->id,
                'description' => "Depreciation for {$asset->asset_name} ({$asset->asset_code})",
                'total_debit' => $depreciationAmount,
                'total_credit' => $depreciationAmount,
                'status' => 'draft',
                'created_by' => $userId,
            ]);

            // Debit: Depreciation Expense
            FinanceJournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $accounts['expense']->id,
                'line_number' => 1,
                'description' => "Depreciation expense - {$asset->asset_name}",
                'debit_amount' => $depreciationAmount,
                'credit_amount' => 0,
                'category_id' => $asset->category_id,
                'department' => $asset->department,
            ]);

            // Credit: Accumulated Depreciation (Contra-Asset)
            FinanceJournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $accounts['accumulated']->id,
                'line_number' => 2,
                'description' => "Accumulated depreciation - {$asset->asset_name}",
                'debit_amount' => 0,
                'credit_amount' => $depreciationAmount,
                'category_id' => $asset->category_id,
                'department' => $asset->department,
            ]);

            // Auto-post if enabled
            $journalEntry->post($userId);

            // Update asset
            $asset->accumulated_depreciation += $depreciationAmount;
            $asset->net_book_value = $asset->purchase_value - $asset->accumulated_depreciation;
            $asset->save();

            return [
                'depreciation' => $depreciation,
                'journal_entry' => $journalEntry->load('lines'),
            ];
        });
    }

    /**
     * Batch process depreciation for all assets in a company
     */
    public function batchCalculateDepreciation($companyId, $fiscalYear, $fiscalMonth, $userId)
    {
        $assets = FinanceAsset::where('company_id', $companyId)
            ->where('status', 'active')
            ->whereIn('depreciation_method', ['straight_line', 'declining_balance', 'double_declining', 'sum_of_years'])
            ->get();

        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($assets as $asset) {
            try {
                $result = $this->postDepreciationEntry($asset, $fiscalYear, $fiscalMonth, $userId);
                $results['success'][] = [
                    'asset' => $asset->asset_name,
                    'amount' => $result['depreciation']->depreciation_amount,
                    'entry_number' => $result['journal_entry']->entry_number,
                ];
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'asset' => $asset->asset_name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get expense and accumulated depreciation accounts
     */
    protected function getDepreciationAccounts($companyId, $categoryId = null)
    {
        // Try to get category-specific accounts first
        $expenseAccount = FinanceChartOfAccount::where('company_id', $companyId)
            ->where('account_subtype', 'depreciation_expense')
            ->where('is_active', true)
            ->first();

        $accumulatedAccount = FinanceChartOfAccount::where('company_id', $companyId)
            ->where('account_subtype', 'accumulated_depreciation')
            ->where('is_active', true)
            ->first();

        return [
            'expense' => $expenseAccount,
            'accumulated' => $accumulatedAccount,
        ];
    }

    /**
     * Generate journal entry number
     */
    protected function generateEntryNumber($companyId, $fiscalYear, $fiscalMonth)
    {
        $prefix = 'JE-DEP';
        $count = FinanceJournalEntry::where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->where('fiscal_month_bs', $fiscalMonth)
            ->where('entry_type', 'depreciation')
            ->count();

        return $prefix . '-' . $fiscalYear . '-' . str_pad($fiscalMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    // Depreciation calculation methods
    protected function calculateStraightLine($asset)
    {
        $depreciableValue = $asset->purchase_value - $asset->salvage_value;
        $monthlyDepreciation = $depreciableValue / ($asset->useful_life_months ?: 1);

        return min($monthlyDepreciation, $depreciableValue - $asset->accumulated_depreciation);
    }

    protected function calculateDecliningBalance($asset)
    {
        $rate = $asset->depreciation_rate / 100;
        $monthlyRate = $rate / 12;
        $bookValue = $asset->purchase_value - $asset->accumulated_depreciation;

        return max(0, min($bookValue * $monthlyRate, $bookValue - $asset->salvage_value));
    }

    protected function calculateDoubleDeclining($asset)
    {
        $rate = (2 / ($asset->useful_life_months / 12)) / 12;
        $bookValue = $asset->purchase_value - $asset->accumulated_depreciation;

        return max(0, min($bookValue * $rate, $bookValue - $asset->salvage_value));
    }

    protected function calculateSumOfYears($asset)
    {
        $years = $asset->useful_life_months / 12;
        $sumOfYears = ($years * ($years + 1)) / 2;
        $monthsElapsed = FinanceAssetDepreciation::where('asset_id', $asset->id)->count();
        $yearsElapsed = floor($monthsElapsed / 12);
        $remainingYears = $years - $yearsElapsed;

        $annualDepreciation = (($asset->purchase_value - $asset->salvage_value) * $remainingYears) / $sumOfYears;

        return $annualDepreciation / 12;
    }
}
