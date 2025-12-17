# Finance Module Phase 2 - Quick Start Guide

## API Authentication

All finance endpoints require authentication using Laravel Sanctum.

```bash
# Login to get token
POST /api/login
{
  "email": "admin@example.com",
  "password": "password"
}

# Use token in all finance requests
Headers: {
  "Authorization": "Bearer YOUR_TOKEN_HERE",
  "Accept": "application/json"
}
```

## 1. Create Expense Transaction

```bash
POST /api/v1/finance/transactions
Headers: Authorization: Bearer {token}
{
  "company_id": 1,
  "transaction_date_bs": "2081-08-15",
  "transaction_type": "expense",
  "category_id": 5,
  "amount": 2500,
  "debit_account_id": 45,
  "credit_account_id": 12,
  "payment_method": "cash",
  "description": "Office supplies purchase"
}

# Response: Auto-approved (amount < 5000)
{
  "success": true,
  "message": "Transaction created successfully",
  "data": {
    "id": 1,
    "status": "approved",
    ...
  }
}
```

## 2. Complete Transaction

```bash
POST /api/v1/finance/transactions/1/complete
Headers: Authorization: Bearer {token}

# Response
{
  "success": true,
  "message": "Transaction completed successfully",
  "data": {
    "id": 1,
    "status": "completed",
    ...
  }
}
```

## 3. Create Reversal Transaction

```bash
POST /api/v1/finance/transactions/1/reverse
Headers: Authorization: Bearer {token}
{
  "reason": "Duplicate entry"
}

# Response: Creates opposite transaction
{
  "success": true,
  "message": "Transaction reversed successfully",
  "data": {
    "id": 2,
    "transaction_type": "expense",
    "debit_account_id": 12,  # Swapped
    "credit_account_id": 45,  # Swapped
    "description": "Reversal: Office supplies purchase",
    ...
  }
}
```

## 4. Create Sale

```bash
POST /api/v1/finance/sales
Headers: Authorization: Bearer {token}
{
  "company_id": 1,
  "sale_date_bs": "2081-08-15",
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "total_amount": 11300,
  "payment_status": "pending",
  "payment_method": "bank_transfer"
}

# Response: Auto-calculates VAT (13%)
{
  "success": true,
  "data": {
    "id": 1,
    "sale_number": "SAL-20810815-0001",
    "total_amount": "11300.00",
    "taxable_amount": "10000.00",
    "vat_amount": "1300.00",
    "net_amount": "11300.00",
    "payment_status": "pending"
  }
}
```

## 5. Record Sale Payment

```bash
POST /api/v1/finance/sales/1/payment
Headers: Authorization: Bearer {token}
{
  "amount": 11300,
  "payment_date_bs": "2081-08-20",
  "payment_method": "bank_transfer",
  "debit_account_id": 12,
  "credit_account_id": 67
}

# Response: Auto-creates transaction
{
  "success": true,
  "message": "Payment recorded successfully",
  "data": {
    "payment_status": "paid",
    "transaction": {
      "id": 3,
      "amount": "11300.00",
      "status": "approved"
    }
  }
}
```

## 6. Create Purchase with TDS

```bash
POST /api/v1/finance/purchases
Headers: Authorization: Bearer {token}
{
  "company_id": 1,
  "purchase_date_bs": "2081-08-15",
  "vendor_name": "ABC Suppliers",
  "vendor_pan": "123456789",
  "total_amount": 56500,
  "tds_percentage": 1.5,
  "payment_status": "paid",
  "payment_method": "bank_transfer",
  "debit_account_id": 45,
  "credit_account_id": 12
}

# Response: Auto-calculates VAT & TDS
{
  "success": true,
  "data": {
    "id": 1,
    "purchase_number": "PUR-20810815-0001",
    "total_amount": "56500.00",
    "taxable_amount": "50000.00",
    "vat_amount": "6500.00",
    "tds_amount": "750.00",
    "net_amount": "55750.00",
    "payment_status": "paid"
  }
}
```

## 7. Get Period Totals

```bash
GET /api/v1/finance/transactions-summary/period-totals?company_id=1&from_date=2081-04-01&to_date=2081-06-32
Headers: Authorization: Bearer {token}

# Response
{
  "success": true,
  "data": {
    "total_income": "150000.00",
    "total_expense": "75000.00",
    "total_transfer": "25000.00",
    "total_investment": "50000.00",
    "total_withdrawal": "10000.00",
    "net_income": "75000.00"
  }
}
```

## 8. Get Category Breakdown

```bash
GET /api/v1/finance/transactions-summary/category-breakdown?company_id=1&type=expense&fiscal_year=2081
Headers: Authorization: Bearer {token}

# Response
{
  "success": true,
  "data": [
    {
      "category_id": 5,
      "category_name": "Office Expenses",
      "total_amount": "45000.00",
      "transaction_count": 12
    },
    {
      "category_id": 8,
      "category_name": "Utilities",
      "total_amount": "30000.00",
      "transaction_count": 8
    }
  ]
}
```

