# Finance Module - Phase 3: Reports & Dashboard Analytics

## Overview

Phase 3 implements comprehensive financial reporting and dashboard analytics capabilities with real-time KPIs, historical analysis, and projections.

## Implementation Summary

### 1. FinanceReportService

**Location**: `app/Services/Finance/FinanceReportService.php`

Complete financial reporting service with six major report types:

#### Profit & Loss Statement

-   **Method**: `generateProfitLoss($companyId, $fiscalYear, $month = null)`
-   **Features**:
    -   Revenue by category with percentages
    -   Expenses by category with percentages
    -   Net profit calculation
    -   Profit margin percentage
    -   Monthly or full-year reports
    -   Only completed transactions

#### Balance Sheet

-   **Method**: `generateBalanceSheet($companyId, $asOfDate)`
-   **Features**:
    -   Assets breakdown (current, fixed, other)
    -   Liabilities breakdown (current, long-term)
    -   Equity calculation
    -   Auto-calculated retained earnings
    -   Balance verification (Assets = Liabilities + Equity)
    -   Point-in-time snapshot

#### Cash Flow Statement

-   **Method**: `generateCashFlow($companyId, $fiscalYear, $month = null)`
-   **Features**:
    -   Operating activities
    -   Investing activities
    -   Financing activities
    -   Opening cash balance
    -   Net cash flow
    -   Closing cash balance
    -   Cash/bank account tracking

#### Trial Balance

-   **Method**: `generateTrialBalance($companyId, $asOfDate)`
-   **Features**:
    -   All accounts with debits and credits
    -   Balance verification (Total Debits = Total Credits)
    -   Discrepancy detection
    -   Account-level detail
    -   Double-entry validation

#### Expense Summary

-   **Method**: `generateExpenseSummary($companyId, $fiscalYear)`
-   **Features**:
    -   12-month breakdown
    -   Category analysis with percentages
    -   Payment method breakdown
    -   Average monthly expense
    -   Trends identification

#### Consolidated Report

-   **Method**: `generateConsolidatedReport($fiscalYear)`
-   **Features**:
    -   All active companies aggregated
    -   Revenue/expense/profit by company
    -   Group totals
    -   Profit margins
    -   Sorted by revenue

### 2. FinanceDashboardService

**Location**: `app/Services/Finance/FinanceDashboardService.php`

Real-time analytics and KPI tracking service:

#### Dashboard Data

-   **Method**: `getDashboardData($companyId, $fiscalYear)`
-   **Returns**: Complete dashboard with all analytics

#### Key Performance Indicators (KPIs)

-   **Method**: `getKPIs($companyId, $fiscalYear)`
-   **Metrics**:
    -   Total revenue with YoY growth
    -   Total expenses with YoY growth
    -   Net profit and margin
    -   Sales metrics (total, paid, pending, count)
    -   Purchase metrics (total, count)
    -   Tax metrics (VAT payable, TDS deducted)

#### Revenue Trends

-   **Method**: `getRevenueTrends($companyId, $fiscalYear)`
-   **Features**:
    -   Monthly revenue/expense/profit
    -   12-month breakdown
    -   Nepali month names
    -   Trend visualization data

#### Expense Breakdown

-   **Method**: `getExpenseBreakdown($companyId, $fiscalYear)`
-   **Features**:
    -   Top 10 categories
    -   Amount and percentage
    -   Transaction count
    -   Sorted by amount

#### Cash Flow Projection

-   **Method**: `getCashFlowProjection($companyId, $fiscalYear)`
-   **Features**:
    -   6-month forward projection
    -   Based on historical averages
    -   Monthly breakdown
    -   Projected revenue/expense/cash flow

#### Top Customers

-   **Method**: `getTopCustomers($companyId, $fiscalYear, $limit = 5)`
-   **Features**:
    -   Top 5 customers by revenue
    -   Total sales amount
    -   Order count
    -   Sorted by sales

