# Finance Reports & Dashboard - Quick Start Guide

## Getting Started

### 1. Access Dashboard

Get comprehensive dashboard data for a company:

```bash
GET /api/v1/finance/dashboard?company_id=1&fiscal_year=2081
```

**Response**:

```json
{
  "success": true,
  "data": {
    "kpis": {
      "revenue": { "total": "5000000.00", "growth": 15.5, "previous_year": "4329896.91" },
      "expense": { "total": "3200000.00", "growth": 12.3, "previous_year": "2849004.26" },
      "profit": { "net": "1800000.00", "margin": 36 },
      "sales": { "total": "4800000.00", "paid": "3600000.00", "pending": "1200000.00", "count": 145 },
      "purchases": { "total": "2100000.00", "count": 89 },
      "tax": { "vat_payable": "156000.00", "tds_deducted": "21000.00" }
    },
    "revenue_trends": [...],
    "expense_breakdown": [...],
    "cash_flow_projection": [...],
    "top_customers": [...],
    "top_vendors": [...],
    "recent_transactions": [...],
    "pending_payments": {...}
  }
}
```

### 2. Generate Profit & Loss Statement

Get comprehensive income statement:

```bash
GET /api/v1/finance/reports/profit-loss?company_id=1&fiscal_year=2081&month=1
```

**Response**:

```json
{
    "success": true,
    "data": {
        "company": "ABC Pvt. Ltd.",
        "fiscal_year": "2081",
        "month": 1,
        "period": "Baisakh 2081",
        "revenue": {
            "by_category": [
                {
                    "category": "Sales Revenue",
                    "amount": "450000.00",
                    "percentage": 90
                },
                {
                    "category": "Service Income",
                    "amount": "50000.00",
                    "percentage": 10
                }
            ],
            "total": "500000.00"
        },
        "expenses": {
            "by_category": [
                {
                    "category": "Salary & Wages",
                    "amount": "150000.00",
                    "percentage": 46.88
                },
                { "category": "Rent", "amount": "80000.00", "percentage": 25 },
                {
                    "category": "Utilities",
                    "amount": "45000.00",
                    "percentage": 14.06
                },
                {
                    "category": "Office Supplies",
                    "amount": "45000.00",
                    "percentage": 14.06
                }
            ],
            "total": "320000.00"
        },
        "net_profit": "180000.00",
        "profit_margin": 36
    }
}
```

### 3. Generate Balance Sheet

Get financial position snapshot:

```bash
GET /api/v1/finance/reports/balance-sheet?company_id=1&as_of_date=2081-03-31
```

**Response**:

```json
{
    "success": true,
    "data": {
        "company": "ABC Pvt. Ltd.",
        "as_of_date": "2081-03-31",
        "assets": {
            "current_assets": [
                { "account": "Cash", "balance": "250000.00" },
                { "account": "Bank - Nabil", "balance": "1500000.00" },
                { "account": "Accounts Receivable", "balance": "800000.00" }
            ],
            "fixed_assets": [
                { "account": "Office Equipment", "balance": "500000.00" },
                { "account": "Furniture", "balance": "300000.00" }
            ],
            "total_assets": "3350000.00"
        },
        "liabilities": {
            "current_liabilities": [
                { "account": "Accounts Payable", "balance": "450000.00" },
                { "account": "VAT Payable", "balance": "65000.00" }
            ],
            "long_term_liabilities": [
                { "account": "Bank Loan", "balance": "1000000.00" }
            ],
            "total_liabilities": "1515000.00"
        },
        "equity": {
            "capital": [
                { "account": "Share Capital", "balance": "1000000.00" }
            ],
            "retained_earnings": "835000.00",
            "total_equity": "1835000.00"
        },
        "total_liabilities_and_equity": "3350000.00",
        "balance_check": true
    }
}
```

### 4. Get KPIs Only

Quick performance metrics:

```bash
GET /api/v1/finance/dashboard/kpis?company_id=1&fiscal_year=2081
```

### 5. Get Revenue Trends

Monthly breakdown:

```bash
GET /api/v1/finance/dashboard/revenue-trends?company_id=1&fiscal_year=2081
```