## 9. Get Monthly Trends

```bash
GET /api/v1/finance/transactions-summary/monthly-trends?company_id=1&fiscal_year=2081
Headers: Authorization: Bearer {token}

# Response: 12 months (Shrawan to Ashadh)
{
  "success": true,
  "data": [
    {
      "month": 4,
      "month_name": "Shrawan",
      "total_income": "25000.00",
      "total_expense": "12000.00",
      "net_income": "13000.00"
    },
    {
      "month": 5,
      "month_name": "Bhadra",
      "total_income": "30000.00",
      "total_expense": "15000.00",
      "net_income": "15000.00"
    }
    // ... 10 more months
  ]
}
```

## 10. Sales Summary

```bash
GET /api/v1/finance/sales-summary/summary?company_id=1&from_date=2081-04-01&to_date=2081-06-32
Headers: Authorization: Bearer {token}

# Response
{
  "success": true,
  "data": {
    "total_sales": "500000.00",
    "total_vat": "57522.00",
    "sales_count": 25,
    "paid_sales": "450000.00",
    "pending_sales": "50000.00",
    "paid_count": 22,
    "pending_count": 3
  }
}
```

## 11. Customer Sales Analysis

```bash
GET /api/v1/finance/sales-summary/customer-sales?company_id=1&fiscal_year=2081
Headers: Authorization: Bearer {token}

# Response
{
  "success": true,
  "data": [
    {
      "customer_id": 3,
      "customer_name": "Big Corp",
      "total_sales": "150000.00",
      "total_paid": "150000.00",
      "total_pending": "0.00",
      "sales_count": 8
    },
    {
      "customer_id": 5,
      "customer_name": "Small Business",
      "total_sales": "75000.00",
      "total_paid": "50000.00",
      "total_pending": "25000.00",
      "sales_count": 5
    }
  ]
}
```

## 12. TDS Compliance Report

```bash
GET /api/v1/finance/purchases-summary/tds-summary?company_id=1&fiscal_year=2081
Headers: Authorization: Bearer {token}

# Response
{
  "success": true,
  "data": {
    "total_tds": "12500.00",
    "total_taxable": "833333.33",
    "vendor_breakdown": [
      {
        "vendor_pan": "123456789",
        "vendor_name": "ABC Suppliers",
        "total_taxable_amount": "500000.00",
        "total_tds_amount": "7500.00",
        "transaction_count": 10
      },
      {
        "vendor_pan": "987654321",
        "vendor_name": "XYZ Ltd",
        "total_taxable_amount": "333333.33",
        "total_tds_amount": "5000.00",
        "transaction_count": 5
      }
    ]
  }
}
```

## 13. Download Document

```bash
GET /api/v1/finance/transactions/1/download
Headers: Authorization: Bearer {token}

# Returns file for download
Content-Type: application/pdf
Content-Disposition: attachment; filename="document.pdf"
```

## Common Query Parameters

### Transaction List Filters

```
?company_id=1
&from_date=2081-04-01
&to_date=2081-06-32
&transaction_type=expense
&category_id=5
&status=completed
&payment_method=cash
&from_holding=true
```

### Sale/Purchase List Filters

```
?company_id=1
&fiscal_year=2081
&payment_status=pending
&customer_id=3  (or vendor_id for purchases)
&from_date=2081-04-01
&to_date=2081-06-32
```

## Transaction Types

-   `income` - Revenue/Sales
-   `expense` - Costs/Purchases
-   `transfer` - Bank transfers
-   `investment` - Capital investments
-   `withdrawal` - Owner withdrawals

## Payment Methods

-   `cash`
-   `bank_transfer`
-   `cheque`
-   `card`
-   `online`
-   `mobile_banking`

## Transaction Status Flow

```
draft → pending → approved → completed
  ↓         ↓
cancelled  cancelled
```

## Validation Rules

### Date Format

-   BS Format: `YYYY-MM-DD` (e.g., `2081-08-15`)

### Transaction Amounts

-   Minimum: 0.01
-   Auto-approval threshold: 5000

### File Upload

-   Formats: PDF, JPG, JPEG, PNG
-   Max size: 5MB

### VAT Calculation

-   Rate: 13% (Nepal standard)
-   Formula: `vat = total - (total / 1.13)`

### TDS Rates (Nepal)

-   Standard: 1.5%
-   Customizable per vendor

## Error Responses

```json
{
    "success": false,
    "message": "Failed to create transaction",
    "error": "Debit and credit accounts must be different"
}
```

## Next Steps

-   Frontend integration
-   Phase 3: Reports & Financial Statements
-   Advanced analytics
-   Budget tracking
