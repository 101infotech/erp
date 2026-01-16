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
use App\Http\Controllers\Api\ScheduleMeetingController;
use App\Http\Controllers\Api\HrmEmployeeController;
use App\Http\Controllers\Api\HrmAttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\Finance\FinanceTransactionController;
use App\Http\Controllers\Api\Finance\FinanceSaleController;
use App\Http\Controllers\Api\Finance\FinancePurchaseController;
use App\Http\Controllers\Api\Finance\FinanceDocumentController;
use App\Http\Controllers\Api\Finance\FinanceReportController;
use App\Http\Controllers\Api\ServiceLeadController;
use App\Http\Controllers\Api\LeadFollowUpController;
use App\Http\Controllers\Api\LeadPaymentController;
use App\Http\Controllers\Api\LeadDocumentController;
use App\Http\Controllers\Api\LeadStageController;
use App\Http\Controllers\Api\LeadAnalyticsController;
use App\Http\Controllers\MaintenanceModeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Maintenance Mode Management (Admin Only)
Route::middleware(['auth:sanctum'])->prefix('maintenance')->group(function () {
    Route::get('status', [MaintenanceModeController::class, 'status'])->name('maintenance.status');
    Route::post('enable', [MaintenanceModeController::class, 'enable'])->name('maintenance.enable');
    Route::post('disable', [MaintenanceModeController::class, 'disable'])->name('maintenance.disable');
    Route::post('message', [MaintenanceModeController::class, 'updateMessage'])->name('maintenance.message');
});

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

    // Booking Form Submission (Brand Bird Agency) - Public & Protected Routes
    Route::prefix('booking')->group(function () {
        // Public endpoint to submit booking form (rate limited)
        Route::post('/', [BookingFormController::class, 'store'])->middleware('throttle:10,1');

        // Protected endpoints (require authentication)
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/', [BookingFormController::class, 'index']);
            Route::get('/{id}', [BookingFormController::class, 'show']);
            Route::patch('/{id}/status', [BookingFormController::class, 'updateStatus']);
            Route::delete('/{id}', [BookingFormController::class, 'destroy']);
        });
    });

    // Schedule Meeting API (Saubhagya Group) - Protected Routes
    Route::prefix('schedule-meeting')->group(function () {
        // Public endpoint to submit a meeting request (rate limited)
        Route::post('/', [ScheduleMeetingController::class, 'store'])->middleware('throttle:5,1');

        // Protected endpoints (require authentication)
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/', [ScheduleMeetingController::class, 'index']);
            Route::get('/{id}', [ScheduleMeetingController::class, 'show']);
            Route::patch('/{id}/status', [ScheduleMeetingController::class, 'updateStatus']);
            Route::delete('/{id}', [ScheduleMeetingController::class, 'destroy']);
        });
    });

    // HRM API Routes
    Route::prefix('hrm')->group(function () {
        // Employees
        Route::get('employees', [HrmEmployeeController::class, 'index']);
        Route::get('employees/{id}', [HrmEmployeeController::class, 'show']);

        // Attendance
        Route::get('attendance', [HrmAttendanceController::class, 'index']);
    });

    // AI Feedback API Routes (Authenticated)
    Route::prefix('ai')->middleware('auth:sanctum')->group(function () {
        Route::prefix('feedback')->group(function () {
            // Question generation
            Route::get('questions', [\App\Http\Controllers\Api\AiFeedbackController::class, 'generateQuestions']);

            // Sentiment analysis
            Route::post('analyze-sentiment', [\App\Http\Controllers\Api\AiFeedbackController::class, 'analyzeSentiment']);

            // Weekly prompts
            Route::get('weekly-prompt', [\App\Http\Controllers\Api\AiFeedbackController::class, 'getWeeklyPrompt']);
            Route::post('submit-answer', [\App\Http\Controllers\Api\AiFeedbackController::class, 'submitAnswer']);

            // Analytics
            Route::get('sentiment-trends', [\App\Http\Controllers\Api\AiFeedbackController::class, 'getSentimentTrends']);
            Route::get('performance-insights', [\App\Http\Controllers\Api\AiFeedbackController::class, 'getPerformanceInsights']);
        });
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

    // Enhanced Leads Module - Complete Pipeline Management
    Route::prefix('leads/enhanced')->middleware('auth:sanctum')->group(function () {
        // Service Leads (Core CRUD)
        Route::get('/', [ServiceLeadController::class, 'index'])->name('leads.enhanced.index');
        Route::post('/', [ServiceLeadController::class, 'store'])->name('leads.enhanced.store');
        Route::get('/{lead}', [ServiceLeadController::class, 'show'])->name('leads.enhanced.show');
        Route::put('/{lead}', [ServiceLeadController::class, 'update'])->name('leads.enhanced.update');
        Route::delete('/{lead}', [ServiceLeadController::class, 'destroy'])->name('leads.enhanced.destroy');

        // Special Lead Queries
        Route::get('/special/needing-follow-up', [ServiceLeadController::class, 'needingFollowUp'])->name('leads.enhanced.needing-follow-up');
        Route::get('/special/pending-payment', [ServiceLeadController::class, 'pendingPayment'])->name('leads.enhanced.pending-payment');
        Route::get('/special/statistics', [ServiceLeadController::class, 'statistics'])->name('leads.enhanced.statistics');

        // Bulk Operations
        Route::post('/bulk/update', [ServiceLeadController::class, 'bulkUpdate'])->name('leads.enhanced.bulk-update');
        Route::post('/bulk/delete', [ServiceLeadController::class, 'bulkDelete'])->name('leads.enhanced.bulk-delete');

        // Stage Transitions
        Route::post('/{lead}/transition', [ServiceLeadController::class, 'transitionStage'])->name('leads.enhanced.transition-stage');

        // Lead Stages
        Route::get('/stages/all', [LeadStageController::class, 'index'])->name('leads.enhanced.stages.index');
        Route::get('/stages/{stage}', [LeadStageController::class, 'show'])->name('leads.enhanced.stages.show');
        Route::get('/stages/pipeline/view', [LeadStageController::class, 'pipeline'])->name('leads.enhanced.stages.pipeline');
        Route::get('/stages/{stage}/transition-info', [LeadStageController::class, 'transitionInfo'])->name('leads.enhanced.stages.transition-info');
        Route::get('/stages/{stage}/metrics', [LeadStageController::class, 'metrics'])->name('leads.enhanced.stages.metrics');

        // Follow-ups (Nested under Lead)
        Route::get('/{lead}/follow-ups', [LeadFollowUpController::class, 'index'])->name('leads.enhanced.follow-ups.index');
        Route::post('/{lead}/follow-ups', [LeadFollowUpController::class, 'store'])->name('leads.enhanced.follow-ups.store');
        Route::get('/{lead}/follow-ups/{followUp}', [LeadFollowUpController::class, 'show'])->name('leads.enhanced.follow-ups.show');
        Route::put('/{lead}/follow-ups/{followUp}', [LeadFollowUpController::class, 'update'])->name('leads.enhanced.follow-ups.update');
        Route::delete('/{lead}/follow-ups/{followUp}', [LeadFollowUpController::class, 'destroy'])->name('leads.enhanced.follow-ups.destroy');
        Route::get('/follow-ups/pending/all', [LeadFollowUpController::class, 'pending'])->name('leads.enhanced.follow-ups.pending');
        Route::get('/follow-ups/by-type/{type}', [LeadFollowUpController::class, 'byType'])->name('leads.enhanced.follow-ups.by-type');

        // Payments (Nested under Lead)
        Route::get('/{lead}/payments', [LeadPaymentController::class, 'index'])->name('leads.enhanced.payments.index');
        Route::post('/{lead}/payments', [LeadPaymentController::class, 'store'])->name('leads.enhanced.payments.store');
        Route::get('/{lead}/payments/{payment}', [LeadPaymentController::class, 'show'])->name('leads.enhanced.payments.show');
        Route::put('/{lead}/payments/{payment}', [LeadPaymentController::class, 'update'])->name('leads.enhanced.payments.update');
        Route::delete('/{lead}/payments/{payment}', [LeadPaymentController::class, 'destroy'])->name('leads.enhanced.payments.destroy');
        Route::get('/{lead}/payments/summary/view', [LeadPaymentController::class, 'summary'])->name('leads.enhanced.payments.summary');

        // Documents (Nested under Lead)
        Route::get('/{lead}/documents', [LeadDocumentController::class, 'index'])->name('leads.enhanced.documents.index');
        Route::post('/{lead}/documents', [LeadDocumentController::class, 'store'])->name('leads.enhanced.documents.store');
        Route::get('/{lead}/documents/{document}', [LeadDocumentController::class, 'show'])->name('leads.enhanced.documents.show');
        Route::put('/{lead}/documents/{document}', [LeadDocumentController::class, 'update'])->name('leads.enhanced.documents.update');
        Route::delete('/{lead}/documents/{document}', [LeadDocumentController::class, 'destroy'])->name('leads.enhanced.documents.destroy');
        Route::post('/{lead}/documents/{document}/restore', [LeadDocumentController::class, 'restore'])->name('leads.enhanced.documents.restore');
        Route::get('/documents/by-type/{type}', [LeadDocumentController::class, 'byType'])->name('leads.enhanced.documents.by-type');
        Route::get('/{lead}/documents/{document}/download', [LeadDocumentController::class, 'download'])->name('leads.enhanced.documents.download');

        // Analytics & Reporting
        Route::get('/analytics/dashboard', [LeadAnalyticsController::class, 'dashboard'])->name('leads.enhanced.analytics.dashboard');
        Route::get('/analytics/pipeline', [LeadAnalyticsController::class, 'pipeline'])->name('leads.enhanced.analytics.pipeline');
        Route::get('/analytics/sales-team', [LeadAnalyticsController::class, 'salesTeam'])->name('leads.enhanced.analytics.sales-team');
        Route::get('/analytics/payments', [LeadAnalyticsController::class, 'payments'])->name('leads.enhanced.analytics.payments');
        Route::get('/analytics/follow-ups', [LeadAnalyticsController::class, 'followUps'])->name('leads.enhanced.analytics.follow-ups');
        Route::get('/analytics/by-priority', [LeadAnalyticsController::class, 'byPriority'])->name('leads.enhanced.analytics.by-priority');
        Route::get('/analytics/by-source', [LeadAnalyticsController::class, 'bySource'])->name('leads.enhanced.analytics.by-source');
        Route::get('/analytics/closures', [LeadAnalyticsController::class, 'closures'])->name('leads.enhanced.analytics.closures');
    });
});
