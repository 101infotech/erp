# Finance Phase 3: Chart of Accounts & Journal Entries - Quick Reference

## 🚀 Quick Start

### Create a Chart of Account

```php
FinanceChartOfAccount::create([
    'company_id' => 1,
    'account_code' => '1010',
    'account_name' => 'Cash in Bank',
    'account_type' => 'asset',
    'account_subtype' => 'current_asset',
    'normal_balance' => 'debit',
    'opening_balance' => 100000.00,
    'is_active' => true,
    'allow_manual_entry' => true,
    'show_in_bs' => true,
    'created_by' => auth()->id(),
]);
```

### Create a Journal Entry (Manual)

```php
// 1. Create entry
$entry = FinanceJournalEntry::create([
    'company_id' => 1,
    'entry_number' => 'JE-2081-03-00001',
    'entry_date_bs' => '2081-03-15',
    'fiscal_year_bs' => '2081',
    'fiscal_month_bs' => 3,
    'entry_type' => 'manual',
    'description' => 'Cash receipt from customer',
    'total_debit' => 10000.00,
    'total_credit' => 10000.00,
    'status' => 'draft',
]);

// 2. Add debit line
FinanceJournalEntryLine::create([
    'journal_entry_id' => $entry->id,
    'account_id' => $cashAccount->id,
    'line_number' => 1,
    'debit_amount' => 10000.00,
    'credit_amount' => 0,
]);

// 3. Add credit line
FinanceJournalEntryLine::create([
    'journal_entry_id' => $entry->id,
    'account_id' => $revenueAccount->id,
    'line_number' => 2,
    'debit_amount' => 0,
    'credit_amount' => 10000.00,
]);

// 4. Post entry (updates balances)
$entry->post(auth()->id());
```

### Generate Depreciation Entry (Automatic)

```php
use App\Services\Finance\AssetDepreciationService;

$service = new AssetDepreciationService();

$result = $service->postDepreciationEntry(
    asset: $asset,
    fiscalYear: '2081',
    fiscalMonth: 3,
    userId: auth()->id()
);
```

---

## 📊 Account Types & Subtypes

### Account Types (5)

1. **asset** - Assets (Normal Balance: Debit)
2. **liability** - Liabilities (Normal Balance: Credit)
3. **equity** - Equity (Normal Balance: Credit)
4. **revenue** - Revenue (Normal Balance: Credit)
5. **expense** - Expenses (Normal Balance: Debit)

### Account Subtypes (15+)

-   `current_asset` - Cash, accounts receivable, inventory
-   `fixed_asset` - Property, equipment, vehicles
-   `current_liability` - Accounts payable, short-term loans
-   `long_term_liability` - Long-term loans, bonds payable
-   `equity` - Owner's equity, retained earnings
-   `revenue` - Sales, service revenue
-   `cost_of_goods_sold` - Direct costs
-   `operating_expense` - Rent, salaries, utilities
-   `other_income` - Interest income
-   `other_expense` - Interest expense
-   `accumulated_depreciation` - Contra-asset account
-   `depreciation_expense` - Depreciation charges

---

## 🔄 Journal Entry Types (13)

1. **manual** - Manual journal entry
2. **asset_purchase** - Asset acquisition
3. **depreciation** - Depreciation (auto-generated)
4. **sales** - Sales transaction
5. **purchase** - Purchase transaction
6. **payroll** - Payroll entry
7. **payment** - Payment to vendor
8. **receipt** - Receipt from customer
9. **adjustment** - Adjusting entry
10. **closing** - Period closing
11. **opening** - Period opening
12. **reversal** - Reversal of posted entry

---

## 🎯 Entry Lifecycle

```
DRAFT → POST → REVERSED
  ↓       ↓
DELETE   REVERSE
```

**Draft**:

-   Can edit
-   Can delete
-   Cannot affect balances

**Posted**:

-   Cannot edit
-   Cannot delete
-   Balances updated
-   Can reverse only

**Reversed**:

-   Original entry marked
-   Reversal entry created
-   Both entries visible

---

## 🔍 Common Queries

### Get all active accounts for company

```php
$accounts = FinanceChartOfAccount::active()
    ->forCompany($companyId)
    ->orderBy('account_code')
    ->get();
```

### Get accounts by type

```php
$assets = FinanceChartOfAccount::active()
    ->forCompany($companyId)
    ->byType('asset')
    ->get();
```

