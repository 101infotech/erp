<?php

namespace App\Services\Finance;

use App\Models\FinanceCompany;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class FinancePdfService
{
    protected FinanceReportService $reportService;

    public function __construct(FinanceReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Export Profit & Loss Statement to PDF
     */
    public function exportProfitLoss(int $companyId, string $fiscalYear, ?int $month = null): Response
    {
        $company = FinanceCompany::findOrFail($companyId);
        $report = $this->reportService->generateProfitLoss($companyId, $fiscalYear, $month);

        $data = array(
            'company' => $company,
            'report' => $report,
            'title' => 'Profit & Loss Statement',
            'generated_at' => now()->format('Y-m-d H:i:s'),
        );

        $pdf = Pdf::loadView('finance.reports.pdf.profit-loss', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = $this->generateFilename($company, 'ProfitLoss', $fiscalYear, $month);

        return $pdf->download($filename);
    }

    /**
     * Export Balance Sheet to PDF
     */
    public function exportBalanceSheet(int $companyId, string $asOfDate): Response
    {
        $company = FinanceCompany::findOrFail($companyId);
        $report = $this->reportService->generateBalanceSheet($companyId, $asOfDate);

        $data = array(
            'company' => $company,
            'report' => $report,
            'title' => 'Balance Sheet',
            'generated_at' => now()->format('Y-m-d H:i:s'),
        );

        $pdf = Pdf::loadView('finance.reports.pdf.balance-sheet', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = $this->generateFilename($company, 'BalanceSheet', $asOfDate);

        return $pdf->download($filename);
    }

    /**
     * Export Cash Flow Statement to PDF
     */
    public function exportCashFlow(int $companyId, string $fiscalYear, ?int $month = null): Response
    {
        $company = FinanceCompany::findOrFail($companyId);
        $report = $this->reportService->generateCashFlow($companyId, $fiscalYear, $month);

        $data = array(
            'company' => $company,
            'report' => $report,
            'title' => 'Cash Flow Statement',
            'generated_at' => now()->format('Y-m-d H:i:s'),
        );

        $pdf = Pdf::loadView('finance.reports.pdf.cash-flow', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = $this->generateFilename($company, 'CashFlow', $fiscalYear, $month);

        return $pdf->download($filename);
    }

    /**
     * Export Trial Balance to PDF
     */
    public function exportTrialBalance(int $companyId, string $asOfDate): Response
    {
        $company = FinanceCompany::findOrFail($companyId);
        $report = $this->reportService->generateTrialBalance($companyId, $asOfDate);

        $data = array(
            'company' => $company,
            'report' => $report,
            'title' => 'Trial Balance',
            'generated_at' => now()->format('Y-m-d H:i:s'),
        );

        $pdf = Pdf::loadView('finance.reports.pdf.trial-balance', $data);
        $pdf->setPaper('a4', 'landscape');

        $filename = $this->generateFilename($company, 'TrialBalance', $asOfDate);

        return $pdf->download($filename);
    }

    /**
     * Export Expense Summary to PDF
     */
    public function exportExpenseSummary(int $companyId, string $fiscalYear): Response
    {
        $company = FinanceCompany::findOrFail($companyId);
        $report = $this->reportService->generateExpenseSummary($companyId, $fiscalYear);

        $data = array(
            'company' => $company,
            'report' => $report,
            'title' => 'Expense Summary',
            'generated_at' => now()->format('Y-m-d H:i:s'),
        );

        $pdf = Pdf::loadView('finance.reports.pdf.expense-summary', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = $this->generateFilename($company, 'ExpenseSummary', $fiscalYear);

        return $pdf->download($filename);
    }

    /**
     * Export Consolidated Report to PDF
     */
    public function exportConsolidatedReport(string $fiscalYear): Response
    {
        $report = $this->reportService->generateConsolidatedReport($fiscalYear);

        $data = array(
            'report' => $report,
            'title' => 'Consolidated Financial Report',
            'generated_at' => now()->format('Y-m-d H:i:s'),
        );

        $pdf = Pdf::loadView('finance.reports.pdf.consolidated', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'ConsolidatedReport_FY' . $fiscalYear . '_' . date('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate filename for PDF
     */
    protected function generateFilename(FinanceCompany $company, string $reportType, string $period, ?int $month = null): string
    {
        $companySlug = str_replace(array(' ', '.', ','), '_', $company->name);
        $monthSuffix = $month ? '_M' . str_pad($month, 2, '0', STR_PAD_LEFT) : '';

        return "{$companySlug}_{$reportType}_{$period}{$monthSuffix}_" . date('Ymd') . '.pdf';
    }
}
