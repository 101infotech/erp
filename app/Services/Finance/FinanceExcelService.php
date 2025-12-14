<?php

namespace App\Services\Finance;

use App\Models\FinanceCompany;
use App\Exports\Finance\ProfitLossExport;
use App\Exports\Finance\BalanceSheetExport;
use App\Exports\Finance\CashFlowExport;
use App\Exports\Finance\TrialBalanceExport;
use App\Exports\Finance\ExpenseSummaryExport;
use App\Exports\Finance\ConsolidatedReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FinanceExcelService
{
    protected FinanceReportService $reportService;

    public function __construct(FinanceReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Export Profit & Loss Statement to Excel
     */
    public function exportProfitLoss(int $companyId, string $fiscalYear, ?int $month = null): BinaryFileResponse
    {
        $company = FinanceCompany::findOrFail($companyId);
        $report = $this->reportService->generateProfitLoss($companyId, $fiscalYear, $month);

        $filename = $this->generateFilename($company, 'ProfitLoss', $fiscalYear, $month);

        return Excel::download(new ProfitLossExport($report, $company->name), $filename);
    }

    /**
     * Export Balance Sheet to Excel
     */
    public function exportBalanceSheet(int $companyId, string $asOfDate): BinaryFileResponse
    {
        $company = FinanceCompany::findOrFail($companyId);
        $report = $this->reportService->generateBalanceSheet($companyId, $asOfDate);

        $filename = $this->generateFilename($company, 'BalanceSheet', $asOfDate);

        return Excel::download(new BalanceSheetExport($report, $company->name), $filename);
    }

    /**
     * Export Cash Flow Statement to Excel
     */
    public function exportCashFlow(int $companyId, string $fiscalYear, ?int $month = null): BinaryFileResponse
    {
        $company = FinanceCompany::findOrFail($companyId);
        $report = $this->reportService->generateCashFlow($companyId, $fiscalYear, $month);

        $filename = $this->generateFilename($company, 'CashFlow', $fiscalYear, $month);

        return Excel::download(new CashFlowExport($report, $company->name), $filename);
    }

    /**
     * Export Trial Balance to Excel
     */
    public function exportTrialBalance(int $companyId, string $asOfDate): BinaryFileResponse
    {
        $company = FinanceCompany::findOrFail($companyId);
        $report = $this->reportService->generateTrialBalance($companyId, $asOfDate);

        $filename = $this->generateFilename($company, 'TrialBalance', $asOfDate);

        return Excel::download(new TrialBalanceExport($report, $company->name), $filename);
    }

    /**
     * Export Expense Summary to Excel
     */
    public function exportExpenseSummary(int $companyId, string $fiscalYear): BinaryFileResponse
    {
        $company = FinanceCompany::findOrFail($companyId);
        $report = $this->reportService->generateExpenseSummary($companyId, $fiscalYear);

        $filename = $this->generateFilename($company, 'ExpenseSummary', $fiscalYear);

        return Excel::download(new ExpenseSummaryExport($report, $company->name), $filename);
    }

    /**
     * Export Consolidated Report to Excel
     */
    public function exportConsolidatedReport(string $fiscalYear): BinaryFileResponse
    {
        $report = $this->reportService->generateConsolidatedReport($fiscalYear);

        $filename = 'ConsolidatedReport_FY' . $fiscalYear . '_' . date('Ymd') . '.xlsx';

        return Excel::download(new ConsolidatedReportExport($report), $filename);
    }

    /**
     * Generate filename for Excel
     */
    protected function generateFilename(FinanceCompany $company, string $reportType, string $period, ?int $month = null): string
    {
        $companySlug = str_replace(array(' ', '.', ','), '_', $company->name);
        $monthSuffix = $month ? '_M' . str_pad($month, 2, '0', STR_PAD_LEFT) : '';

        return "{$companySlug}_{$reportType}_{$period}{$monthSuffix}_" . date('Ymd') . '.xlsx';
    }
}