### Get parent accounts only

```php
$parents = FinanceChartOfAccount::active()
    ->parentAccounts()
    ->get();
```

### Get posted entries for fiscal year

```php
$entries = FinanceJournalEntry::posted()
    ->forCompany($companyId)
    ->byFiscalYear('2081')
    ->with('lines.account')
    ->get();
```

### Get depreciation entries

```php
$depreciations = FinanceJournalEntry::posted()
    ->byType('depreciation')
    ->with('lines.account')
    ->get();
```

---

## 💡 Helper Methods

### Account Balance Update

```php
// Update account balance
$account->updateBalance(5000, 'debit');  // Increases balance for debit normal balance
$account->updateBalance(5000, 'credit'); // Decreases balance for debit normal balance
```

### Get Account Hierarchy Path

```php
$path = $account->getFullAccountPath();
// Returns: "Assets > Fixed Assets > Vehicles"
```

### Check if Entry is Balanced

```php
if ($entry->isBalanced()) {
    $entry->post(auth()->id());
}
```

### Reverse Posted Entry

```php
$reversalEntry = $entry->reverse(
    auth()->id(),
    'Duplicate entry - reversal required'
);
```

---

## 📝 Validation Rules

### Account Code

-   Required
-   Max 50 characters
-   Unique per company
-   Cannot be changed after creation

### Journal Entry

-   Must have minimum 2 lines
-   Total Debit = Total Credit (within 0.01)
-   Each line: debit OR credit (not both)
-   All accounts must be active
-   All accounts must allow manual entry (for manual entries)

### Posting Requirements

-   Entry must be balanced
-   Entry must be in 'draft' status
-   All accounts must exist

### Reversal Requirements

-   Entry must be in 'posted' status
-   Reason must be provided
-   Original entry will be marked 'reversed'

---

## 🛣️ Routes

### Chart of Accounts

```
GET    /finance/chart-of-accounts           → List all accounts
GET    /finance/chart-of-accounts/create    → Create form
POST   /finance/chart-of-accounts           → Store new account
GET    /finance/chart-of-accounts/{id}      → Show account details
GET    /finance/chart-of-accounts/{id}/edit → Edit form
PUT    /finance/chart-of-accounts/{id}      → Update account
DELETE /finance/chart-of-accounts/{id}      → Delete account
```

### Journal Entries

```
GET    /finance/journal-entries             → List all entries
GET    /finance/journal-entries/create      → Create form
POST   /finance/journal-entries             → Store new entry
GET    /finance/journal-entries/{id}        → Show entry details
GET    /finance/journal-entries/{id}/edit   → Edit form (draft only)
PUT    /finance/journal-entries/{id}        → Update entry (draft only)
DELETE /finance/journal-entries/{id}        → Delete entry (draft only)
POST   /finance/journal-entries/{id}/post   → Post entry
POST   /finance/journal-entries/{id}/reverse → Reverse posted entry
```

---

## ⚠️ Important Notes

1. **Account Deletion Protection**:

    - Cannot delete accounts with journal entries
    - Cannot delete system accounts
    - Cannot delete accounts with child accounts

2. **Entry Modification Protection**:

    - Can only edit/delete draft entries
    - Posted entries are immutable (reverse only)

3. **Balance Updates**:

    - Automatic on posting
    - Manual updates not recommended
    - Use journal entries for all balance changes

4. **Entry Numbers**:

    - Auto-generated on save
    - Format: `JE-{year}-{month}-{sequential}`
    - Example: `JE-2081-03-00001`

5. **Depreciation Accounts**:
    - Must create before batch depreciation
    - Expense account: `account_subtype = 'depreciation_expense'`
    - Accumulated account: `account_subtype = 'accumulated_depreciation'`

---

## 🔗 Related Documentation

-   Full Implementation: `/docs/FINANCE_PHASE_3_IMPLEMENTATION_COMPLETE.md`
-   Phase 1 (Loans): `/docs/FINANCE_PHASE_1_IMPLEMENTATION.md`
-   Phase 2 (Assets): `/docs/FINANCE_PHASE_2_IMPLEMENTATION.md`
-   Gap Analysis: `/docs/FINANCE_MODULE_GAP_ANALYSIS.md`

---

## 📞 Next Phase

**Phase 4: Reports & Analytics**

-   Balance Sheet
-   Profit & Loss Statement
-   Trial Balance
-   General Ledger
-   Cash Flow Statement
