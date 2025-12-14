<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TeamMemberController;
use App\Http\Controllers\Api\NewsMediaController;
use App\Http\Controllers\Api\CareerController;
use App\Http\Controllers\Api\CaseStudyController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ContactFormController;
use App\Http\Controllers\Api\BookingFormController;
use App\Http\Controllers\Api\HrmEmployeeController;
use App\Http\Controllers\Api\HrmAttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\Finance\FinanceTransactionController;
use App\Http\Controllers\Api\Finance\FinanceSaleController;
use App\Http\Controllers\Api\Finance\FinancePurchaseController;
use App\Http\Controllers\Api\Finance\FinanceDocumentController;
use App\Http\Controllers\Api\Finance\FinanceReportController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API Routes (v1) - Production-Ready for Brand Bird Agency
Route::prefix('v1')->group(function () {

    // Lead Submission (POST only) - Rate limited to 10 requests per minute
    Route::post('leads', [LeadController::class, 'store'])->middleware('throttle:10,1');

    // Services (GET only)
    Route::get('services', [ServiceController::class, 'index']);

    // Case Studies (GET only)
    Route::get('case-studies', [CaseStudyController::class, 'index']);
    Route::get('case-studies/{slug}', [CaseStudyController::class, 'show']);

    // Team Members (GET only)
    Route::get('team', [TeamController::class, 'index']);

    // Legacy routes (backward compatibility)
    Route::get('team-members', [TeamMemberController::class, 'index']);
    Route::get('team-members/{id}', [TeamMemberController::class, 'show']);

    // News & Media
    Route::get('news', [NewsMediaController::class, 'index']);
    Route::get('news/{slug}', [NewsMediaController::class, 'show']);

    // Careers
    Route::get('careers', [CareerController::class, 'index']);
    Route::get('careers/{slug}', [CareerController::class, 'show']);

    // Blogs
    Route::get('blogs', [BlogController::class, 'index']);
    Route::get('blogs/{slug}', [BlogController::class, 'show']);

    // Contact Form Submission
    Route::post('contact', [ContactFormController::class, 'store']);

    // Booking Form Submission
    Route::post('booking', [BookingFormController::class, 'store']);

    // HRM API Routes
    Route::prefix('hrm')->group(function () {
        // Employees
        Route::get('employees', [HrmEmployeeController::class, 'index']);
        Route::get('employees/{id}', [HrmEmployeeController::class, 'show']);

        // Attendance
        Route::get('attendance', [HrmAttendanceController::class, 'index']);
    });

    // Dashboard API Routes
    Route::prefix('dashboard')->group(function () {
        Route::get('stats', [DashboardController::class, 'stats']);
        Route::get('projects', [DashboardController::class, 'projects']);
        Route::get('calendar', [DashboardController::class, 'calendar']);
        Route::get('yearly-profit', [DashboardController::class, 'yearlyProfit']);
    });

    // Finance Module API Routes (Authenticated)
    Route::prefix('finance')->middleware('auth:sanctum')->group(function () {

        // Companies
        Route::get('companies', [\App\Http\Controllers\Api\Finance\FinanceCompanyController::class, 'index'])->name('finance.companies.index');

        // Transactions
        Route::apiResource('transactions', FinanceTransactionController::class);
        Route::post('transactions/{transaction}/approve', [FinanceTransactionController::class, 'approve'])->name('finance.transactions.approve');
        Route::post('transactions/{transaction}/complete', [FinanceTransactionController::class, 'complete'])->name('finance.transactions.complete');
        Route::post('transactions/{transaction}/cancel', [FinanceTransactionController::class, 'cancel'])->name('finance.transactions.cancel');
        Route::post('transactions/{transaction}/reverse', [FinanceTransactionController::class, 'reverse'])->name('finance.transactions.reverse');
        Route::get('transactions/{transaction}/download', [FinanceDocumentController::class, 'downloadTransactionDocument'])->name('finance.transactions.download');
        Route::get('transactions-summary/period-totals', [FinanceTransactionController::class, 'periodTotals'])->name('finance.transactions.period-totals');
        Route::get('transactions-summary/category-breakdown', [FinanceTransactionController::class, 'categoryBreakdown'])->name('finance.transactions.category-breakdown');
        Route::get('transactions-summary/monthly-trends', [FinanceTransactionController::class, 'monthlyTrends'])->name('finance.transactions.monthly-trends');

        // Sales
        Route::apiResource('sales', FinanceSaleController::class);
        Route::post('sales/{sale}/payment', [FinanceSaleController::class, 'recordPayment'])->name('finance.sales.payment');
        Route::get('sales/{sale}/download', [FinanceDocumentController::class, 'downloadSaleDocument'])->name('finance.sales.download');
        Route::get('sales-summary/summary', [FinanceSaleController::class, 'summary'])->name('finance.sales.summary');
        Route::get('sales-summary/customer-sales', [FinanceSaleController::class, 'customerSales'])->name('finance.sales.customer-sales');
        Route::get('sales-summary/monthly-trends', [FinanceSaleController::class, 'monthlyTrends'])->name('finance.sales.monthly-trends');

        // Purchases
        Route::apiResource('purchases', FinancePurchaseController::class);
        Route::post('purchases/{purchase}/payment', [FinancePurchaseController::class, 'recordPayment'])->name('finance.purchases.payment');
        Route::get('purchases/{purchase}/download', [FinanceDocumentController::class, 'downloadPurchaseDocument'])->name('finance.purchases.download');
        Route::get('purchases-summary/summary', [FinancePurchaseController::class, 'summary'])->name('finance.purchases.summary');
        Route::get('purchases-summary/vendor-purchases', [FinancePurchaseController::class, 'vendorPurchases'])->name('finance.purchases.vendor-purchases');
        Route::get('purchases-summary/monthly-trends', [FinancePurchaseController::class, 'monthlyTrends'])->name('finance.purchases.monthly-trends');
        Route::get('purchases-summary/tds-summary', [FinancePurchaseController::class, 'tdsSummary'])->name('finance.purchases.tds-summary');

        // Reports
        Route::get('reports/profit-loss', [FinanceReportController::class, 'profitLoss'])->name('finance.reports.profit-loss');
        Route::get('reports/balance-sheet', [FinanceReportController::class, 'balanceSheet'])->name('finance.reports.balance-sheet');
        Route::get('reports/cash-flow', [FinanceReportController::class, 'cashFlow'])->name('finance.reports.cash-flow');
        Route::get('reports/trial-balance', [FinanceReportController::class, 'trialBalance'])->name('finance.reports.trial-balance');
        Route::get('reports/expense-summary', [FinanceReportController::class, 'expenseSummary'])->name('finance.reports.expense-summary');
        Route::get('reports/consolidated', [FinanceReportController::class, 'consolidatedReport'])->name('finance.reports.consolidated');

        // PDF Exports
        Route::get('reports/profit-loss/pdf', [FinanceReportController::class, 'profitLossPdf'])->name('finance.reports.profit-loss.pdf');
        Route::get('reports/balance-sheet/pdf', [FinanceReportController::class, 'balanceSheetPdf'])->name('finance.reports.balance-sheet.pdf');
        Route::get('reports/cash-flow/pdf', [FinanceReportController::class, 'cashFlowPdf'])->name('finance.reports.cash-flow.pdf');
        Route::get('reports/trial-balance/pdf', [FinanceReportController::class, 'trialBalancePdf'])->name('finance.reports.trial-balance.pdf');
        Route::get('reports/expense-summary/pdf', [FinanceReportController::class, 'expenseSummaryPdf'])->name('finance.reports.expense-summary.pdf');
        Route::get('reports/consolidated/pdf', [FinanceReportController::class, 'consolidatedReportPdf'])->name('finance.reports.consolidated.pdf');

        // Excel Exports
        Route::get('reports/profit-loss/excel', [FinanceReportController::class, 'profitLossExcel'])->name('finance.reports.profit-loss.excel');
        Route::get('reports/balance-sheet/excel', [FinanceReportController::class, 'balanceSheetExcel'])->name('finance.reports.balance-sheet.excel');
        Route::get('reports/cash-flow/excel', [FinanceReportController::class, 'cashFlowExcel'])->name('finance.reports.cash-flow.excel');
        Route::get('reports/trial-balance/excel', [FinanceReportController::class, 'trialBalanceExcel'])->name('finance.reports.trial-balance.excel');
        Route::get('reports/expense-summary/excel', [FinanceReportController::class, 'expenseSummaryExcel'])->name('finance.reports.expense-summary.excel');
        Route::get('reports/consolidated/excel', [FinanceReportController::class, 'consolidatedReportExcel'])->name('finance.reports.consolidated.excel');

        // Dashboard Analytics
        Route::get('dashboard', [FinanceReportController::class, 'dashboard'])->name('finance.dashboard');
        Route::get('dashboard/kpis', [FinanceReportController::class, 'kpis'])->name('finance.dashboard.kpis');
        Route::get('dashboard/revenue-trends', [FinanceReportController::class, 'revenueTrends'])->name('finance.dashboard.revenue-trends');
    });
});