#### Top Vendors

-   **Method**: `getTopVendors($companyId, $fiscalYear, $limit = 5)`
-   **Features**:
    -   Top 5 vendors by purchase volume
    -   Total purchase amount
    -   Order count
    -   Sorted by purchases

#### Recent Transactions

-   **Method**: `getRecentTransactions($companyId, $limit = 10)`
-   **Features**:
    -   Last 10 transactions
    -   Date, type, category, amount
    -   Status tracking
    -   Quick overview

#### Pending Payments

-   **Method**: `getPendingPayments($companyId)`
-   **Features**:
    -   Pending/partial sales
    -   Pending/partial purchases
    -   Total amounts
    -   Top 5 items each
    -   Payment tracking

### 3. FinanceReportController

**Location**: `app/Http/Controllers/Api/Finance/FinanceReportController.php`

API endpoints for accessing all reports and analytics:

#### Report Endpoints

1. **Profit & Loss**: `GET /api/v1/finance/reports/profit-loss`

    - Params: `company_id`, `fiscal_year`, `month` (optional)

2. **Balance Sheet**: `GET /api/v1/finance/reports/balance-sheet`

    - Params: `company_id`, `as_of_date` (BS format: YYYY-MM-DD)

3. **Cash Flow**: `GET /api/v1/finance/reports/cash-flow`

    - Params: `company_id`, `fiscal_year`, `month` (optional)

4. **Trial Balance**: `GET /api/v1/finance/reports/trial-balance`

    - Params: `company_id`, `as_of_date` (BS format)

5. **Expense Summary**: `GET /api/v1/finance/reports/expense-summary`

    - Params: `company_id`, `fiscal_year`

6. **Consolidated Report**: `GET /api/v1/finance/reports/consolidated`
    - Params: `fiscal_year`

#### Dashboard Endpoints

1. **Full Dashboard**: `GET /api/v1/finance/dashboard`

    - Params: `company_id`, `fiscal_year`
    - Returns: Complete dashboard with all metrics

2. **KPIs Only**: `GET /api/v1/finance/dashboard/kpis`

    - Params: `company_id`, `fiscal_year`
    - Returns: Key performance indicators

3. **Revenue Trends**: `GET /api/v1/finance/dashboard/revenue-trends`
    - Params: `company_id`, `fiscal_year`
    - Returns: 12-month trends

### 4. Routes

**Location**: `routes/api.php`

All routes protected with `auth:sanctum` middleware:

```php
// Financial Reports
Route::get('reports/profit-loss', [FinanceReportController::class, 'profitLoss']);
Route::get('reports/balance-sheet', [FinanceReportController::class, 'balanceSheet']);
Route::get('reports/cash-flow', [FinanceReportController::class, 'cashFlow']);
Route::get('reports/trial-balance', [FinanceReportController::class, 'trialBalance']);
Route::get('reports/expense-summary', [FinanceReportController::class, 'expenseSummary']);
Route::get('reports/consolidated', [FinanceReportController::class, 'consolidatedReport']);

// Dashboard Analytics
Route::get('dashboard', [FinanceReportController::class, 'dashboard']);
Route::get('dashboard/kpis', [FinanceReportController::class, 'kpis']);
Route::get('dashboard/revenue-trends', [FinanceReportController::class, 'revenueTrends']);
```

## Data Integrity

### Financial Accuracy

-   All calculations based on **completed transactions only**
-   Double-entry verification in Trial Balance
-   Balance Sheet equation verified: `Assets = Liabilities + Equity`
-   Retained earnings auto-calculated: `Total Income - Total Expenses`

### Date Handling

-   All reports support Nepali BS calendar
-   Fiscal year: Shrawan (month 4) to Ashadh (month 3)
-   Point-in-time snapshots using `<=as_of_date`
-   Monthly breakdowns with Nepali month names

### Aggregation Rules

