# 🚀 Finance Module - Quick Start Guide

## 📋 Overview

This quick reference guide provides essential information for understanding and using the Finance Module for Saubhagya Group.

---

## 🏢 Company Structure Quick Reference

### Holding Company

-   **Saubhagya Group** (ID: 1) - Parent company managing all fund flows

### Sister Companies

| ID  | Company Name           | Type   | Department Count |
| --- | ---------------------- | ------ | ---------------- |
| 2   | Saubhagya Construction | Sister | 3                |
| 3   | Brand Bird             | Sister | 2                |
| 4   | Saubhagya Ghar         | Sister | 2                |
| 5   | SSIT                   | Sister | 1                |
| 6   | Your Hostel            | Sister | 1                |

---

## 📅 Nepali Fiscal Year

### Key Dates

-   **Fiscal Year Start**: Shrawan 1 (Month 4)
-   **Fiscal Year End**: Ashadh 32 (Month 3 of next year)
-   **Current FY**: 2081 (started 2081-04-01)

### Example FY Range

```
FY 2081: 2081-04-01 to 2082-03-32
FY 2082: 2082-04-01 to 2083-03-32
```

---

## 💰 Transaction Types

| Type           | Description                    | Example                | Account Impact                       |
| -------------- | ------------------------------ | ---------------------- | ------------------------------------ |
| **Income**     | Revenue/Sales                  | Client payment         | Debit: Bank, Credit: Revenue         |
| **Expense**    | Operating costs                | Rent, Salary, Supplies | Debit: Expense, Credit: Bank         |
| **Transfer**   | Inter-company/account transfer | Fund to sister company | Debit: Receivable, Credit: Bank      |
| **Investment** | Founder investment             | Capital injection      | Debit: Bank, Credit: Capital         |
| **Loan**       | Inter-company loan             | Loan to Brand Bird     | Debit: Loan Receivable, Credit: Bank |

---

## 📊 Expense Categories (from Excel Analysis)

### Primary Categories

1. **Salary** - Employee salaries (auto-generated from payroll)
2. **Operational** - Day-to-day operations
    - Rent
    - Utilities
    - Office supplies
    - Server costs
3. **Marketing** - Marketing and advertising
    - Digital ads
    - Brand promotion
    - Client entertainment
4. **Supplies** - Physical goods
    - Kitchen supplies
    - Stationery
    - Equipment
5. **Construction** - Project-specific (for Saubhagya Construction)
    - Material costs
    - Contractor payments
6. **Municipal** - Government payments
    - Taxes
    - Licenses
7. **Design** - Design services (for Brand Bird)
    - Freelancer payments
    - Software subscriptions

---

## 💳 Payment Methods

| Method        | Code            | Use Case                 |
| ------------- | --------------- | ------------------------ |
| Cash          | `cash`          | Small expenses           |
| Bank Transfer | `bank_transfer` | Salaries, large payments |
| Cheque        | `cheque`        | Vendor payments          |
| Online        | `online`        | eSewa, Khalti, etc.      |
| Cash/Online   | `cash_online`   | Mixed payment            |

---

## 👥 Key Personnel (from Excel)

### Transaction Handlers

-   **Aditya Sir** - Saubhagya Construction, Brand Bird
-   **Sagar Sir** - All companies
-   **Niraj Sir** - Brand Bird, Your Hostel
-   **Sabin Sir** - Saubhagya Construction
-   **Adilya Sir** - Various transactions

_Note: These will be mapped to users in the system_

---

## 🔄 Common Workflows

### 1. Recording a Simple Expense

```
1. Go to: Finance > Transactions > New Transaction
2. Select: Company (e.g., Brand Bird)
3. Enter: Date (BS), Amount, Category
4. Select: Payment Method, Handled By
5. Upload: Bill/Receipt (optional)
6. Click: Submit for Approval
```

### 2. Recording Founder Investment

```
1. Go to: Finance > Founders > [Select Founder]
2. Click: New Investment
3. Enter: Amount, Date (BS), Company
4. Select: Payment Method
5. Add: Description/Notes
6. Click: Submit
→ Running balance auto-calculated
```

### 3. Inter-company Loan

```
1. Go to: Finance > Inter-company Loans > New Loan
2. Select: Lender Company, Borrower Company
3. Enter: Loan Amount, Date (BS)
4. Add: Purpose, Due Date (optional)
5. Click: Create Loan
→ Outstanding balance tracked automatically
```

### 4. Recording Loan Repayment

```
1. Go to: Finance > Inter-company Loans > [Select Loan]
2. Click: Record Payment
3. Enter: Payment Amount, Date (BS)
4. Select: Payment Method
5. Click: Submit
→ Outstanding balance updated
```

### 5. Payroll Integration (Automatic)

