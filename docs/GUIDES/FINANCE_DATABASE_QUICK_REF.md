# Finance Module - Quick Database Reference

## 📊 Database Overview

**Total Tables:** 17  
**Total Models:** 16  
**Companies:** 6 (1 holding + 5 sisters)  
**Seeded Records:** 636+

---

## 🗄️ Table Relationships Quick Reference

```
finance_companies (6 records)
├── finance_bank_accounts
├── finance_accounts (36 per company = 216 total)
├── finance_categories (66 per company = 396 total)
├── finance_payment_methods (4 per company = 24 total)
├── finance_transactions
├── finance_sales
├── finance_purchases
├── finance_budgets
└── hrm_departments

finance_founders
└── finance_founder_transactions

finance_vendors
└── finance_purchases

finance_customers
└── finance_sales

finance_intercompany_loans
└── finance_intercompany_loan_payments
```

---

## 🔑 Key Foreign Keys

| Child Table                        | Foreign Key         | References                 | On Delete |
| ---------------------------------- | ------------------- | -------------------------- | --------- |
| finance_companies                  | parent_company_id   | finance_companies          | SET NULL  |
| finance_bank_accounts              | company_id          | finance_companies          | CASCADE   |
| finance_accounts                   | company_id          | finance_companies          | CASCADE   |
| finance_accounts                   | parent_account_id   | finance_accounts           | SET NULL  |
| finance_categories                 | company_id          | finance_companies          | CASCADE   |
| finance_categories                 | parent_category_id  | finance_categories         | SET NULL  |
| finance_transactions               | company_id          | finance_companies          | CASCADE   |
| finance_transactions               | category_id         | finance_categories         | SET NULL  |
| finance_transactions               | debit_account_id    | finance_accounts           | RESTRICT  |
| finance_transactions               | credit_account_id   | finance_accounts           | RESTRICT  |
| finance_sales                      | company_id          | finance_companies          | CASCADE   |
| finance_sales                      | customer_id         | finance_customers          | SET NULL  |
| finance_purchases                  | company_id          | finance_companies          | CASCADE   |
| finance_purchases                  | vendor_id           | finance_vendors            | SET NULL  |
| finance_founder_transactions       | company_id          | finance_companies          | CASCADE   |
| finance_founder_transactions       | founder_id          | finance_founders           | CASCADE   |
| finance_intercompany_loans         | lender_company_id   | finance_companies          | RESTRICT  |
| finance_intercompany_loans         | borrower_company_id | finance_companies          | RESTRICT  |
| finance_intercompany_loan_payments | loan_id             | finance_intercompany_loans | CASCADE   |
| hrm_departments                    | finance_company_id  | finance_companies          | SET NULL  |

---

## 📋 Common Queries

### Get All Companies with Subsidiaries

```php
$holdingCompany = FinanceCompany::with('subsidiaries')
    ->where('type', 'holding')
    ->first();
```

### Get Company Transactions for Fiscal Year

```php
$transactions = FinanceTransaction::byCompany($companyId)
    ->byFiscalYear('2081')
    ->completed()
    ->get();
```

### Calculate Company Revenue

```php
$company = FinanceCompany::find($id);
$revenue = $company->getTotalRevenue('2081');
```

### Get Outstanding Vendor Payments

```php
$outstanding = FinancePurchase::byCompany($companyId)
    ->pending()
    ->sum('net_amount');
```

### Get Intercompany Loan Balance

```php
$loans = FinanceIntercompanyLoan::byBorrower($companyId)
    ->active()
    ->sum('outstanding_balance');
```

### Get Account Balance

```php
$account = FinanceAccount::find($accountId);
$balance = $account->getBalance();
```

---

## 🎯 Important Scopes

### Available on All Models

-   `active()` - Where is_active = true

### FinanceCompany

-   `holding()` - Type = holding
-   `sister()` - Type = sister

### FinanceTransaction

-   `byCompany($id)` - Filter by company
-   `byFiscalYear($year)` - Filter by fiscal year
-   `byMonth($month)` - Filter by fiscal month
-   `income()` - Income transactions
-   `expense()` - Expense transactions
-   `completed()` - Status = completed
-   `pending()` - Status = pending
-   `fromHolding()` - From holding company

