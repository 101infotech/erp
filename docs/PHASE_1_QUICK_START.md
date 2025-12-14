# Phase 1 Quick Start Guide

## 🚀 How to Access & Test Phase 1 Features

### Prerequisites

-   Laravel server running at `http://localhost:8000`
-   Logged in as Admin user
-   Database seeded with companies, categories, and payment methods

---

## 📍 Module Access URLs

### 1. Founder Management

**URL:** `http://localhost:8000/admin/finance/founders`

**What you can do:**

-   View all founders with their investment statistics
-   Create new founder profiles
-   Edit founder information
-   View detailed founder profile with transaction history
-   Export founder data to Excel

**Test Scenario:**

1. Click "Add Founder" button
2. Fill in the form:
    - Company: Select "Saubhagya Group"
    - Name: "John Doe"
    - Email: "john@example.com"
    - Phone: "+977-9800000000"
    - Investment Limit: "10000000" (NPR 1 Crore)
    - Check "Active"
3. Click "Create Founder"
4. You should see the founder in the list

---

### 2. Founder Transactions

**URL:** `http://localhost:8000/admin/finance/founder-transactions`

**What you can do:**

-   Record investments and withdrawals
-   Approve/Cancel/Settle transactions
-   Download transaction documents
-   View transaction history

**Test Scenario - Record Investment:**

1. Go to Founder Transactions
2. Click "Add Transaction"
3. Fill in:
    - Founder: Select the founder you created
    - Company: "Saubhagya Group"
    - Type: "Investment"
    - Amount: "500000" (NPR 5 Lakhs)
    - Fiscal Year: "2082"
    - Transaction Date (BS): "2082-08-27"
    - Payment Method: Select "Bank Transfer"
    - Reference Number: "TXN001"
    - Description: "Initial investment"
4. Click "Create Transaction"
5. Transaction will be in "Pending" status
6. Click "Approve" to approve it
7. The founder's balance will be updated

**Transaction Number Format:** `FT-2082-000001`

---

### 3. Intercompany Loans

**URL:** `http://localhost:8000/admin/finance/intercompany-loans`

**What you can do:**

-   Track loans between sister companies
-   Record loan repayments
-   Write-off bad debts
-   Monitor outstanding balances

**Test Scenario - Create Loan:**

1. Go to Intercompany Loans
2. Click "Add Loan"
3. Fill in:
    - From Company: "Saubhagya Group"
    - To Company: "Brand Bird"
    - Principal Amount: "1000000" (NPR 10 Lakhs)
    - Loan Date (BS): "2082-08-01"
    - Due Date (BS): "2083-07-30"
    - Fiscal Year: "2082"
    - Purpose: "Working capital support"
4. Click "Create Loan"
5. Click "Approve" to activate the loan
6. Click "Record Payment" to record a repayment:
    - Payment Date: "2082-09-01"
    - Amount: "200000" (NPR 2 Lakhs)
    - Payment Method: "Bank Transfer"
7. Outstanding balance will automatically update

**Loan Number Format:** `ICL-2082-000001`

---

### 4. Finance Categories

**URL:** `http://localhost:8000/admin/finance/categories`

**What you can do:**

-   View hierarchical category structure
-   Filter by company and type
-   Create parent and sub-categories
-   Manage active/inactive status

**Pre-seeded Categories:**

-   396 categories already loaded
-   Types: Income, Expense, Asset, Liability, Equity
-   Both global and company-specific categories available

**Test Scenario:**

1. Go to Categories
2. Filter by Type: "Expense"
3. You'll see categories like:
    - Operating Expenses
    - Office Rent
    - Utilities
    - Salaries
4. Click "Add Category" to create a new one

---

### 5. Payment Methods

**URL:** `http://localhost:8000/admin/finance/payment-methods`

**What you can do:**

-   Manage payment types
-   Set reference number requirements
-   Enable/disable payment methods

**Pre-seeded Payment Methods:**