```
When payroll is approved:
→ System auto-creates finance transaction
→ Maps department to finance company
→ Creates expense entry
→ Links to payroll record
→ No manual entry needed!
```

---

## 📈 Key Reports

### 1. Monthly Expense Summary

**Path**: Finance > Reports > Expense Summary  
**Filters**: Company, Month, Category  
**Shows**:

-   Total expenses
-   Category breakdown
-   Payment method distribution
-   Budget variance

### 2. Profit & Loss Statement

**Path**: Finance > Reports > P&L Statement  
**Filters**: Company, Fiscal Year, Month (optional)  
**Shows**:

-   Total revenue
-   Total expenses (by category)
-   Net profit/loss
-   Profit margin %

### 3. Founder Investment Summary

**Path**: Finance > Founders > Reports  
**Shows**:

-   Total investment by founder
-   Total withdrawals by founder
-   Current balance per founder
-   Company-wise breakdown

### 4. Inter-company Loan Summary

**Path**: Finance > Loans > Summary Report  
**Shows**:

-   Active loans
-   Total outstanding
-   Repayment schedule
-   Loan history

### 5. Consolidated Group Report

**Path**: Finance > Reports > Consolidated  
**Shows**:

-   All companies side-by-side
-   Group total revenue
-   Group total expenses
-   Company comparison

---

## 🎨 Dashboard Widgets

### Financial Overview (Top Row)

```
┌──────────────┬──────────────┬──────────────┬──────────────┐
│  Revenue     │  Expenses    │  Net Profit  │  Cash Balance│
│  Rs 50.2L    │  Rs 38.5L    │  Rs 11.7L    │  Rs 15.3L    │
└──────────────┴──────────────┴──────────────┴──────────────┘
```

### Monthly Trend (Chart)

-   Line graph: Revenue vs Expenses
-   Last 12 months
-   Fiscal year comparison

### Quick Stats

-   **Top 5 Expense Categories** (Current month)
-   **Recent Transactions** (Last 10)
-   **Pending Approvals** (Count)
-   **Outstanding Loans** (Total amount)

---

## ⚡ Quick Actions

### From Dashboard

-   **+ New Transaction** - Quick expense entry
-   **+ New Sale** - Record sale/invoice
-   **+ New Purchase** - Record purchase/bill
-   **View Approvals** - Pending transaction approvals

### From Transaction List

-   **Filter by Date Range** - Custom BS date range
-   **Export to Excel** - Download filtered transactions
-   **Bulk Import** - Upload CSV from Excel
-   **Search** - By description, amount, person

---

## 🔐 User Roles & Permissions

| Role                | Can Create      | Can Approve           | Can View Reports      | Can Delete    |
| ------------------- | --------------- | --------------------- | --------------------- | ------------- |
| **Super Admin**     | ✅ All          | ✅ All                | ✅ All Companies      | ✅ Yes        |
| **Finance Admin**   | ✅ All          | ✅ Assigned Companies | ✅ Assigned Companies | ✅ Draft only |
| **Accountant**      | ✅ Transactions | ❌ No                 | ✅ Assigned Companies | ✅ Own drafts |
| **Company Manager** | ✅ Own Company  | ❌ No                 | ✅ Own Company        | ❌ No         |
| **Auditor**         | ❌ No           | ❌ No                 | ✅ All (Read-only)    | ❌ No         |

---

## 📱 Excel Migration Tips

### Preparing Excel Data for Import

#### 1. Transaction Import CSV Format

```csv
company_name,date_bs,type,category,amount,credit,debit,payment_method,description,handled_by,from_holding
"Brand Bird","2081-03-15","expense","Marketing",50000,0,50000,"Online","Facebook Ads","Sagar Sir","No"
"Saubhagya Construction","2081-03-20","income","Sales",100000,100000,0,"Cheque","Client Payment","Aditya Sir","No"
```

#### 2. Founder Transaction Import CSV Format

```csv
founder_name,company_name,date_bs,type,amount,payment_method,description
"Founder 1","Saubhagya Construction","2081-03-01","investment",500000,"Bank Transfer","Initial investment"
"Founder 1","Brand Bird","2081-03-15","withdrawal",100000,"Bank Transfer","Personal loan"
```

#### 3. Inter-company Loan Import CSV Format

```csv
lender_company,borrower_company,date_bs,amount,purpose,due_date_bs
"Saubhagya Group","Brand Bird","2081-01-15",500000,"Working capital",""
"Saubhagya Group","Saubhagya Construction","2081-02-01",1000000,"Project funding",""
```

### Import Commands

```bash
# Import transactions
php artisan finance:import-transactions brand_bird.csv

# Import founder transactions
php artisan finance:import-founders founder_investments.csv

# Import loans
php artisan finance:import-loans intercompany_loans.csv
```

