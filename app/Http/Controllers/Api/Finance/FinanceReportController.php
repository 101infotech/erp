<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Services\Finance\FinanceReportService;
use App\Services\Finance\FinanceDashboardService;
use App\Services\Finance\FinancePdfService;
use App\Services\Finance\FinanceExcelService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FinanceReportController extends Controller
{
    protected FinanceReportService $reportService;
    protected FinanceDashboardService $dashboardService;
    protected FinancePdfService $pdfService;
    protected FinanceExcelService $excelService;

    public function __construct(
        FinanceReportService $reportService,
        FinanceDashboardService $dashboardService,
        FinancePdfService $pdfService,
        FinanceExcelService $excelService
    ) {
        $this->reportService = $reportService;
        $this->dashboardService = $dashboardService;
        $this->pdfService = $pdfService;
        $this->excelService = $excelService;
    }

    /**
     * Generate Profit & Loss Statement
     */
    public function profitLoss(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:finance_companies,id',
                'fiscal_year' => 'required|string|regex:/^\d{4}$/',
                'month' => 'nullable|integer|min:1|max:12',
            ]);

            $report = $this->reportService->generateProfitLoss(
                $request->input('company_id'),
                $request->input('fiscal_year'),
                $request->input('month')
            );

            return response()->json([
                'success' => true,
                'data' => $report,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate profit & loss statement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Balance Sheet
     */
    public function balanceSheet(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:finance_companies,id',
                'as_of_date' => 'required|string|regex:/^\d{4}-\d{2}-\d{2}$/',
            ]);

            $report = $this->reportService->generateBalanceSheet(
                $request->input('company_id'),
                $request->input('as_of_date')
            );

            return response()->json([
                'success' => true,
                'data' => $report,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate balance sheet',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Cash Flow Statement
     */
    public function cashFlow(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:finance_companies,id',
                'fiscal_year' => 'required|string|regex:/^\d{4}$/',
                'month' => 'nullable|integer|min:1|max:12',
            ]);

            $report = $this->reportService->generateCashFlow(
                $request->input('company_id'),
                $request->input('fiscal_year'),
                $request->input('month')
            );

            return response()->json([
                'success' => true,
                'data' => $report,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate cash flow statement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Trial Balance
     */
    public function trialBalance(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:finance_companies,id',
                'as_of_date' => 'required|string|regex:/^\d{4}-\d{2}-\d{2}$/',
            ]);

            $report = $this->reportService->generateTrialBalance(
                $request->input('company_id'),
                $request->input('as_of_date')
            );

            return response()->json([
                'success' => true,
                'data' => $report,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate trial balance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Expense Summary Report
     */
    public function expenseSummary(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:finance_companies,id',
                'fiscal_year' => 'required|string|regex:/^\d{4}$/',
            ]);

            $report = $this->reportService->generateExpenseSummary(
                $request->input('company_id'),
                $request->input('fiscal_year')
            );

            return response()->json([
                'success' => true,
                'data' => $report,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate expense summary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Consolidated Report (all companies)
     */
    public function consolidatedReport(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'fiscal_year' => 'required|string|regex:/^\d{4}$/',
            ]);

            $report = $this->reportService->generateConsolidatedReport(
                $request->input('fiscal_year')
            );

            return response()->json([
                'success' => true,
                'data' => $report,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate consolidated report',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get dashboard analytics
     */
    public function dashboard(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:finance_companies,id',
                'fiscal_year' => 'required|string|regex:/^\d{4}$/',
            ]);

            $dashboard = $this->dashboardService->getDashboardData(
                $request->input('company_id'),
                $request->input('fiscal_year')
            );

            return response()->json([
                'success' => true,
                'data' => $dashboard,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get KPIs only
     */
    public function kpis(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:finance_companies,id',
                'fiscal_year' => 'required|string|regex:/^\d{4}$/',
            ]);

            $kpis = $this->dashboardService->getKPIs(
                $request->input('company_id'),
                $request->input('fiscal_year')
            );

            return response()->json([
                'success' => true,
                'data' => $kpis,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch KPIs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get revenue trends
     */
    public function revenueTrends(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:finance_companies,id',
                'fiscal_year' => 'required|string|regex:/^\d{4}$/',
            ]);

            $trends = $this->dashboardService->getRevenueTrends(
                $request->input('company_id'),
                $request->input('fiscal_year')
            );

            return response()->json([
                'success' => true,
                'data' => $trends,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch revenue trends',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // PDF Export Methods

    /**
     * Export Profit & Loss to PDF
     */
    public function profitLossPdf(Request $request): Response
    {
        $request->validate([
            'company_id' => 'required|integer|exists:finance_companies,id',
            'fiscal_year' => 'required|string|regex:/^\d{4}$/',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        return $this->pdfService->exportProfitLoss(
            $request->input('company_id'),
            $request->input('fiscal_year'),
            $request->input('month')
        );
    }

    /**
     * Export Balance Sheet to PDF
     */
    public function balanceSheetPdf(Request $request): Response
    {
        $request->validate([
            'company_id' => 'required|integer|exists:finance_companies,id',
            'as_of_date' => 'required|string|regex:/^\d{4}-\d{2}-\d{2}$/',
        ]);

        return $this->pdfService->exportBalanceSheet(
            $request->input('company_id'),
            $request->input('as_of_date')
        );
    }

    /**
     * Export Cash Flow to PDF
     */
    public function cashFlowPdf(Request $request): Response
    {
        $request->validate([
            'company_id' => 'required|integer|exists:finance_companies,id',
            'fiscal_year' => 'required|string|regex:/^\d{4}$/',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        return $this->pdfService->exportCashFlow(
            $request->input('company_id'),
            $request->input('fiscal_year'),
            $request->input('month')
        );
    }

    /**
     * Export Trial Balance to PDF
     */
    public function trialBalancePdf(Request $request): Response
    {
        $request->validate([
            'company_id' => 'required|integer|exists:finance_companies,id',
            'as_of_date' => 'required|string|regex:/^\d{4}-\d{2}-\d{2}$/',
        ]);

        return $this->pdfService->exportTrialBalance(
            $request->input('company_id'),
            $request->input('as_of_date')
        );
    }

    /**
     * Export Expense Summary to PDF
     */
    public function expenseSummaryPdf(Request $request): Response
    {
        $request->validate([
            'company_id' => 'required|integer|exists:finance_companies,id',
            'fiscal_year' => 'required|string|regex:/^\d{4}$/',
        ]);

        return $this->pdfService->exportExpenseSummary(
            $request->input('company_id'),
            $request->input('fiscal_year')
        );
    }

    /**
     * Export Consolidated Report to PDF
     */
    public function consolidatedReportPdf(Request $request): Response
    {
        $request->validate([
            'fiscal_year' => 'required|string|regex:/^\d{4}$/',
        ]);

        return $this->pdfService->exportConsolidatedReport(
            $request->input('fiscal_year')
        );
    }

    // Excel Export Methods

    /**
     * Export Profit & Loss to Excel
     */
    public function profitLossExcel(Request $request): BinaryFileResponse
    {
        $request->validate([
            'company_id' => 'required|integer|exists:finance_companies,id',
            'fiscal_year' => 'required|string|regex:/^\d{4}$/',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        return $this->excelService->exportProfitLoss(
            $request->input('company_id'),
            $request->input('fiscal_year'),
            $request->input('month')
        );
    }

    /**
     * Export Balance Sheet to Excel
     */
    public function balanceSheetExcel(Request $request): BinaryFileResponse
    {
        $request->validate([
            'company_id' => 'required|integer|exists:finance_companies,id',
            'as_of_date' => 'required|string|regex:/^\d{4}-\d{2}-\d{2}$/',
        ]);

        return $this->excelService->exportBalanceSheet(
            $request->input('company_id'),
            $request->input('as_of_date')
        );
    }

    /**
     * Export Cash Flow to Excel
     */
    public function cashFlowExcel(Request $request): BinaryFileResponse
    {
        $request->validate([
            'company_id' => 'required|integer|exists:finance_companies,id',
            'fiscal_year' => 'required|string|regex:/^\d{4}$/',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        return $this->excelService->exportCashFlow(
            $request->input('company_id'),
            $request->input('fiscal_year'),
            $request->input('month')
        );
    }

    /**
     * Export Trial Balance to Excel
     */
    public function trialBalanceExcel(Request $request): BinaryFileResponse
    {
        $request->validate([
            'company_id' => 'required|integer|exists:finance_companies,id',
            'as_of_date' => 'required|string|regex:/^\d{4}-\d{2}-\d{2}$/',
        ]);

        return $this->excelService->exportTrialBalance(
            $request->input('company_id'),
            $request->input('as_of_date')
        );
    }

    /**
     * Export Expense Summary to Excel
     */
    public function expenseSummaryExcel(Request $request): BinaryFileResponse
    {
        $request->validate([
            'company_id' => 'required|integer|exists:finance_companies,id',
            'fiscal_year' => 'required|string|regex:/^\d{4}$/',
        ]);

        return $this->excelService->exportExpenseSummary(
            $request->input('company_id'),
            $request->input('fiscal_year')
        );
    }

    /**
     * Export Consolidated Report to Excel
     */
    public function consolidatedReportExcel(Request $request): BinaryFileResponse
    {
        $request->validate([
            'fiscal_year' => 'required|string|regex:/^\d{4}$/',
        ]);

        return $this->excelService->exportConsolidatedReport(
            $request->input('fiscal_year')
        );
    }
}