### FinanceCategory

-   `income()` - Income or both
-   `expense()` - Expense or both
-   `system()` - System categories

### FinanceSale / FinancePurchase

-   `byFiscalYear($year)` - Filter by year
-   `paid()` - Payment status = paid
-   `pending()` - Payment status = pending/partial

### FinanceBudget

-   `approved()` - Status = approved/active
-   `monthly()` - Budget type = monthly
-   `quarterly()` - Budget type = quarterly
-   `annual()` - Budget type = annual

---

## 🔢 Nepali BS Date Format

**All dates stored as:** `YYYY-MM-DD` string (e.g., "2081-04-01")

**Fiscal Year:**

-   Starts: Shrawan 1 (month 4, day 1)
-   Ends: Ashadh 32 (month 3, day 32 of next year)
-   Example: FY 2081 = 2081-04-01 to 2082-03-32

**Fiscal Months:**

1. Baisakh, 2. Jestha, 3. Ashadh
2. Shrawan, 5. Bhadra, 6. Ashwin
3. Kartik, 8. Mangsir, 9. Poush
4. Magh, 11. Falgun, 12. Chaitra

---

## 💰 Account Code Ranges

| Range     | Type      | Examples                                           |
| --------- | --------- | -------------------------------------------------- |
| 1000-1999 | Asset     | Cash (1000), Bank (1100), Receivables (1200)       |
| 2000-2999 | Liability | Payables (2000), VAT (2100), Loans (2300)          |
| 3000-3999 | Equity    | Capital (3000), Retained Earnings (3100)           |
| 4000-4999 | Revenue   | Sales (4000), Services (4100), Construction (4200) |
| 5000-5999 | Expense   | Salary (5000), Marketing (5100), Rent (5200)       |

---

## 📊 Transaction Types

**Enum Values:**

-   `income` - Revenue/sales
-   `expense` - Costs/purchases
-   `transfer` - Between accounts
-   `investment` - Founder capital injection
-   `loan` - Intercompany lending
-   `withdrawal` - Founder drawings

---

## 🏢 Seeded Companies

1. **Saubhagya Group** (Holding) - PAN: 000000000
2. **Saubhagya Construction** (Sister) - PAN: 111111111
3. **Brand Bird** (Sister) - PAN: 222222222
4. **Saubhagya Ghar** (Sister) - PAN: 333333333
5. **SSIT** (Sister) - PAN: 444444444
6. **Your Hostel** (Sister) - PAN: 555555555

All fiscal years start: Shrawan 1 (month 4)

---

## ⚡ Quick Tips

### Auto-Generated Fields

-   `transaction_number` - Generated via `FinanceTransaction::generateTransactionNumber($companyId)`
-   `sale_number` - Generated via `FinanceSale::generateSaleNumber($companyId)`
-   `purchase_number` - Generated via `FinancePurchase::generatePurchaseNumber($companyId)`
-   `running_balance` - Auto-calculated on `FinanceFounderTransaction` create
-   `outstanding_balance` - Auto-updated on loan payment

### Validation Rules

-   Unique per company: `account_code`, `vendor_code`, `customer_code`
-   Unique globally: `transaction_number`, `sale_number`, `purchase_number`
-   Required for double-entry: Both `debit_account_id` AND `credit_account_id`

### Status Workflows

**Transaction:** draft → pending → approved → completed | cancelled  
**Payment:** pending → partial → paid  
**Loan:** active → partially_repaid → fully_repaid | written_off  
**Budget:** draft → approved → active

---

## 🔍 Debugging Commands

```bash
# Check table structure
php artisan tinker --execute="Schema::getColumnListing('finance_transactions')"

# Count records
php artisan tinker --execute="echo App\Models\FinanceCompany::count()"

# View sample data
php artisan tinker --execute="print_r(App\Models\FinanceCompany::first()->toArray())"

# Test relationships
php artisan tinker --execute="print_r(App\Models\FinanceCompany::with('subsidiaries')->first()->toArray())"
```

---

**Last Updated:** December 11, 2025  
**Database Version:** v1.0 (Phase 1 Complete)