---

## 🎯 Best Practices

### 1. Transaction Entry

-   ✅ Always upload bill/receipt for expenses > Rs 5,000
-   ✅ Use descriptive purpose/description
-   ✅ Select correct category for accurate reporting
-   ✅ Double-check amount before submission

### 2. Approvals

-   ✅ Review document before approving
-   ✅ Verify amount matches bill
-   ✅ Check category is appropriate
-   ✅ Add notes if clarification needed

### 3. Monthly Closing

-   ✅ Ensure all transactions entered by month-end + 5 days
-   ✅ Reconcile bank statements
-   ✅ Review budget variance
-   ✅ Generate and save monthly reports

### 4. Founder Transactions

-   ✅ Always get founder approval before recording
-   ✅ Upload supporting documents
-   ✅ Reconcile balances quarterly
-   ✅ Mark as "settled" when appropriate

### 5. Inter-company Loans

-   ✅ Document loan purpose clearly
-   ✅ Set due dates for repayment tracking
-   ✅ Record payments promptly
-   ✅ Review outstanding balances monthly

---

## 🔍 Troubleshooting

### Transaction Not Showing in Report

**Check**:

-   Status is "Completed" (not Draft/Pending)
-   Date falls within report period
-   Correct fiscal year selected
-   Company filter is correct

### Balance Mismatch

**Solution**:

1. Run: Finance > Tools > Recalculate Balances
2. Check for unapproved transactions
3. Verify all transactions are completed
4. Contact admin if issue persists

### Cannot Approve Transaction

**Check**:

-   You have approval permission
-   Transaction status is "Pending"
-   Amount is within your approval limit
-   All required fields are filled

### Import Failed

**Common Issues**:

-   CSV format doesn't match template
-   Invalid date format (should be YYYY-MM-DD BS)
-   Company name doesn't exist
-   Amount format incorrect (use numbers only, no commas)

---

## 📞 Support & Training

### For Help

-   **Documentation**: `/docs/FINANCE_MODULE_MASTER_PLAN.md`
-   **Technical Spec**: `/docs/FINANCE_TECHNICAL_SPEC.md`
-   **User Manual**: (To be created in Phase 10)

### Training Resources

-   Video tutorials (To be created)
-   Step-by-step guides (In user manual)
-   FAQ section (In user manual)

---

## 🎓 Common Scenarios from Excel

Based on the uploaded Excel sheets, here are common scenarios:

### Scenario 1: Brand Bird Monthly Operations

```
Typical monthly expenses:
- Marketing: Rs 100,000 - Rs 200,000
- Salaries: Rs 90,000 - Rs 100,000
- Operational: Rs 50,000 - Rs 100,000
- Supplies: Rs 10,000 - Rs 20,000

Payment split:
- Online: ~80%
- Cheque: ~15%
- Cash: ~5%

Main handlers: Sagar Sir, Niraj Sir
```

### Scenario 2: Saubhagya Construction

```
Typical monthly expenses:
- Construction/Contractor: Rs 500,000+
- Operational: Rs 100,000 - Rs 200,000
- Municipal: Rs 10,000 - Rs 50,000
- Design: Rs 50,000 - Rs 100,000

Receives funds from: Saubhagya Group
Main handlers: Aditya Sir, Sabin Sir
```

### Scenario 3: Inter-company Loans

```
Common pattern:
- Saubhagya Group → Other companies
- Interest-free loans
- Variable repayment schedule
- Tracked per company
```

---

## ✅ Implementation Checklist

When finance module goes live, ensure:

-   [ ] All companies created and configured
-   [ ] Chart of accounts set up
-   [ ] Categories configured
-   [ ] Payment methods added
-   [ ] Users assigned to companies
-   [ ] Permissions configured
-   [ ] Historical data imported from Excel
-   [ ] Running balances verified
-   [ ] Test transactions successful
-   [ ] Reports generating correctly
-   [ ] Payroll integration working
-   [ ] Users trained on system

---

## 📊 Data Points from Excel Analysis

### Observed Patterns:

1. **Transaction Volume**: ~20-30 transactions per company per month
2. **Average Transaction Size**: Rs 10,000 - Rs 50,000
3. **Large Transactions**: Salary payments (monthly), Contractor payments (ad-hoc)
4. **Recurring Expenses**: Rent, Server costs, Subscriptions
5. **Seasonal Variation**: Higher expenses during project phases

### Key Metrics to Track:

-   Monthly expense trend
-   Category-wise distribution
-   Payment method preference
-   Handler-wise transactions
-   Budget adherence
-   Cash flow

---

**Document Version**: 1.0  
**Created**: 2082-08-27 (December 11, 2025)  
**Last Updated**: 2082-08-27  
**Status**: ✅ Complete