-   Consolidated reports include only active companies
-   All amounts formatted to 2 decimal places
-   Percentages rounded to 2 decimal places
-   Sorted results for better readability

## API Response Format

All endpoints return consistent JSON structure:

```json
{
    "success": true,
    "data": {
        // Report or dashboard data
    }
}
```

Error responses:

```json
{
    "success": false,
    "message": "Error description",
    "error": "Technical details"
}
```

## Testing Checklist

### Report Validation

-   [ ] Profit & Loss: Net profit = Revenue - Expenses
-   [ ] Balance Sheet: Assets = Liabilities + Equity
-   [ ] Cash Flow: Opening + Net = Closing
-   [ ] Trial Balance: Total Debits = Total Credits
-   [ ] Expense Summary: Monthly totals match fiscal year
-   [ ] Consolidated: Sum of companies = Group total

### Dashboard Validation

-   [ ] KPIs: Year-over-year growth accurate
-   [ ] Revenue Trends: 12 months with correct totals
-   [ ] Expense Breakdown: Top 10 sorted by amount
-   [ ] Cash Flow Projection: 6 months forward
-   [ ] Top Customers/Vendors: Sorted correctly
-   [ ] Recent Transactions: Limited to 10
-   [ ] Pending Payments: Only pending/partial status

### Edge Cases

-   [ ] No transactions: Returns zero values
-   [ ] Single transaction: Calculations correct
-   [ ] Multi-company: Consolidated aggregation
-   [ ] Partial fiscal year: Correct monthly breakdown
-   [ ] Future dates: Projections work
-   [ ] Invalid dates: Validation errors

## Integration Points

### Frontend Requirements

1. **Dashboard Page**:

    - KPI cards with trends
    - Revenue/expense charts
    - Top customers/vendors tables
    - Recent activity feed
    - Pending payments alerts

2. **Reports Page**:

    - Date range selector
    - Company selector
    - Report type tabs
    - Export buttons (PDF/Excel)
    - Print preview

3. **Analytics Page**:
    - Interactive charts
    - Drill-down capabilities
    - Comparison tools
    - Custom date ranges

### Required Dependencies

```json
{
    "laravel/framework": "^11.0",
    "laravel/sanctum": "^4.0"
}
```

## Next Steps (Pending)

### Phase 3 Remaining Tasks

1. **PDF Export Service** (Task 4)

    - Install DomPDF package
    - Create FinancePdfService
    - PDF templates for all reports
    - Controller methods and routes

2. **Excel Export Service** (Task 5)

    - Install PhpSpreadsheet
    - Create FinanceExcelService
    - Excel templates with charts
    - Controller methods and routes

3. **Testing** (Task 7)
    - Create test transactions
    - Verify all calculations
    - Test edge cases
    - Performance testing

## Files Created

```
app/Services/Finance/
├── FinanceReportService.php (600+ lines)
└── FinanceDashboardService.php (350+ lines)

app/Http/Controllers/Api/Finance/
└── FinanceReportController.php (300+ lines)

routes/
└── api.php (9 new routes)
```

## Total Endpoints: 48

### Phase 2 (Transaction Management): 39 endpoints

-   Companies: 5
-   Categories: 5
-   Accounts: 5
-   Transactions: 9
-   Sales: 8
-   Purchases: 7

### Phase 3 (Reports & Dashboard): 9 endpoints

-   Reports: 6
-   Dashboard: 3

## Summary

Phase 3 successfully implements:

-   ✅ Complete financial reporting (6 report types)
-   ✅ Real-time dashboard analytics (8 metrics)
-   ✅ KPI tracking with YoY comparison
-   ✅ Revenue/expense trend analysis
-   ✅ Cash flow projections
-   ✅ Top customers/vendors tracking
-   ✅ Pending payment monitoring
-   ✅ Nepali BS calendar support
-   ✅ Multi-company consolidation
-   ✅ Double-entry verification

**Status**: Core reporting and analytics complete. PDF/Excel export pending.