-   Cash
-   Bank Transfer
-   Cheque
-   Online Payment
-   Mobile Banking
-   And 22+ more...

**Test Scenario:**

1. Go to Payment Methods
2. View existing methods
3. Click "Add Payment Method"
4. Create: "Credit Card"
    - Code: "CC"
    - Description: "Credit card payment"
    - Requires Reference: Yes
    - Active: Yes

---

## 🎯 Key Features to Test

### Auto-Number Generation

-   **Founder Transactions:** `FT-{YEAR_BS}-{SEQUENCE}`
    -   Example: `FT-2082-000001`, `FT-2082-000002`
-   **Intercompany Loans:** `ICL-{YEAR_BS}-{SEQUENCE}`
    -   Example: `ICL-2082-000001`, `ICL-2082-000002`

### Approval Workflows

**Founder Transaction Flow:**

1. Create → Status: **Pending**
2. Approve → Status: **Approved** (validates investment limit)
3. Settle → Status: **Settled** (updates founder balance)
4. Can cancel at any time → Status: **Cancelled**

**Intercompany Loan Flow:**

1. Create → Status: **Pending**
2. Approve → Status: **Active**
3. Record Payments → Updates outstanding balance
4. Fully Paid → Status: **Completed**
5. Can Write-Off → Status: **Written Off**

### Balance Calculations

**Founder Balance:**

-   Current Balance = Total Invested - Total Withdrawn
-   Investment Validation: Total Invested ≤ Investment Limit
-   Withdrawal Validation: Withdrawal Amount ≤ Current Balance

**Loan Balance:**

-   Outstanding Balance = Principal Amount - Sum(Payments)
-   Auto-updates to "Completed" when Outstanding = 0

---

## 📊 Expected Data

### Companies (10 Total)

1. **Saubhagya Group** (Holding Company)
2. Saubhagya Construction
3. Brand Bird
4. Saubhagya Ghar
5. SSIT
6. Your Hostel
7. Plus 5 more sister companies

### Categories (396 Total)

-   Income categories
-   Expense categories (Operating, Administrative, etc.)
-   Asset categories
-   Liability categories
-   Equity categories

### Payment Methods (27 Total)

-   Cash
-   Bank Transfer
-   Cheque
-   Online Payment
-   Mobile Banking
-   eSewa, Khalti, etc.

---

## 🐛 Common Issues & Solutions

### Issue: "Route not found"

**Solution:** Clear route cache

```bash
php artisan route:clear
php artisan route:cache
```

### Issue: "Class not found"

**Solution:** Regenerate autoload files

```bash
composer dump-autoload
```

### Issue: "Database connection error"

**Solution:** Check `.env` file and run

```bash
php artisan config:clear
php artisan migrate
```

### Issue: "Founder balance not updating"

**Solution:** Make sure to:

1. Approve the transaction first
2. Then settle it
3. Balance updates on "Settle" action

---

## 📈 Testing Checklist

-   [ ] Access all 5 module URLs successfully
-   [ ] Create a founder profile
-   [ ] Record an investment transaction
-   [ ] Approve the transaction
-   [ ] Settle the transaction
-   [ ] Verify founder balance updated
-   [ ] Create an intercompany loan
-   [ ] Approve the loan
-   [ ] Record a payment on the loan
-   [ ] Verify outstanding balance decreased
-   [ ] Export founder data to Excel
-   [ ] Filter categories by type
-   [ ] Create a new payment method

---

## 🎉 Success Indicators

You'll know Phase 1 is working correctly when:

1. ✅ All 5 modules load without errors
2. ✅ Founder creation saves successfully
3. ✅ Transaction numbers auto-generate (FT-2082-XXXXXX)
4. ✅ Approval workflow updates statuses
5. ✅ Balances calculate correctly
6. ✅ Loan payments update outstanding balances
7. ✅ Excel export downloads successfully
8. ✅ Filters work on all list pages

---

**Happy Testing! 🚀**

If you encounter any issues, check the Laravel logs at:
`storage/logs/laravel.log`
