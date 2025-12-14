<?php

namespace App\Services;

/**
 * Nepal Tax Calculation Service
 * 
 * Implements Nepal's progressive income tax slabs for FY 2081/82 (2024/25)
 * Tax slabs (Annual Income):
 * - Up to NPR 500,000: 1%
 * - NPR 500,001 - 700,000: 10%
 * - NPR 700,001 - 1,000,000: 20%
 * - NPR 1,000,001 - 2,000,000: 30%
 * - NPR 2,000,001 - 5,000,000: 36%
 * - Above NPR 5,000,000: 39%
 */
class NepalTaxCalculationService
{
    // Tax slabs in NPR (annual)
    private const TAX_SLABS = [
        ['limit' => 500000, 'rate' => 0.01],
        ['limit' => 700000, 'rate' => 0.10],
        ['limit' => 1000000, 'rate' => 0.20],
        ['limit' => 2000000, 'rate' => 0.30],
        ['limit' => 5000000, 'rate' => 0.36],
        ['limit' => PHP_INT_MAX, 'rate' => 0.39],
    ];

    /**
     * Calculate annual tax based on annual income
     *
     * @param float $annualIncome Total annual taxable income in NPR
     * @return array ['tax_amount' => float, 'effective_rate' => float, 'breakdown' => array]
     */
    public function calculateAnnualTax(float $annualIncome): array
    {
        if ($annualIncome <= 0) {
            return [
                'tax_amount' => 0,
                'effective_rate' => 0,
                'breakdown' => [],
            ];
        }

        $totalTax = 0;
        $remainingIncome = $annualIncome;
        $previousLimit = 0;
        $breakdown = [];

        foreach (self::TAX_SLABS as $slab) {
            if ($remainingIncome <= 0) {
                break;
            }

            $slabLimit = $slab['limit'];
            $taxableInThisSlab = min($remainingIncome, $slabLimit - $previousLimit);

            if ($taxableInThisSlab > 0) {
                $taxInThisSlab = $taxableInThisSlab * $slab['rate'];
                $totalTax += $taxInThisSlab;

                $breakdown[] = [
                    'slab_min' => $previousLimit,
                    'slab_max' => $slabLimit === PHP_INT_MAX ? 'Above' : $slabLimit,
                    'rate' => $slab['rate'] * 100,
                    'taxable_amount' => $taxableInThisSlab,
                    'tax' => $taxInThisSlab,
                ];

                $remainingIncome -= $taxableInThisSlab;
            }

            $previousLimit = $slabLimit;
        }

        return [
            'tax_amount' => round($totalTax, 2),
            'effective_rate' => $annualIncome > 0 ? round(($totalTax / $annualIncome) * 100, 2) : 0,
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Calculate monthly tax from monthly income (extrapolated to annual)
     *
     * @param float $monthlyIncome Monthly taxable income in NPR
     * @return array ['monthly_tax' => float, 'annual_tax' => float, 'breakdown' => array]
     */
    public function calculateMonthlyTax(float $monthlyIncome): array
    {
        $annualIncome = $monthlyIncome * 12;
        $annualTaxData = $this->calculateAnnualTax($annualIncome);

        return [
            'monthly_tax' => round($annualTaxData['tax_amount'] / 12, 2),
            'annual_tax' => $annualTaxData['tax_amount'],
            'annual_income' => $annualIncome,
            'effective_rate' => $annualTaxData['effective_rate'],
            'breakdown' => $annualTaxData['breakdown'],
        ];
    }

    /**
     * Calculate tax for a specific period based on gross salary
     *
     * @param float $grossSalary Gross salary for the period
     * @param int $monthsInPeriod Number of months in the period (default 1)
     * @return float Tax amount for the period
     */
    public function calculateTaxForPeriod(float $grossSalary, int $monthsInPeriod = 1): float
    {
        // Annualize the income
        $monthlyIncome = $grossSalary / $monthsInPeriod;
        $taxData = $this->calculateMonthlyTax($monthlyIncome);

        // Return tax for the period
        return round($taxData['monthly_tax'] * $monthsInPeriod, 2);
    }

    /**
     * Get tax breakdown as human-readable array
     *
     * @param float $annualIncome
     * @return array
     */
    public function getTaxBreakdownForDisplay(float $annualIncome): array
    {
        $result = $this->calculateAnnualTax($annualIncome);

        return [
            'total_tax' => number_format($result['tax_amount'], 2),
            'effective_rate' => $result['effective_rate'] . '%',
            'slabs' => array_map(function ($slab) {
                return [
                    'range' => 'NPR ' . number_format($slab['slab_min']) . ' - ' .
                        ($slab['slab_max'] === 'Above' ? 'Above' : 'NPR ' . number_format($slab['slab_max'])),
                    'rate' => $slab['rate'] . '%',
                    'taxable' => 'NPR ' . number_format($slab['taxable_amount'], 2),
                    'tax' => 'NPR ' . number_format($slab['tax'], 2),
                ];
            }, $result['breakdown'])
        ];
    }
}
