# 💻 Finance Module - Technical Implementation Specification

## 📋 Table of Contents

1. [Database Migrations](#database-migrations)
2. [Model Relationships](#model-relationships)
3. [API Endpoints](#api-endpoints)
4. [Service Layer](#service-layer)
5. [Frontend Components](#frontend-components)
6. [Business Logic](#business-logic)

---

## 🗄️ Database Migrations

### Migration Order & Dependencies

```
1. finance_companies (base)
2. finance_founders (base)
3. finance_bank_accounts (depends on companies)
4. finance_accounts (depends on companies)
5. finance_categories (depends on companies)
6. finance_payment_methods (base)
7. finance_vendors (depends on companies)
8. finance_customers (depends on companies)
9. finance_transactions (depends on multiple)
10. finance_sales (depends on customers)
11. finance_purchases (depends on vendors)
12. finance_founder_transactions (depends on founders)
13. finance_intercompany_loans (depends on companies)
14. finance_intercompany_loan_payments (depends on loans)
15. finance_recurring_expenses (depends on companies)
16. finance_budgets (depends on categories)
17. hrm_departments_add_finance_company (alter existing)
```

---

## 📊 Detailed Model Structure

### 1. FinanceCompany Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceCompany extends Model
{
    protected $fillable = [
        'name',
        'type', // holding, sister
        'parent_company_id',
        'contact_email',
        'contact_phone',
        'pan_number',
        'address',
        'established_date_bs',
        'fiscal_year_start_month', // 4 = Shrawan
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fiscal_year_start_month' => 'integer',
    ];

    // Relationships
    public function parentCompany(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'parent_company_id');
    }

    public function subsidiaries(): HasMany
    {
        return $this->hasMany(FinanceCompany::class, 'parent_company_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FinanceTransaction::class, 'company_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(FinanceAccount::class, 'company_id');
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(FinanceBankAccount::class, 'company_id');
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(FinanceBudget::class, 'company_id');
    }

    public function hrmDepartments(): HasMany
    {
        return $this->hasMany(HrmDepartment::class, 'finance_company_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHolding($query)
    {
        return $query->where('type', 'holding');
    }

    public function scopeSister($query)
    {
        return $query->where('type', 'sister');
    }

    // Helper Methods
    public function getCurrentFiscalYear(): string
    {
        $calendar = app(\App\Services\NepalCalendarService::class);
        $currentBs = $calendar->getCurrentBsDate();
        [$year, $month] = explode('-', $currentBs);

        // If current month >= fiscal_year_start_month, current year
        // Otherwise, previous year
        return (int)$month >= $this->fiscal_year_start_month
            ? $year
            : (string)((int)$year - 1);
    }

    public function getFiscalYearRange(string $fiscalYear): array
    {
        $startMonth = str_pad($this->fiscal_year_start_month, 2, '0', STR_PAD_LEFT);
        $endMonth = str_pad(($this->fiscal_year_start_month - 1) ?: 12, 2, '0', STR_PAD_LEFT);
        $endYear = $endMonth < $startMonth ? (string)((int)$fiscalYear + 1) : $fiscalYear;

        return [
            'start' => "{$fiscalYear}-{$startMonth}-01",
            'end' => "{$endYear}-{$endMonth}-32", // Max days in BS month
        ];
    }

    public function getTotalRevenue(string $fiscalYear = null): float
    {
        $fiscalYear = $fiscalYear ?? $this->getCurrentFiscalYear();

        return $this->transactions()
            ->where('fiscal_year_bs', $fiscalYear)
            ->where('transaction_type', 'income')
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getTotalExpense(string $fiscalYear = null): float
    {
        $fiscalYear = $fiscalYear ?? $this->getCurrentFiscalYear();

        return $this->transactions()
            ->where('fiscal_year_bs', $fiscalYear)
            ->where('transaction_type', 'expense')
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getNetProfit(string $fiscalYear = null): float
    {
        return $this->getTotalRevenue($fiscalYear) - $this->getTotalExpense($fiscalYear);
    }
}
```

---

### 2. FinanceTransaction Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FinanceTransaction extends Model
{
    protected $fillable = [
        'company_id',
        'transaction_number',
        'transaction_date_bs',
        'transaction_type', // income, expense, transfer, investment, loan
        'category_id',
        'subcategory_id',
        'reference_type', // sale, purchase, payroll, etc.
        'reference_id',
        'description',
        'amount',
        'debit_account_id',
        'credit_account_id',
        'payment_method',
        'payment_reference',
        'handled_by_user_id',
        'received_paid_by',
        'is_from_holding_company',
        'fund_source_company_id',
        'bill_number',
        'invoice_number',
        'document_path',
        'status', // draft, pending, approved, completed, cancelled
        'approved_by_user_id',
        'approved_at',
        'fiscal_year_bs',
        'fiscal_month_bs',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_from_holding_company' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'company_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(FinanceCategory::class, 'subcategory_id');
    }

    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'debit_account_id');
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'credit_account_id');
    }

    public function handledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by_user_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function fundSourceCompany(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'fund_source_company_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeIncome($query)
    {
        return $query->where('transaction_type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('transaction_type', 'expense');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFiscalYear($query, string $fiscalYear)
    {
        return $query->where('fiscal_year_bs', $fiscalYear);
    }

    public function scopeFiscalMonth($query, int $month)
    {
        return $query->where('fiscal_month_bs', $month);
    }

    public function scopeDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('transaction_date_bs', [$startDate, $endDate]);
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (!$transaction->transaction_number) {
                $transaction->transaction_number = static::generateTransactionNumber(
                    $transaction->company_id,
                    $transaction->fiscal_year_bs
                );
            }

            if (!$transaction->fiscal_year_bs || !$transaction->fiscal_month_bs) {
                $calendar = app(\App\Services\NepalCalendarService::class);
                [$year, $month, $day] = explode('-', $transaction->transaction_date_bs);

                $transaction->fiscal_year_bs = $year;
                $transaction->fiscal_month_bs = (int)$month;
            }
        });
    }

    public static function generateTransactionNumber(int $companyId, string $fiscalYear): string
    {
        $prefix = 'FT';
        $lastTransaction = static::where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastTransaction
            ? ((int)substr($lastTransaction->transaction_number, -4) + 1)
            : 1;

        return sprintf('%s-%s-%04d', $prefix, $fiscalYear, $sequence);
    }

    public function approve(User $user): bool
    {
        $this->update([
            'status' => 'approved',
            'approved_by_user_id' => $user->id,
            'approved_at' => now(),
        ]);

        return true;
    }

    public function complete(): bool
    {
        if ($this->status !== 'approved') {
            return false;
        }

        $this->update(['status' => 'completed']);

        // Update account balances
        $this->updateAccountBalances();

        return true;
    }

    protected function updateAccountBalances(): void
    {
        // Debit account (increase for assets/expenses, decrease for liabilities/equity/revenue)
        if ($this->debitAccount) {
            $this->debitAccount->updateBalance($this->amount, 'debit');
        }

        // Credit account (opposite)
        if ($this->creditAccount) {
            $this->creditAccount->updateBalance($this->amount, 'credit');
        }
    }
}
```

---

### 3. FinanceFounderTransaction Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceFounderTransaction extends Model
{
    protected $fillable = [
        'founder_id',
        'company_id',
        'transaction_number',
        'transaction_date_bs',
        'transaction_type', // investment, withdrawal
        'amount',
        'payment_method',
        'payment_reference',
        'description',
        'running_balance',
        'is_settled',
        'settled_date_bs',
        'document_path',
        'created_by_user_id',
        'approved_by_user_id',
        'status', // pending, approved, cancelled
        'fiscal_year_bs',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'running_balance' => 'decimal:2',
        'is_settled' => 'boolean',
    ];

    // Relationships
    public function founder(): BelongsTo
    {
        return $this->belongsTo(FinanceFounder::class, 'founder_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'company_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (!$transaction->transaction_number) {
                $transaction->transaction_number = static::generateTransactionNumber(
                    $transaction->fiscal_year_bs
                );
            }

            // Calculate running balance
            $lastBalance = static::where('founder_id', $transaction->founder_id)
                ->where('company_id', $transaction->company_id)
                ->where('status', 'approved')
                ->orderBy('id', 'desc')
                ->value('running_balance') ?? 0;

            if ($transaction->transaction_type === 'investment') {
                $transaction->running_balance = $lastBalance + $transaction->amount;
            } else { // withdrawal
                $transaction->running_balance = $lastBalance - $transaction->amount;
            }
        });
    }

    public static function generateTransactionNumber(string $fiscalYear): string
    {
        $prefix = 'FI';
        $lastTransaction = static::where('fiscal_year_bs', $fiscalYear)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastTransaction
            ? ((int)substr($lastTransaction->transaction_number, -4) + 1)
            : 1;

        return sprintf('%s-%s-%04d', $prefix, $fiscalYear, $sequence);
    }

    public function getNetBalance(int $founderId, int $companyId): float
    {
        return static::where('founder_id', $founderId)
            ->where('company_id', $companyId)
            ->where('status', 'approved')
            ->orderBy('id', 'desc')
            ->value('running_balance') ?? 0;
    }
}
```

---

## 🔗 API Endpoints Structure

### Finance Companies

```
GET     /api/finance/companies              - List all companies
POST    /api/finance/companies              - Create company
GET     /api/finance/companies/{id}         - Get company details
PUT     /api/finance/companies/{id}         - Update company
DELETE  /api/finance/companies/{id}         - Delete company
GET     /api/finance/companies/{id}/summary - Financial summary
```

### Transactions

```
GET     /api/finance/transactions                      - List transactions
POST    /api/finance/transactions                      - Create transaction
GET     /api/finance/transactions/{id}                 - Get transaction
PUT     /api/finance/transactions/{id}                 - Update transaction
DELETE  /api/finance/transactions/{id}                 - Delete transaction
POST    /api/finance/transactions/{id}/approve         - Approve transaction
POST    /api/finance/transactions/{id}/complete        - Complete transaction
POST    /api/finance/transactions/bulk-import          - Bulk import from CSV
GET     /api/finance/transactions/export               - Export to Excel
```

### Sales & Purchases

```
GET     /api/finance/sales                  - List sales
POST    /api/finance/sales                  - Create sale
GET     /api/finance/sales/{id}             - Get sale
PUT     /api/finance/sales/{id}             - Update sale
DELETE  /api/finance/sales/{id}             - Delete sale
POST    /api/finance/sales/{id}/invoice     - Generate invoice PDF

GET     /api/finance/purchases              - List purchases
POST    /api/finance/purchases              - Create purchase
GET     /api/finance/purchases/{id}         - Get purchase
PUT     /api/finance/purchases/{id}         - Update purchase
DELETE  /api/finance/purchases/{id}         - Delete purchase
```

### Founder Transactions

```
GET     /api/finance/founders                           - List founders
POST    /api/finance/founders                           - Create founder
GET     /api/finance/founders/{id}                      - Get founder
PUT     /api/finance/founders/{id}                      - Update founder
GET     /api/finance/founders/{id}/transactions         - Founder transactions
POST    /api/finance/founders/{id}/investment           - Record investment
POST    /api/finance/founders/{id}/withdrawal           - Record withdrawal
GET     /api/finance/founders/{id}/balance              - Get balance
```

### Inter-company Loans

```
GET     /api/finance/intercompany-loans                 - List loans
POST    /api/finance/intercompany-loans                 - Create loan
GET     /api/finance/intercompany-loans/{id}            - Get loan details
PUT     /api/finance/intercompany-loans/{id}            - Update loan
POST    /api/finance/intercompany-loans/{id}/payment    - Record payment
GET     /api/finance/intercompany-loans/{id}/schedule   - Payment schedule
```

### Reports

```
GET     /api/finance/reports/profit-loss                - P&L Statement
GET     /api/finance/reports/balance-sheet              - Balance Sheet
GET     /api/finance/reports/cash-flow                  - Cash Flow
GET     /api/finance/reports/trial-balance              - Trial Balance
GET     /api/finance/reports/expense-summary            - Expense Summary
GET     /api/finance/reports/budget-variance            - Budget vs Actual
GET     /api/finance/reports/founder-summary            - Founder Investment Summary
GET     /api/finance/reports/intercompany-summary       - Inter-company Loan Summary
```

### Dashboard

```
GET     /api/finance/dashboard/overview                 - Dashboard data
GET     /api/finance/dashboard/company-comparison       - Multi-company comparison
GET     /api/finance/dashboard/trends                   - Monthly trends
```

---

## 🔧 Service Layer

### 1. FinanceTransactionService

```php
<?php

namespace App\Services\Finance;

use App\Models\FinanceTransaction;
use App\Models\FinanceCompany;
use App\Services\NepalCalendarService;
use Illuminate\Support\Facades\DB;

class FinanceTransactionService
{
    protected NepalCalendarService $calendar;

    public function __construct(NepalCalendarService $calendar)
    {
        $this->calendar = $calendar;
    }

    public function createTransaction(array $data, ?int $userId = null): FinanceTransaction
    {
        return DB::transaction(function () use ($data, $userId) {
            // Auto-set fiscal year and month if not provided
            if (!isset($data['fiscal_year_bs']) || !isset($data['fiscal_month_bs'])) {
                [$year, $month] = explode('-', $data['transaction_date_bs']);
                $data['fiscal_year_bs'] = $year;
                $data['fiscal_month_bs'] = (int)$month;
            }

            // Set user if provided
            if ($userId) {
                $data['handled_by_user_id'] = $userId;
            }

            $transaction = FinanceTransaction::create($data);

            // If auto-approve is enabled and amount is below threshold
            if ($this->shouldAutoApprove($transaction)) {
                $transaction->approve(auth()->user());
                $transaction->complete();
            }

            return $transaction->fresh();
        });
    }

    public function createFromPayroll(HrmPayrollRecord $payroll): FinanceTransaction
    {
        // Map department to finance company
        $department = $payroll->employee->department;
        $financeCompanyId = $department->finance_company_id ?? 1; // Default to Saubhagya Group

        return $this->createTransaction([
            'company_id' => $financeCompanyId,
            'transaction_date_bs' => $payroll->period_end_bs,
            'transaction_type' => 'expense',
            'category_id' => $this->getSalaryCategoryId(),
            'reference_type' => 'payroll',
            'reference_id' => $payroll->id,
            'description' => "Salary payment for {$payroll->employee->name}",
            'amount' => $payroll->net_payable,
            'payment_method' => 'bank_transfer',
            'debit_account_id' => $this->getSalaryExpenseAccountId(),
            'credit_account_id' => $this->getBankAccountId($financeCompanyId),
            'status' => 'approved', // Auto-approve payroll transactions
        ]);
    }

    protected function shouldAutoApprove(FinanceTransaction $transaction): bool
    {
        // Auto-approve if amount < 10,000 and type is expense
        return $transaction->transaction_type === 'expense' && $transaction->amount < 10000;
    }

    public function getMonthlyExpenseSummary(int $companyId, string $fiscalYear, int $month): array
    {
        $transactions = FinanceTransaction::where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->where('fiscal_month_bs', $month)
            ->where('transaction_type', 'expense')
            ->where('status', 'completed')
            ->with('category')
            ->get();

        $summary = [
            'total_expense' => $transactions->sum('amount'),
            'by_category' => [],
            'by_payment_method' => [],
            'transaction_count' => $transactions->count(),
        ];

        // Group by category
        $byCategory = $transactions->groupBy('category_id');
        foreach ($byCategory as $categoryId => $items) {
            $category = $items->first()->category;
            $summary['by_category'][] = [
                'category_name' => $category->name,
                'amount' => $items->sum('amount'),
                'percentage' => ($items->sum('amount') / $summary['total_expense']) * 100,
            ];
        }

        // Group by payment method
        $byPaymentMethod = $transactions->groupBy('payment_method');
        foreach ($byPaymentMethod as $method => $items) {
            $summary['by_payment_method'][] = [
                'method' => $method,
                'amount' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        }

        return $summary;
    }

    protected function getSalaryCategoryId(): int
    {
        return FinanceCategory::firstOrCreate([
            'name' => 'Salary',
            'type' => 'expense',
            'is_system' => true,
        ])->id;
    }
}
```

### 2. FinanceReportService

```php
<?php

namespace App\Services\Finance;

use App\Models\FinanceCompany;
use App\Models\FinanceTransaction;
use Illuminate\Support\Collection;

class FinanceReportService
{
    public function generateProfitLossStatement(
        int $companyId,
        string $fiscalYear,
        ?int $month = null
    ): array {
        $company = FinanceCompany::findOrFail($companyId);

        $query = FinanceTransaction::where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear)
            ->where('status', 'completed');

        if ($month) {
            $query->where('fiscal_month_bs', $month);
        }

        $transactions = $query->with('category')->get();

        $revenue = $transactions->where('transaction_type', 'income')->sum('amount');
        $expenses = $transactions->where('transaction_type', 'expense')->sum('amount');

        // Group expenses by category
        $expenseByCategory = $transactions
            ->where('transaction_type', 'expense')
            ->groupBy('category.name')
            ->map(fn($items) => $items->sum('amount'));

        return [
            'company' => $company->name,
            'fiscal_year' => $fiscalYear,
            'month' => $month,
            'revenue' => [
                'total' => $revenue,
            ],
            'expenses' => [
                'total' => $expenses,
                'by_category' => $expenseByCategory,
            ],
            'net_profit' => $revenue - $expenses,
            'profit_margin' => $revenue > 0 ? (($revenue - $expenses) / $revenue) * 100 : 0,
        ];
    }

    public function generateConsolidatedReport(string $fiscalYear): array
    {
        $companies = FinanceCompany::active()->get();
        $consolidated = [];

        foreach ($companies as $company) {
            $consolidated[] = [
                'company_id' => $company->id,
                'company_name' => $company->name,
                'revenue' => $company->getTotalRevenue($fiscalYear),
                'expense' => $company->getTotalExpense($fiscalYear),
                'net_profit' => $company->getNetProfit($fiscalYear),
            ];
        }

        $totalRevenue = collect($consolidated)->sum('revenue');
        $totalExpense = collect($consolidated)->sum('expense');

        return [
            'fiscal_year' => $fiscalYear,
            'companies' => $consolidated,
            'group_total' => [
                'revenue' => $totalRevenue,
                'expense' => $totalExpense,
                'net_profit' => $totalRevenue - $totalExpense,
            ],
        ];
    }
}
```

---

## 🎨 Frontend Component Structure

### React Components Hierarchy

```
src/
├── pages/
│   └── Finance/
│       ├── Dashboard/
│       │   ├── FinanceDashboard.jsx
│       │   ├── CompanySelector.jsx
│       │   ├── KPICards.jsx
│       │   ├── RevenueExpenseChart.jsx
│       │   └── RecentTransactions.jsx
│       ├── Transactions/
│       │   ├── TransactionList.jsx
│       │   ├── TransactionForm.jsx
│       │   ├── TransactionDetail.jsx
│       │   ├── TransactionFilters.jsx
│       │   └── BulkImport.jsx
│       ├── Sales/
│       │   ├── SalesList.jsx
│       │   ├── SaleForm.jsx
│       │   └── InvoicePreview.jsx
│       ├── Purchases/
│       │   ├── PurchasesList.jsx
│       │   ├── PurchaseForm.jsx
│       │   └── BillUpload.jsx
│       ├── Founders/
│       │   ├── FounderList.jsx
│       │   ├── FounderTransactions.jsx
│       │   ├── InvestmentForm.jsx
│       │   └── WithdrawalForm.jsx
│       ├── Loans/
│       │   ├── LoanList.jsx
│       │   ├── LoanForm.jsx
│       │   ├── LoanDetail.jsx
│       │   └── PaymentForm.jsx
│       ├── Reports/
│       │   ├── ProfitLossReport.jsx
│       │   ├── BalanceSheet.jsx
│       │   ├── ExpenseSummary.jsx
│       │   ├── BudgetVariance.jsx
│       │   └── ConsolidatedReport.jsx
│       └── Settings/
│           ├── CompanySettings.jsx
│           ├── CategoryManagement.jsx
│           ├── AccountManagement.jsx
│           └── BudgetSettings.jsx
└── components/
    └── Finance/
        ├── CategorySelector.jsx
        ├── PaymentMethodSelector.jsx
        ├── AmountInput.jsx
        ├── DocumentUploader.jsx
        ├── ApprovalButton.jsx
        └── ExportButton.jsx
```

---

## 📱 Key Frontend Features

### 1. Transaction Form Component

```jsx
import React, { useState } from "react";
import { useForm } from "react-hook-form";
import NepaliDatePicker from "@/components/NepaliDatePicker";

export default function TransactionForm({ companyId, onSuccess }) {
    const { register, handleSubmit, watch, setValue } = useForm();
    const transactionType = watch("transaction_type");

    const onSubmit = async (data) => {
        try {
            const response = await fetch("/api/finance/transactions", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ ...data, company_id: companyId }),
            });

            if (response.ok) {
                onSuccess();
            }
        } catch (error) {
            console.error("Error creating transaction:", error);
        }
    };

    return (
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
            {/* Transaction Type */}
            <div>
                <label className="block text-sm font-medium">
                    Transaction Type
                </label>
                <select
                    {...register("transaction_type")}
                    className="mt-1 block w-full"
                >
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>

            {/* Date (BS) */}
            <div>
                <label className="block text-sm font-medium">Date (BS)</label>
                <NepaliDatePicker
                    name="transaction_date_bs"
                    onChange={(date) => setValue("transaction_date_bs", date)}
                />
            </div>

            {/* Category */}
            <div>
                <label className="block text-sm font-medium">Category</label>
                <select
                    {...register("category_id")}
                    className="mt-1 block w-full"
                >
                    {/* Populate from API */}
                </select>
            </div>

            {/* Amount */}
            <div>
                <label className="block text-sm font-medium">
                    Amount (NPR)
                </label>
                <input
                    type="number"
                    step="0.01"
                    {...register("amount")}
                    className="mt-1 block w-full"
                />
            </div>

            {/* Payment Method */}
            <div>
                <label className="block text-sm font-medium">
                    Payment Method
                </label>
                <select
                    {...register("payment_method")}
                    className="mt-1 block w-full"
                >
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cheque">Cheque</option>
                    <option value="online">Online</option>
                </select>
            </div>

            {/* Description */}
            <div>
                <label className="block text-sm font-medium">Description</label>
                <textarea
                    {...register("description")}
                    className="mt-1 block w-full"
                    rows="3"
                />
            </div>

            {/* From Holding Company */}
            <div className="flex items-center">
                <input
                    type="checkbox"
                    {...register("is_from_holding_company")}
                    className="h-4 w-4"
                />
                <label className="ml-2 text-sm">From Holding Company</label>
            </div>

            {/* Document Upload */}
            <div>
                <label className="block text-sm font-medium">
                    Upload Bill/Receipt
                </label>
                <input
                    type="file"
                    {...register("document")}
                    className="mt-1 block w-full"
                />
            </div>

            {/* Submit Buttons */}
            <div className="flex space-x-3">
                <button type="button" className="btn-secondary">
                    Cancel
                </button>
                <button
                    type="submit"
                    name="status"
                    value="draft"
                    className="btn-secondary"
                >
                    Save as Draft
                </button>
                <button
                    type="submit"
                    name="status"
                    value="pending"
                    className="btn-primary"
                >
                    Submit for Approval
                </button>
            </div>
        </form>
    );
}
```

---

## 🔒 Security & Validation

### 1. Form Request Validation

```php
<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NepaliDate;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('create', FinanceTransaction::class);
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'exists:finance_companies,id'],
            'transaction_date_bs' => ['required', new NepaliDate()],
            'transaction_type' => ['required', 'in:income,expense,transfer,investment,loan'],
            'category_id' => ['required', 'exists:finance_categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string'],
            'description' => ['required', 'string', 'max:500'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB
            'is_from_holding_company' => ['boolean'],
        ];
    }
}
```

---

## 📊 Business Logic & Calculations

### 1. Fiscal Year Helper

```php
<?php

namespace App\Helpers;

use App\Services\NepalCalendarService;

class FiscalYearHelper
{
    protected NepalCalendarService $calendar;

    public function __construct()
    {
        $this->calendar = app(NepalCalendarService::class);
    }

    /**
     * Get current fiscal year based on Nepali calendar
     * Fiscal year starts from Shrawan (month 4)
     */
    public function getCurrentFiscalYear(int $startMonth = 4): string
    {
        $currentBs = $this->calendar->getCurrentBsDate();
        [$year, $month] = explode('-', $currentBs);

        // If current month >= start month, current year is fiscal year
        // Otherwise, previous year
        return (int)$month >= $startMonth ? $year : (string)((int)$year - 1);
    }

    /**
     * Get fiscal year date range
     */
    public function getFiscalYearRange(string $fiscalYear, int $startMonth = 4): array
    {
        $endMonth = $startMonth - 1;
        if ($endMonth <= 0) {
            $endMonth = 12;
        }

        $endYear = $endMonth < $startMonth
            ? (string)((int)$fiscalYear + 1)
            : $fiscalYear;

        return [
            'start' => sprintf('%s-%02d-01', $fiscalYear, $startMonth),
            'end' => sprintf('%s-%02d-32', $endYear, $endMonth), // Max 32 days
        ];
    }

    /**
     * Get list of fiscal years (last 5 years)
     */
    public function getFiscalYearsList(int $count = 5): array
    {
        $current = (int)$this->getCurrentFiscalYear();
        $years = [];

        for ($i = 0; $i < $count; $i++) {
            $year = $current - $i;
            $years[] = [
                'value' => (string)$year,
                'label' => sprintf('FY %s/%s', $year, substr($year + 1, -2)),
            ];
        }

        return $years;
    }
}
```

---

## 🎯 Next Steps

1. **Review this technical specification**
2. **Approve database schema**
3. **Begin Phase 1 migration creation**
4. **Set up model relationships**
5. **Create seed data**

---

**Document Version**: 1.0  
**Created**: 2082-08-27 (December 11, 2025)  
**Status**: 📋 Technical Specification Complete