**Response**:

```json
{
  "success": true,
  "data": [
    { "month": 1, "month_name": "Baisakh", "revenue": "500000.00", "expense": "320000.00", "profit": "180000.00" },
    { "month": 2, "month_name": "Jestha", "revenue": "550000.00", "expense": "340000.00", "profit": "210000.00" },
    ...
  ]
}
```

### 6. Cash Flow Statement

Track cash movements:

```bash
GET /api/v1/finance/reports/cash-flow?company_id=1&fiscal_year=2081&month=1
```

**Response**:

```json
{
    "success": true,
    "data": {
        "company": "ABC Pvt. Ltd.",
        "period": "Baisakh 2081",
        "operating_activities": {
            "inflows": [
                { "description": "Cash from Sales", "amount": "450000.00" },
                { "description": "Service Income", "amount": "50000.00" }
            ],
            "outflows": [
                { "description": "Salary Payments", "amount": "150000.00" },
                { "description": "Rent Payment", "amount": "80000.00" }
            ],
            "net": "270000.00"
        },
        "investing_activities": {
            "inflows": [],
            "outflows": [
                { "description": "Equipment Purchase", "amount": "100000.00" }
            ],
            "net": "-100000.00"
        },
        "financing_activities": {
            "inflows": [{ "description": "Bank Loan", "amount": "500000.00" }],
            "outflows": [
                { "description": "Loan Repayment", "amount": "50000.00" }
            ],
            "net": "450000.00"
        },
        "opening_cash_balance": "100000.00",
        "net_cash_flow": "620000.00",
        "closing_cash_balance": "720000.00"
    }
}
```

### 7. Trial Balance

Verify accounting accuracy:

```bash
GET /api/v1/finance/reports/trial-balance?company_id=1&as_of_date=2081-03-31
```

**Response**:

```json
{
  "success": true,
  "data": {
    "company": "ABC Pvt. Ltd.",
    "as_of_date": "2081-03-31",
    "accounts": [
      { "account": "Cash", "account_type": "asset", "debit": "250000.00", "credit": "0.00", "balance": "250000.00" },
      { "account": "Bank - Nabil", "account_type": "asset", "debit": "1500000.00", "credit": "0.00", "balance": "1500000.00" },
      { "account": "Sales Revenue", "account_type": "income", "debit": "0.00", "credit": "5000000.00", "balance": "-5000000.00" },
      ...
    ],
    "totals": {
      "total_debits": "10500000.00",
      "total_credits": "10500000.00",
      "difference": "0.00"
    },
    "balanced": true
  }
}
```

### 8. Expense Summary

Annual expense analysis:

```bash
GET /api/v1/finance/reports/expense-summary?company_id=1&fiscal_year=2081
```

**Response**:

```json
{
  "success": true,
  "data": {
    "company": "ABC Pvt. Ltd.",
    "fiscal_year": "2081",
    "monthly_breakdown": [
      { "month": 1, "month_name": "Baisakh", "total_expenses": "320000.00" },
      { "month": 2, "month_name": "Jestha", "total_expenses": "340000.00" },
      ...
    ],
    "category_analysis": [
      { "category": "Salary & Wages", "total": "1800000.00", "percentage": 56.25, "count": 12 },
      { "category": "Rent", "total": "960000.00", "percentage": 30, "count": 12 },
      ...
    ],
    "payment_methods": [
      { "method": "bank_transfer", "total": "2400000.00", "percentage": 75 },
      { "method": "cash", "total": "800000.00", "percentage": 25 }
    ],
    "total_expenses": "3200000.00",
    "average_monthly_expense": "266666.67"
  }
}
```

### 9. Consolidated Report

Multi-company overview:

```bash
GET /api/v1/finance/reports/consolidated?fiscal_year=2081
```

**Response**:

```json
{
    "success": true,
    "data": {
        "fiscal_year": "2081",
        "companies": [
            {
                "company": "ABC Pvt. Ltd.",
                "revenue": "5000000.00",
                "expenses": "3200000.00",
                "net_profit": "1800000.00",
                "profit_margin": 36
            },
            {
                "company": "XYZ Trading",
                "revenue": "3500000.00",
                "expenses": "2800000.00",
                "net_profit": "700000.00",
                "profit_margin": 20
            }
        ],
        "group_totals": {
            "total_revenue": "8500000.00",
            "total_expenses": "6000000.00",
            "total_net_profit": "2500000.00",
            "average_profit_margin": 29.41
        }
    }
}
```

## Common Use Cases

### Executive Dashboard

```javascript
// Fetch complete dashboard
const dashboard = await fetch(
    "/api/v1/finance/dashboard?company_id=1&fiscal_year=2081"
);

// Display KPIs in cards
displayKPIs(dashboard.kpis);

// Show revenue trends chart
displayChart(dashboard.revenue_trends);

// Show top customers table
displayTable(dashboard.top_customers);
```

### Monthly Financial Review

```javascript
// Get P&L for current month
const profitLoss = await fetch(
    "/api/v1/finance/reports/profit-loss?company_id=1&fiscal_year=2081&month=1"
);

// Get cash flow for month
const cashFlow = await fetch(
    "/api/v1/finance/reports/cash-flow?company_id=1&fiscal_year=2081&month=1"
);

// Compare with trends
const trends = await fetch(
    "/api/v1/finance/dashboard/revenue-trends?company_id=1&fiscal_year=2081"
);
```

### Year-End Closing

```javascript
// Generate balance sheet
const balanceSheet = await fetch(
    "/api/v1/finance/reports/balance-sheet?company_id=1&as_of_date=2081-12-30"
);

// Verify trial balance
const trialBalance = await fetch(
    "/api/v1/finance/reports/trial-balance?company_id=1&as_of_date=2081-12-30"
);

// Get annual expense summary
const expenseSummary = await fetch(
    "/api/v1/finance/reports/expense-summary?company_id=1&fiscal_year=2081"
);
```

### Multi-Company Analysis

```javascript
// Get consolidated report
const consolidated = await fetch(
    "/api/v1/finance/reports/consolidated?fiscal_year=2081"
);

// Compare companies
consolidated.companies.forEach((company) => {
    console.log(`${company.company}: ${company.profit_margin}% margin`);
});
```

## Validation Rules

### Required Parameters

-   **company_id**: Required for all company-specific endpoints (integer, must exist)
-   **fiscal_year**: Required for most reports (string, format: YYYY)
-   **as_of_date**: Required for balance sheet and trial balance (string, format: YYYY-MM-DD BS)
-   **month**: Optional for P&L and cash flow (integer, 1-12)

### Date Formats

-   Fiscal Year: `2081` (4-digit BS year)
-   Date: `2081-03-31` (BS format: YYYY-MM-DD)
-   Month: `1` to `12` (Baisakh to Chaitra)

## Error Handling

All endpoints return consistent error format:

```json
{
    "success": false,
    "message": "Failed to generate report",
    "error": "Company not found"
}
```

Common errors:

-   `422`: Validation error (missing/invalid parameters)
-   `404`: Resource not found (company, account)
-   `500`: Server error (calculation failure)

## Performance Tips

1. **Use specific months** for large datasets:

    ```bash
    # Better performance
    GET /reports/profit-loss?company_id=1&fiscal_year=2081&month=1

    # vs full year
    GET /reports/profit-loss?company_id=1&fiscal_year=2081
    ```

2. **Cache KPIs** for dashboard:

    ```javascript
    // Cache for 5 minutes
    const kpis = await cacheOrFetch("/dashboard/kpis", 300);
    ```

3. **Fetch dashboard data once**:

    ```bash
    # Single call for all metrics
    GET /dashboard?company_id=1&fiscal_year=2081

    # Instead of multiple calls
    GET /dashboard/kpis
    GET /dashboard/revenue-trends
    ...
    ```

## Next Steps

1. **PDF Export**: Export reports to PDF (pending)
2. **Excel Export**: Export with charts to Excel (pending)
3. **Real-time Updates**: WebSocket support for live dashboard
4. **Custom Reports**: User-defined report builder
5. **Budgeting**: Budget vs. actual comparison
6. **Forecasting**: AI-powered financial predictions
